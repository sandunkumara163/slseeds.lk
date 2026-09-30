<?php
$file = 'app/Http/Controllers/Admin/ProductController.php';
$content = file_get_contents($file);

$search = '/public function index\(\)\s*\{\s*\$products = Product::with\(\[\'translations\', \'category\.translations\'\]\)->paginate\(15\);\s*return view\(\'admin\.products\.index\', compact\(\'products\'\)\);\s*\}/s';

$replace = <<<EOF
public function index(\Illuminate\Http\Request \$request)
    {
        \$query = Product::with(['translations', 'category.translations']);
        
        if (\$request->has('search') && \$request->search != '') {
            \$searchTerm = \$request->search;
            \$query->whereHas('translations', function(\$q) use (\$searchTerm) {
                \$q->where('name', 'like', '%' . \$searchTerm . '%');
            });
        }
        
        \$products = \$query->paginate(15)->appends(\$request->query());
        return view('admin.products.index', compact('products'));
    }
EOF;

$content = preg_replace($search, $replace, $content);
file_put_contents($file, $content);

$file2 = 'app/Http/Controllers/Admin/StockController.php';
$content2 = file_get_contents($file2);

$search2 = '/public function index\(\)\s*\{\s*\$products = Product::with\(\[\'translations\', \'category\.translations\'\]\)->paginate\(20\);\s*return view\(\'admin\.stock\.index\', compact\(\'products\'\)\);\s*\}/s';

$replace2 = <<<EOF
public function index(\Illuminate\Http\Request \$request)
    {
        \$query = Product::with(['translations', 'category.translations']);
        
        if (\$request->has('search') && \$request->search != '') {
            \$searchTerm = \$request->search;
            \$query->whereHas('translations', function(\$q) use (\$searchTerm) {
                \$q->where('name', 'like', '%' . \$searchTerm . '%');
            });
        }
        
        \$products = \$query->paginate(20)->appends(\$request->query());
        return view('admin.stock.index', compact('products'));
    }
EOF;

$content2 = preg_replace($search2, $replace2, $content2);
file_put_contents($file2, $content2);

echo "Done\n";
?>
