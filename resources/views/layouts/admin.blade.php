<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('adminDarkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('adminDarkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - SLSeeds.lk</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased text-gray-800 dark:text-gray-200" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transition-transform duration-300 md:static md:translate-x-0">
            <div class="flex items-center justify-between h-16 px-6 bg-gray-950 border-b border-gray-800">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold flex items-center gap-2"><img src="{{ asset('images/logo.jpg') }}" alt="SLSeeds Logo" class="h-8 w-8 rounded-full object-cover bg-white"> Admin Panel</a>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">✕</button>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3">📊</span> Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3">🌱</span> Products
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3">📁</span> Categories
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.orders.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3">📦</span> Orders
                </a>
                <a href="{{ route('admin.financial.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.financial.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3">💰</span> Financial
                </a>
                <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.customers.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3"><svg class="w-5 h-5 fill-current inline-block" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg></span> Customers
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.reviews.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3"><svg class="w-5 h-5 fill-current inline-block" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span> Reviews
                </a>
                <a href="{{ route('admin.stock.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.stock.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3">📊</span> Stock
                </a>
                <a href="{{ route('admin.chats.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.chats.*') ? 'bg-green-600' : 'hover:bg-gray-800' }} rounded-lg transition-colors">
                    <span class="mr-3">💬</span> Chats
                </a>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-white dark:bg-gray-800 shadow-sm flex items-center justify-between px-6 z-40 relative border-b border-gray-200 dark:border-gray-700">
                <button @click="sidebarOpen = true" class="md:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="hidden md:block"></div>
                <div class="flex items-center space-x-4">
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" @click.away="notifOpen = false" class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition focus:outline-none">
                            <span>🔔</span>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <!-- Dropdown panel -->
                        <div x-show="notifOpen" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50" style="display: none;">
                            <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <h3 class="font-bold text-gray-800 dark:text-gray-200">Notifications</h3>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form method="POST" action="{{ route('admin.notifications.readAll') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-green-600 hover:text-green-700 font-semibold">Mark all as read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse(auth()->user()->notifications()->limit(10)->get() as $notification)
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 {{ $notification->read_at ? 'bg-white dark:bg-gray-800' : 'bg-green-50/50 dark:bg-green-900/20' }}">
                                        <div class="flex justify-between items-start gap-2">
                                            <a href="{{ $notification->data['url'] ?? '#' }}" class="block flex-1">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 font-medium">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </a>
                                            <div class="flex flex-col gap-1 items-end">
                                                @if(!$notification->read_at)
                                                    <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                                                        @csrf
                                                        <button type="submit" title="Mark as Read" class="text-xs text-blue-500 hover:text-blue-700">✔️</button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete" class="text-xs text-red-400 hover:text-red-600">🗑️</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        No notifications yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <button @click="darkMode = !darkMode" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                        <span x-show="!darkMode">🌙</span>
                        <span x-show="darkMode" style="display: none;">☀️</span>
                    </button>
                    <a href="{{ route('home') }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Site</a>
                    <span class="text-gray-300 dark:text-gray-600">|</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                @if(session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: '{{ session('success') }}',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    </script>
                @endif
                
                @yield('content')
            </main>
        </div>
        
        <!-- Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" style="display: none;"></div>
    </div>
    
    <script type="module">
        if (window.Echo) {
            window.Echo.private('admin-notifications')
                .listen('OrderPlaced', (e) => {
                    Swal.fire({
                        title: 'New Order!',
                        text: `Order #${e.order.id} received from ${e.order.user.name}`,
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    
                    // Optional: refresh page if they are on the orders page
                    if (window.location.href.includes('/admin/orders') || window.location.href.includes('/admin/dashboard')) {
                        setTimeout(() => window.location.reload(), 2000);
                    }
                });
        }
    </script>
</body>
</html>




