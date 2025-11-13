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
}
