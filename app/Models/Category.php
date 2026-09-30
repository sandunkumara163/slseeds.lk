<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['slug', 'status'];

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }
    
    public function translation($lang = 'en')
    {
        return $this->translations()->where('language_code', $lang)->first();
    }
}