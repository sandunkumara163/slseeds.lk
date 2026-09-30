<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SLSeeds.lk</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-200">
    <!-- Top Navbar -->
    <nav class="bg-green-600 dark:bg-green-900 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-bold tracking-wider flex items-center gap-2">
                <img src="{{ asset('images/logo.jpg') }}" alt="SLSeeds Logo" class="h-8 w-8 rounded-full object-cover"> SLSeeds.lk
            </a>
                </div>
                
                <!-- Search Bar -->
                <div class="hidden md:flex flex-1 mx-8">
        <form action="{{ route('products.index') }}" method="GET" class="relative w-full max-w-lg">
            <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border dark:border-gray-700 rounded-full pl-4 pr-10 py-2 focus:outline-none focus:ring-2 focus:ring-green-300" placeholder="Search products...">
            <button type="submit" class="absolute right-0 top-0 mt-2 mr-3 text-gray-500">🔍</button>
        </form>
    </div>

                <!-- Right Menu -->
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex space-x-2 relative" x-data="{ open: false }">
    <button @click="open = !open" class="bg-green-700 dark:bg-green-800 text-white border-none rounded-md px-3 py-1 text-sm focus:outline-none flex items-center gap-1">
        {{ strtoupper(app()->getLocale()) }}
        <span class="text-xs">▼</span>
    </button>
    <div x-show="open" @click.away="open = false" class="absolute top-full mt-1 right-0 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-lg rounded-md overflow-hidden z-50">
        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm hover:bg-green-50 dark:hover:bg-gray-700">English (EN)</a>
        <a href="{{ route('lang.switch', 'si') }}" class="block px-4 py-2 text-sm hover:bg-green-50 dark:hover:bg-gray-700">සිංහල (SI)</a>
        <a href="{{ route('lang.switch', 'ta') }}" class="block px-4 py-2 text-sm hover:bg-green-50 dark:hover:bg-gray-700">தமிழ் (TA)</a>
    </div>
</div>
<!-- Dark mode toggle -->
<button onclick="toggleDarkMode()" class="text-white p-1 rounded-full hover:bg-green-700 transition">
    <span class="dark:hidden">🌙</span>
    <span class="hidden dark:inline">☀️</span>
</button>
                    
                    @auth
                        <div class="hidden sm:flex items-center space-x-4">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-white hover:text-green-200">{{ __("Dashboard") }}</a>
                            @else
                                <a href="{{ route('products.index') }}" class="text-sm font-medium text-white hover:text-green-200">{{ __("Store") }}</a>
                                <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-white hover:text-green-200">{{ __("My Profile") }}</a>
                                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-white hover:text-green-200">{{ __("My Orders") }}</a>
<a href="{{ route('wishlist.index') }}" class="text-sm font-medium text-white hover:text-green-200">{{ __("My Wishlist") }}</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-red-200 hover:text-white">{{ __("Logout") }}</button>
                            </form>
                        </div>
                    @else
                        <div class="hidden sm:flex items-center space-x-3">
                            <a href="{{ route('products.index') }}" class="text-sm font-medium hover:text-green-200">{{ __("Store") }}</a>
                            <a href="{{ route('login') }}" class="text-sm font-medium hover:text-green-200">{{ __("Login") }}</a>
                            <a href="{{ route('register') }}" class="text-sm font-medium hover:text-green-200">{{ __("Register") }}</a>
                        </div>
                    @endauth
                    
                    @php
                        $cartCount = auth()->check() && auth()->user()->role === 'customer' ? \App\Models\Cart::where('user_id', auth()->id())->sum('quantity') : 0;
                    @endphp
                    
                    @if(!auth()->check() || auth()->user()->role !== 'admin')
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-white hover:text-green-200 hidden md:block">
                        🛒
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">{{ $cartCount }}</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Mobile Bottom Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 shadow-[0_-2px_10px_rgba(0,0,0,0.1)] border-t border-gray-100 dark:border-gray-800 z-50">
        <div class="flex justify-around py-2">
            <a href="{{ route('home') }}" class="flex flex-col items-center {{ request()->routeIs('home') ? 'text-green-600' : 'text-gray-500' }}">
                <span class="text-xl">🏠</span>
                <span class="text-[10px] font-medium mt-1">{{ __("Home") }}</span>
            </a>
            
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center {{ request()->routeIs('cart.index') ? 'text-green-600' : 'text-gray-500' }} hover:text-green-600 relative">
                <span class="text-xl">🛒</span>
                <span class="absolute top-0 right-2 bg-red-500 w-4 h-4 rounded-full text-white text-[9px] flex items-center justify-center">{{ $cartCount }}</span>
                <span class="text-[10px] font-medium mt-1">{{ __("Cart") }}</span>
            </a>
            
            @auth
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-gray-500' }} hover:text-green-600">
                    <span class="text-xl">📦</span>
                    <span class="text-[10px] font-medium mt-1">{{ __("Orders") }}</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center {{ request()->routeIs('profile.edit') ? 'text-green-600' : 'text-gray-500' }} hover:text-green-600">
                    <span class="text-xl">👤</span>
                    <span class="text-[10px] font-medium mt-1">{{ __("Profile") }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex flex-col items-center justify-center">
                    @csrf
                    <button type="submit" class="flex flex-col items-center text-gray-500 hover:text-red-500">
                        <span class="text-xl">🚪</span>
                        <span class="text-[10px] font-medium mt-1">{{ __("Logout") }}</span>
                    </button>
                </form>
            @else
                <a href="{{ route('products.index') }}" class="flex flex-col items-center text-gray-500 hover:text-green-600">
                    <span class="text-xl">🔍</span>
                    <span class="text-[10px] font-medium mt-1">{{ __("Shop") }}</span>
                </a>
                <a href="{{ route('login') }}" class="flex flex-col items-center {{ request()->routeIs('login') ? 'text-green-600' : 'text-gray-500' }} hover:text-green-600">
                    <span class="text-xl">🔑</span>
                    <span class="text-[10px] font-medium mt-1">{{ __("Login") }}</span>
                </a>
                <a href="{{ route('register') }}" class="flex flex-col items-center {{ request()->routeIs('register') ? 'text-green-600' : 'text-gray-500' }} hover:text-green-600">
                    <span class="text-xl">📝</span>
                    <span class="text-[10px] font-medium mt-1">{{ __("Register") }}</span>
                </a>
            @endauth
        </div>
    </div>
    <div class="h-16 md:hidden"></div>

    <script>
        window.addToCart = function(productId, quantity = 1, buyNow = false) {
            @auth
            @if(auth()->user()->role === 'admin')
                Swal.fire({ icon: 'warning', title: 'Admin Account', text: 'Admins cannot place orders.', confirmButtonColor: '#16a34a' });
                return;
            @endif
            
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: data.error, confirmButtonColor: '#16a34a' });
                } else {
                    if (buyNow) {
                        window.location.href = '{{ route('checkout.index') }}';
                    } else {
                        Swal.fire({ title: 'Added to Cart!', text: 'Item has been added successfully.', icon: 'success', toast: true, position: 'bottom-end', showConfirmButton: false, timer: 1000 });
                        setTimeout(() => window.location.reload(), 1000);
                    }
                }
            });
            @else
            window.location.href = '{{ route('login') }}';
            @endauth
        }
    </script>
    
    @if(session('success'))
        <script>
            Swal.fire({ title: 'Success!', text: '{{ session('success') }}', icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        </script>
    @endif
    
    @if(session('error'))
        <script>
            Swal.fire({ title: 'Error!', text: '{{ session('error') }}', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        </script>
    @endif
    
    @auth
    @if(auth()->user()->role === 'customer')
    <script type="module">
        if (window.Echo) {
            window.Echo.private('customer-notifications.{{ auth()->id() }}')
                .listen('OrderStatusUpdated', (e) => {
                    Swal.fire({
                        title: 'Order Status Updated!',
                        text: `Your Order #${e.order.id} is now ${e.order.status.toUpperCase()}`,
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    
                    if (window.location.href.includes('/dashboard')) {
                        setTimeout(() => window.location.reload(), 2000);
                    }
                });
        }
    </script>
    @endif
    @endauth
<script>
    function toggleDarkMode() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
    }
    // Check initial
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>
    @auth
        @if(auth()->user()->role === 'customer')
            <x-chat-widget />
        @endif
    @endauth
<script>
function toggleWishlist(productId, btn) {
    fetch('/wishlist/' + productId + '/toggle', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'added') {
            btn.classList.add('text-red-500');
            if(btn.classList.contains('border-gray-300')) btn.classList.add('border-red-200');
            btn.classList.remove('text-gray-400');
            btn.querySelector('svg').classList.add('fill-current');
        } else {
            btn.classList.remove('text-red-500');
            btn.classList.remove('border-red-200');
            btn.classList.add('text-gray-400');
            btn.querySelector('svg').classList.remove('fill-current');
        }
    });
}
</script>
@stack('scripts')
</body>
</html>






