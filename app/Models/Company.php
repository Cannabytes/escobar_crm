<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_number',
        'registration_number',
        'incorporation_date',
        'expiration_date',
        'jurisdiction_zone',
        'business_activities',
        'legal_address',
        'factual_address',
        'owner_name',
        'email',
        'phone',
        'website',
    ];

    protected $casts = [
        'incorporation_date' => 'date',
        'expiration_date' => 'date',
    ];
}

