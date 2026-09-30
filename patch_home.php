<?php
$c = file_get_contents('resources/views/home.blade.php');
// Translations
$c = str_replace('Premium Seeds', '{{ __(\'Premium Seeds\') }}', $c);
$c = str_replace('For Your Garden', '{{ __(\'For Your Garden\') }}', $c);
$c = str_replace('Discover our wide variety', '{{ __(\'Discover our wide variety\') }}', $c);
$c = str_replace('Shop Now', '{{ __(\'Shop Now\') }}', $c);
$c = str_replace('Featured Categories', '{{ __(\'Featured Categories\') }}', $c);
$c = str_replace('Popular Products', '{{ __(\'Popular Products\') }}', $c);
$c = str_replace('>View All<', '>{{ __(\'View All\') }}<', $c);
$c = str_replace('Rs.', '{{ __(\'Rs.\') }}', $c);
$c = str_replace('>Add<', '>{{ __(\'Add\') }}<', $c);

// Dynamic translations mapping for DB items based on current locale
// We will just change $category->translation('en')->name to $category->translation(app()->getLocale())->name ?? $category->name
$c = preg_replace('/->translation\(\'en\'\)->name/', '->translation(app()->getLocale())->name', $c);

// Dark mode
$c = str_replace('bg-green-50', 'bg-green-50 dark:bg-green-900', $c);
$c = str_replace('bg-white', 'bg-white dark:bg-gray-800', $c);
$c = str_replace('text-gray-900', 'text-gray-900 dark:text-white', $c);
$c = str_replace('text-gray-800', 'text-gray-800 dark:text-gray-100', $c);
$c = str_replace('text-gray-700', 'text-gray-700 dark:text-gray-200', $c);
$c = str_replace('text-gray-600', 'text-gray-600 dark:text-gray-300', $c);
$c = str_replace('text-gray-500', 'text-gray-500 dark:text-gray-400', $c);
$c = str_replace('border-gray-100', 'border-gray-100 dark:border-gray-700', $c);

file_put_contents('resources/views/home.blade.php', $c);
