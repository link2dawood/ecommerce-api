<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable;

     protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'min_stock_level',
        'weight',
        'dimensions',
        'status',
        'featured',
        'vendor_id',
    ];
   
     /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];


    public function category()
      {
    return $this->belongsTo(Category::class);
      }
}