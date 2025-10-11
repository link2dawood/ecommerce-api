<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'image_path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];
    
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    // Add this accessor method
    public function getUrlAttribute()
    {
        // If image_path is null or empty, return default image
        if (empty($this->image_path)) {
            return asset('images/default-product.jpg');
        }
        
        // If image_path starts with http/https, return as is (external URL)
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        
        // Otherwise, prepend storage path for local images
        return asset('storage/' . $this->image_path);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}