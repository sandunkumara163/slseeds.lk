<?php
$c=file_get_contents('resources/views/layouts/admin.blade.php');
$c=str_replace('<span class="text-lg font-bold">Admin Panel</span>', '<a href="{{ route(\'admin.dashboard\') }}" class="text-lg font-bold flex items-center gap-2"><img src="{{ asset(\'images/logo.jpg\') }}" alt="SLSeeds Logo" class="h-8 w-8 rounded-full object-cover bg-white"> Admin Panel</a>', $c);
file_put_contents('resources/views/layouts/admin.blade.php', $c);
