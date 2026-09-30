<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'price', 'discount_price', 'stock_quantity', 'image', 'status'];

    public function images() { return $this->hasMany(ProductImage::class); }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }
    
    public function translation($lang = 'en')
    {
        return $this->translations()->where('language_code', $lang)->first();
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->avg('rating') ?: 0;
        }
        return $this->reviews()->avg('rating') ?: 0;
    }
}
