<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;


       protected $fillable = [
        'code',
        'type',
        'value',
        'minimum_amount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

     protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
