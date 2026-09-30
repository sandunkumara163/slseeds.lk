<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'vegetables' => ['en' => 'Vegetable Seeds', 'si' => 'එළවළු බීජ', 'ta' => 'காய்கறி விதைகள்'],
            'fruits' => ['en' => 'Fruit Seeds', 'si' => 'පළතුරු බීජ', 'ta' => 'பழ விதைகள்'],
            'flowers' => ['en' => 'Flower Seeds', 'si' => 'මල් බීජ', 'ta' => 'மலர் விதைகள்'],
        ];

        foreach ($categories as $slug => $langs) {
            $cat = \App\Models\Category::create(['slug' => $slug, 'status' => 1]);
            foreach ($langs as $lang => $name) {
                \App\Models\CategoryTranslation::create([
                    'category_id' => $cat->id,
                    'language_code' => $lang,
                    'name' => $name,
                ]);
            }

            // Add some products for this category
            for ($i = 1; $i <= 4; $i++) {
                $price = rand(100, 500);
                $prod = \App\Models\Product::create([
                    'category_id' => $cat->id,
                    'price' => $price,
                    'discount_price' => rand(0, 1) ? $price - 20 : null,
                    'stock_quantity' => rand(10, 100),
                    'status' => 'active',
                ]);

                \App\Models\ProductTranslation::create([
                    'product_id' => $prod->id,
                    'language_code' => 'en',
                    'name' => "Premium {$langs['en']} $i",
                    'description' => "High quality {$langs['en']} for your garden.",
                ]);
            }
        }
    }
}
