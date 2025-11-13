<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'operator',
        'comment',
    ];
}


