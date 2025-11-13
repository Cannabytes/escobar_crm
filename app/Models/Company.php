<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'moderator_id',
        'license_file',
        'license_number',
        'registration_number',
        'incorporation_date',
        'expiry_date',
        'free_zone',
        'business_activities',
        'legal_address',
        'actual_address',
        'owner_name',
        'owner_email',
        'owner_phone',
        'owner_website',
    ];

    protected $casts = [
        'incorporation_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Модератор компании
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    // Банковские реквизиты (старая структура)
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(CompanyBankAccount::class)->orderBy('sort_order');
    }

    // Банки (новая структура)
    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class)->orderBy('sort_order');
    }

    // Лицензии компании (множественные изображения)
    public function licenses(): HasMany
    {
        return $this->hasMany(CompanyLicense::class)->orderBy('sort_order');
    }

    // Логины и пароли
    public function credentials(): HasOne
    {
        return $this->hasOne(CompanyCredential::class);
    }

    // Пользователи с доступом к компании
    public function accessUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user_access')
            ->withPivot('id', 'access_type')
            ->withTimestamps();
    }

    // Проверка прав доступа пользователя
    public function canUserEdit(User $user): bool
    {
        // Супер админ может всё
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return true;
        }

        if ($user->hasAnyPermission(['companies.edit', 'companies.manage'])) {
            return true;
        }

        // Модератор компании может редактировать
        if ($this->moderator_id === $user->id) {
            return true;
        }

        // Проверяем дополнительных модераторов с доступом
        $hasAccess = $this->accessUsers()
            ->where('user_id', $user->id)
            ->where('access_type', 'edit')
            ->exists();

        return $hasAccess;
    }

    public function canUserView(User $user): bool
    {
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return true;
        }

        if ($user->hasAnyPermission(['companies.view', 'companies.manage'])) {
            return true;
        }

        if ($this->moderator_id === $user->id) {
            return true;
        }

        return $this->accessUsers()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function canUserViewCredentials(User $user): bool
    {
        // Супер админ может всё
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return true;
        }

        // Модератор компании может видеть логины/пароли
        if ($this->moderator_id === $user->id) {
            return true;
        }

        // Проверяем дополнительных модераторов с доступом к редактированию
        $hasAccess = $this->accessUsers()
            ->where('user_id', $user->id)
            ->whereIn('access_type', ['edit', 'view'])
            ->exists();

        if ($hasAccess) {
            return true;
        }

        return $user->hasAnyPermission([
            'company-credentials.view',
            'company-credentials.edit',
            'company-credentials.manage',
            'companies.manage',
        ]);
    }

    public function hasLicenseDetails(): bool
    {
        return collect([
            $this->license_number,
            $this->registration_number,
            $this->incorporation_date,
            $this->expiry_date,
            $this->free_zone,
            $this->business_activities,
            $this->legal_address,
            $this->actual_address,
            $this->owner_name,
            $this->owner_email,
            $this->owner_phone,
            $this->owner_website,
        ])->filter(function ($value) {
            if ($value instanceof \Carbon\CarbonInterface) {
                return true;
            }

            return filled($value);
        })->isNotEmpty();
    }
}
