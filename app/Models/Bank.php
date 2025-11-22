<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'country',
        'bank_code',
        'status',
        'notes',
        'sort_order',
        'login',
        'login_id',
        'password',
        'email',
        'email_password',
        'online_banking_url',
        'manager_name',
        'manager_phone',
    ];

    protected $hidden = [
        'password',
        'email_password',
    ];

    /**
     * Компанія, якій належить банк
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Реквізити банку
     */
    public function details(): HasMany
    {
        return $this->hasMany(BankDetail::class)->orderBy('sort_order');
    }

    public function userCanManage(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $company = $this->company;

        return $company ? $company->canUserEdit($user) : false;
    }

    // Статусы банков
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_HOLD = 'hold';
    const STATUS_CLOSED = 'closed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_HOLD => __('Hold'),
            self::STATUS_CLOSED => __('Closed'),
        ];
    }

    /**
     * Получить сокращенное название банка
     */
    public function getShortName(): string
    {
        $country = strtolower(str_replace(' ', '_', $this->country ?? ''));
        $banks = config("banks.{$country}", []);
        
        return $banks[$this->name] ?? $this->name;
    }

    /**
     * Получить список банков для страны
     */
    public static function getBanksForCountry(?string $country): array
    {
        if (!$country) {
            return [];
        }

        $countryKey = strtolower(str_replace(' ', '_', $country));
        return config("banks.{$countryKey}", []);
    }
}
