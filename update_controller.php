<?php
$file = 'app/Http/Controllers/Admin/ProductController.php';
$content = file_get_contents($file);

// Add ProductImage use statement
$content = str_replace('use App\Models\Product;', "use App\Models\Product;\nuse App\Models\ProductImage;", $content);

// Update store validation
$content = str_replace("'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',", "'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',\n            'image_gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',", $content);

// Update store logic
$storeLogic = <<<EOF
        \$this->syncTranslations(\$request, \$product);

        if (\$request->hasFile('image_gallery')) {
            foreach (\$request->file('image_gallery') as \$image) {
                \$path = \$image->store('products/gallery', 'public');
                \$product->images()->create(['image_path' => \$path]);
            }
        }

        return redirect()->route('admin.products.index')
EOF;
$content = preg_replace("/\\\$this->syncTranslations\(\\\$request, \\\$product\);\s*return redirect\(\)->route\('admin\.products\.index'\)/s", $storeLogic, $content);


// Update edit logic to eager load images
$content = preg_replace("/public function edit\(Product \\\$product\)\s*\{\s*\\\$categories = Category::with\('translations'\)->get\(\);/", "public function edit(Product \$product)\n    {\n        \$product->load('images');\n        \$categories = Category::with('translations')->get();", $content);


// Update update logic
$updateLogic = <<<EOF
        \$this->syncTranslations(\$request, \$product);

        if (\$request->hasFile('image_gallery')) {
            foreach (\$request->file('image_gallery') as \$image) {
                \$path = \$image->store('products/gallery', 'public');
                \$product->images()->create(['image_path' => \$path]);
            }
        }

        return redirect()->route('admin.products.index')
EOF;
$content = preg_replace("/\\\$this->syncTranslations\(\\\$request, \\\$product\);\s*return redirect\(\)->route\('admin\.products\.index'\)/s", $updateLogic, $content); // wait, it might match both, let's just do it again or use a general replace


// Add deleteImage method
$deleteMethod = <<<EOF
    public function destroy(Product \$product)
    {
        try {
            foreach (\$product->images as \$img) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists(\$img->image_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete(\$img->image_path);
                }
            }
            if (\$product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists(\$product->image)) {
EOF;
$content = str_replace("    public function destroy(Product \$product)\n    {\n        try {\n            if (\$product->image && Storage::disk('public')->exists(\$product->image)) {", $deleteMethod, $content);

$deleteImageRoute = <<<EOF
    public function deleteImage(ProductImage \$image)
    {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists(\$image->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(\$image->image_path);
        }
        \$image->delete();
        return back()->with('success', 'Image deleted successfully!');
    }
}
EOF;
$content = preg_replace('/\}\s*$/', "\n$deleteImageRoute", $content);

file_put_contents($file, $content);
echo "Done\n";
?>
