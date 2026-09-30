<?php
$files = [
    'resources/views/layouts/customer.blade.php',
    'resources/views/layouts/admin.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        // Sometimes the emoji is corrupted, so we match the general structure
        $c = preg_replace(
            '/<a href="{{ route\(\'home\'\) }}" class="text-xl sm:text-2xl font-bold tracking-wider flex items-center gap-2">\s*.*?\s*SLSeeds\.lk\s*<\/a>/s',
            '<a href="{{ route(\'home\') }}" class="text-xl sm:text-2xl font-bold tracking-wider flex items-center gap-2">
                <img src="{{ asset(\'images/logo.jpg\') }}" alt="SLSeeds Logo" class="h-8 w-8 rounded-full object-cover"> SLSeeds.lk
            </a>',
            $c
        );
        // Admin layout logo might be different
        $c = preg_replace(
            '/<div class="h-16 flex items-center justify-center border-b border-gray-800">\s*<a href="{{ route\(\'admin\.dashboard\'\) }}" class="text-xl font-bold tracking-wider flex items-center gap-2 text-white">\s*.*?\s*Admin Panel\s*<\/a>\s*<\/div>/s',
            '<div class="h-16 flex items-center justify-center border-b border-gray-800">
                <a href="{{ route(\'admin.dashboard\') }}" class="text-xl font-bold tracking-wider flex items-center gap-2 text-white">
                    <img src="{{ asset(\'images/logo.jpg\') }}" alt="SLSeeds Logo" class="h-8 w-8 rounded-full object-cover"> Admin Panel
                </a>
            </div>',
            $c
        );
        file_put_contents($file, $c);
    }
}

// Receipt PDF
$pdf = 'resources/views/pdf/receipt.blade.php';
if (file_exists($pdf)) {
    $c = file_get_contents($pdf);
    $c = preg_replace(
        '/<div class="header">\s*<h1>SLSeeds\.lk<\/h1>/',
        '<div class="header">
            <img src="{{ public_path(\'images/logo.jpg\') }}" alt="SLSeeds Logo" style="height: 50px; border-radius: 50%; margin-bottom: 10px;">
            <h1>SLSeeds.lk</h1>',
        $c
    );
    file_put_contents($pdf, $c);
}
