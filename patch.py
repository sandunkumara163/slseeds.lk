import re

with open('resources/views/layouts/customer.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the select with a dropdown for language switching
lang_html = """<div class="hidden md:flex space-x-2 relative" x-data="{ open: false }">
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
"""
content = re.sub(r'<div class="hidden md:flex space-x-2">\s*<select.*?</select>\s*</div>', lang_html, content, flags=re.DOTALL)

# Add dark classes to body
content = content.replace('<body class="font-sans antialiased text-gray-900 bg-gray-50">', '<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-200">')
content = content.replace('<nav class="bg-green-600 text-white shadow-md sticky top-0 z-50">', '<nav class="bg-green-600 dark:bg-green-900 text-white shadow-md sticky top-0 z-50">')
content = content.replace('bg-white text-gray-900', 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border dark:border-gray-700')

# Ensure tailwind applies dark mode class correctly by putting it in html tag
content = content.replace('<html lang=', '<html class="light" lang=')

# Add toggleDarkMode script
script = """
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
</body>
"""
content = content.replace('</body>', script)

# Fix translations
content = content.replace('>Dashboard<', '>{{ __("Dashboard") }}<')
content = content.replace('>My Orders<', '>{{ __("My Orders") }}<')
content = content.replace('>Logout<', '>{{ __("Logout") }}<')
content = content.replace('>Login<', '>{{ __("Login") }}<')
content = content.replace('>Register<', '>{{ __("Register") }}<')
content = content.replace('>Home<', '>{{ __("Home") }}<')
content = content.replace('>Search<', '>{{ __("Search") }}<')
content = content.replace('>Cart<', '>{{ __("Cart") }}<')
content = content.replace('>Account<', '>{{ __("Account") }}<')

# Mobile dark mode
content = content.replace('bg-white shadow-[0_-2px_10px_rgba(0,0,0,0.1)] border-t border-gray-100', 'bg-white dark:bg-gray-900 shadow-[0_-2px_10px_rgba(0,0,0,0.1)] border-t border-gray-100 dark:border-gray-800')

with open('resources/views/layouts/customer.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
