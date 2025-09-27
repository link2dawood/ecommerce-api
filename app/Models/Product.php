<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Notifiable, Searchable;

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


   public function categories()
    {
    return $this->belongsToMany(Category::class, 'product_categories');
    }
    public function images()
    {
    return $this->hasMany(ProductImage::class);
    }
     public function orderItems()
    {
    return $this->hasMany(OrderItem::class);
    }
    public function reviews()
    {
    return $this->hasMany(Review::class);
    }
        public function shoppingCarts()
    {
        return $this->hasMany(ShoppingCart::class);
    }


}    
