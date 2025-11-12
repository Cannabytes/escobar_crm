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
}
