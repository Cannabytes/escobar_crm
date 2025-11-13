<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyLicense extends Model
{
    protected $fillable = [
        'company_id',
        'file_path',
        'original_name',
        'sort_order',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Получить URL файла лицензии
     */
    public function getFileUrlAttribute(): string
    {
        return url('public/storage/' . $this->file_path);
    }
}
