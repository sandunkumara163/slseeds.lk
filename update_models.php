<?php
$modelsDir = __DIR__ . '/app/Models/';

$categoryContent = <<<'PHP'
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
PHP;

$productContent = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'price', 'discount_price', 'stock_quantity', 'image', 'status'];

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
}
PHP;

$catTransContent = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'language_code', 'name'];
}
PHP;

$prodTransContent = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'language_code', 'name', 'description'];
}
PHP;

file_put_contents($modelsDir . 'Category.php', $categoryContent);
file_put_contents($modelsDir . 'Product.php', $productContent);
file_put_contents($modelsDir . 'CategoryTranslation.php', $catTransContent);
file_put_contents($modelsDir . 'ProductTranslation.php', $prodTransContent);
echo "Models updated.\n";
