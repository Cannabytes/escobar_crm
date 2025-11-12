<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
