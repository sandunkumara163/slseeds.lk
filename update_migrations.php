<?php

$dir = __DIR__ . '/database/migrations/';
$files = scandir($dir);

$replacements = [
    'create_categories_table' => "\$table->id();\n            \$table->string('slug')->unique();\n            \$table->boolean('status')->default(true);\n            \$table->timestamps();",
    'create_category_translations_table' => "\$table->id();\n            \$table->foreignId('category_id')->constrained()->onDelete('cascade');\n            \$table->string('language_code', 2);\n            \$table->string('name');\n            \$table->timestamps();",
    'create_products_table' => "\$table->id();\n            \$table->foreignId('category_id')->constrained()->onDelete('cascade');\n            \$table->decimal('price', 10, 2);\n            \$table->decimal('discount_price', 10, 2)->nullable();\n            \$table->integer('stock_quantity')->default(0);\n            \$table->string('image')->nullable();\n            \$table->string('status')->default('active');\n            \$table->timestamps();",
    'create_product_translations_table' => "\$table->id();\n            \$table->foreignId('product_id')->constrained()->onDelete('cascade');\n            \$table->string('language_code', 2);\n            \$table->string('name');\n            \$table->text('description')->nullable();\n            \$table->timestamps();",
    'create_carts_table' => "\$table->id();\n            \$table->foreignId('user_id')->constrained()->onDelete('cascade');\n            \$table->foreignId('product_id')->constrained()->onDelete('cascade');\n            \$table->integer('quantity')->default(1);\n            \$table->timestamps();",
    'create_orders_table' => "\$table->id();\n            \$table->foreignId('user_id')->constrained()->onDelete('cascade');\n            \$table->decimal('total_amount', 10, 2);\n            \$table->text('delivery_address');\n            \$table->string('status')->default('pending');\n            \$table->string('payment_method')->default('cod');\n            \$table->timestamps();",
    'create_order_items_table' => "\$table->id();\n            \$table->foreignId('order_id')->constrained()->onDelete('cascade');\n            \$table->foreignId('product_id')->constrained();\n            \$table->integer('quantity');\n            \$table->decimal('price', 10, 2);\n            \$table->timestamps();",
    'create_notifications_table' => "\$table->id();\n            \$table->foreignId('user_id')->constrained()->onDelete('cascade');\n            \$table->string('message');\n            \$table->boolean('is_read')->default(false);\n            \$table->timestamps();",
];

foreach ($files as $file) {
    if (strpos($file, '.php') !== false) {
        foreach ($replacements as $key => $content) {
            if (strpos($file, $key) !== false) {
                $filePath = $dir . $file;
                $fileContent = file_get_contents($filePath);
                $fileContent = preg_replace('/\$table->id\(\);\n\s+\$table->timestamps\(\);/', $content, $fileContent);
                file_put_contents($filePath, $fileContent);
                echo "Updated $file\n";
            }
        }
    }
}
