<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'bank_name',
        'country',
        'company_name',
        'currency',
        'account_number',
        'iban',
        'swift',
        'sort_order',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
