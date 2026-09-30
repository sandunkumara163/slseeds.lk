<?php
$c = file_get_contents('resources/views/products/index.blade.php');
// Translations
$c = str_replace('Search Results for', '{{ __(\'Search Results for\') }}', $c);
$c = str_replace('All Products', '{{ __(\'All Products\') }}', $c);
$c = str_replace('Default Sorting', '{{ __(\'Default Sorting\') }}', $c);
$c = str_replace('Price: Low to High', '{{ __(\'Price: Low to High\') }}', $c);
$c = str_replace('Price: High to Low', '{{ __(\'Price: High to Low\') }}', $c);
$c = str_replace('>All Categories<', '>{{ __(\'All Categories\') }}<', $c);
$c = str_replace('No products found', '{{ __(\'No products found\') }}', $c);
$c = str_replace('Clear Filters', '{{ __(\'Clear Filters\') }}', $c);
$c = str_replace('Rs.', '{{ __(\'Rs.\') }}', $c);
$c = str_replace('> Add', '> {{ __(\'Add\') }}', $c);

// Dynamic translations mapping for DB items based on current locale
$c = preg_replace('/->translation\(\'en\'\)->name/', '->translation(app()->getLocale())->name', $c);

// Dark mode
$c = str_replace('bg-white', 'bg-white dark:bg-gray-800', $c);
$c = str_replace('text-gray-900', 'text-gray-900 dark:text-white', $c);
$c = str_replace('text-gray-800', 'text-gray-800 dark:text-gray-100', $c);
$c = str_replace('text-gray-700', 'text-gray-700 dark:text-gray-200', $c);
$c = str_replace('text-gray-600', 'text-gray-600 dark:text-gray-300', $c);
$c = str_replace('text-gray-500', 'text-gray-500 dark:text-gray-400', $c);
$c = str_replace('border-gray-100', 'border-gray-100 dark:border-gray-700', $c);
$c = str_replace('border-gray-200', 'border-gray-200 dark:border-gray-600', $c);
$c = str_replace('bg-gray-50', 'bg-gray-50 dark:bg-gray-900', $c);

file_put_contents('resources/views/products/index.blade.php', $c);
