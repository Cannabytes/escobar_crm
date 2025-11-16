<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CurrencyRateService
{
    private const ENDPOINT = 'https://www.cbr-xml-daily.ru/daily_json.js';
    private const CACHE_KEY = 'system.currency_rates.cbr';
    private const CACHE_LAST_SUCCESS_KEY = 'system.currency_rates.cbr.last_successful';
    private const CACHE_TTL_SECONDS = 60; // 60 сек
    private const HTTP_TIMEOUT_SECONDS = 5;

    /**
     * Возвращает информацию о курсах валют.
     *
     * @param  array<int, string>  $codes
     * @return array{
     *     updated_at: string|null,
     *     rates: array<int, array{
     *         code: string,
     *         name: string|null,
     *         value: float|null,
     *         previous: float|null,
     *         nominal: int|null,
     *         per_unit: float|null,
     *         change: float|null
     *     }>
     * }
     */
    public function getRates(array $codes): array
    {
        $payload = $this->getPayload();

        $rates = [];

        foreach ($codes as $code) {
            $normalizedCode = strtoupper($code);
            $valute = $payload['Valute'][$normalizedCode] ?? null;

            if (! is_array($valute)) {
                $rates[] = [
                    'code' => $normalizedCode,
                    'name' => null,
                    'value' => null,
                    'previous' => null,
                    'nominal' => null,
                    'per_unit' => null,
                    'change' => null,
                ];

                continue;
            }

            $value = isset($valute['Value']) ? (float) $valute['Value'] : null;
            $previous = isset($valute['Previous']) ? (float) $valute['Previous'] : null;
            $nominal = isset($valute['Nominal']) ? (int) $valute['Nominal'] : null;
            $perUnit = ($value !== null && $nominal) ? $value / max($nominal, 1) : null;

            $rates[] = [
                'code' => $normalizedCode,
                'name' => $valute['Name'] ?? null,
                'value' => $value,
                'previous' => $previous,
                'nominal' => $nominal,
                'per_unit' => $perUnit,
                'change' => ($value !== null && $previous !== null) ? $value - $previous : null,
            ];
        }

        // Используем время кеширования для отображения, если есть, иначе время из API
        $updatedAt = null;
        if (isset($payload['_cached_at'])) {
            try {
                $updatedAt = Carbon::parse($payload['_cached_at']);
            } catch (\Throwable) {
                $updatedAt = $this->resolveUpdatedAt($payload);
            }
        } else {
            $updatedAt = $this->resolveUpdatedAt($payload);
        }

        return [
            'updated_at' => $updatedAt,
            'rates' => $rates,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getPayload(): array
    {
        $payload = Cache::get(self::CACHE_KEY);

        // Проверяем возраст данных, если они есть в кеше
        if (is_array($payload)) {
            // Проверяем время кеширования (когда МЫ закешировали данные)
            $cachedAt = null;
            if (isset($payload['_cached_at'])) {
                try {
                    $cachedAt = Carbon::parse($payload['_cached_at']);
                } catch (\Throwable) {
                    $cachedAt = null;
                }
            }
            
            // Если нет метки времени кеширования или данные старше TTL, обновляем
            if (! $cachedAt || now()->diffInSeconds($cachedAt) > self::CACHE_TTL_SECONDS) {
                // Кеш устарел или нет метки времени, обновляем данные
                $data = $this->downloadPayload();
                
                if (! empty($data)) {
                    $this->storePayload($data);
                    return $data;
                }
                
                // Если не удалось обновить, возвращаем старые данные
                return $payload;
            }
            
            return $payload;
        }

        $data = $this->downloadPayload();

        if (! empty($data)) {
            $this->storePayload($data);

            return $data;
        }

        $fallback = Cache::get(self::CACHE_LAST_SUCCESS_KEY);

        return is_array($fallback) ? $fallback : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function downloadPayload(): array
    {
        $payload = $this->performRequest();

        if ($payload !== null) {
            return $payload;
        }

        $payload = $this->performRequest(['verify' => false]);

        if ($payload !== null) {
            return $payload;
        }

        $payload = $this->downloadViaStreamContext();

        return $payload ?? [];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function storePayload(array $data): void
    {
        // Добавляем метку времени кеширования
        $data['_cached_at'] = now()->toIso8601String();
        
        Cache::put(self::CACHE_KEY, $data, self::CACHE_TTL_SECONDS);
        
        // Для fallback тоже добавляем метку времени
        $fallbackData = $data;
        $fallbackData['_cached_at'] = now()->toIso8601String();
        Cache::forever(self::CACHE_LAST_SUCCESS_KEY, $fallbackData);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>|null
     */
    private function performRequest(array $options = []): ?array
    {
        try {
            $client = Http::timeout(self::HTTP_TIMEOUT_SECONDS)
                ->retry(2, 200)
                ->acceptJson();

            if (! empty($options)) {
                $client = $client->withOptions($options);
            }

            $response = $client->get(self::ENDPOINT);

            if ($response->successful()) {
                $json = $response->json();

                return $this->isValidPayload($json) ? $json : null;
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function downloadViaStreamContext(): ?array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => self::HTTP_TIMEOUT_SECONDS,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        try {
            $contents = file_get_contents(self::ENDPOINT, false, $context);

            if ($contents === false || ! Str::isJson($contents)) {
                return null;
            }

            $json = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

            return $this->isValidPayload($json) ? $json : null;
        } catch (\Throwable $exception) {
            report($exception);
        }

        return null;
    }

    /**
     * @param  mixed  $payload
     */
    private function isValidPayload(mixed $payload): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        if (! isset($payload['Valute']) || ! is_array($payload['Valute'])) {
            return false;
        }

        return count($payload['Valute']) > 0;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function resolveUpdatedAt(array $payload): ?Carbon
    {
        $timestamp = $payload['Timestamp'] ?? $payload['Date'] ?? null;

        if (! is_string($timestamp) || $timestamp === '') {
            return null;
        }

        try {
            return Carbon::parse($timestamp)->setTimezone(config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }
    }
}


