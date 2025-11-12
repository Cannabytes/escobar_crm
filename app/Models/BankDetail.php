<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id',
        'detail_type',
        'detail_key',
        'detail_value',
        'currency',
        'status',
        'notes',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Банк, якому належить реквізит
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Константи для типів реквізитів
     */
    const TYPE_ACCOUNT_NUMBER = 'account_number';
    const TYPE_IBAN = 'iban';
    const TYPE_SWIFT = 'swift';

    // Статуси реквізитів
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_HOLD = 'hold';
    const STATUS_CLOSED = 'closed';

    public static function getTypes(): array
    {
        return [
            self::TYPE_ACCOUNT_NUMBER => __('Account'),
            self::TYPE_IBAN => __('Iban'),
            self::TYPE_SWIFT => __('Swift'),
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_HOLD => __('Hold'),
            self::STATUS_CLOSED => __('Closed'),
        ];
    }
}
