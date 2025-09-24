<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

     protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];

     protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];


     public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
