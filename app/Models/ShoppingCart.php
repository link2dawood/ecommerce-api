<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ShoppingCart extends Model
{
    use HasFactory, HasApiTokens, Notifiable;
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price',
    ];

        protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];


    public function product()
      {
    return $this->belongsTo(Product::class);
      }
    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
