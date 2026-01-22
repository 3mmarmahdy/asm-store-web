<!DOCTYPE html>
<html class="dark" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>متجر قلم وقرطاس</title>
    
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class', 
            theme: {
                extend: {
                    colors: {
                        'background-light': '#F9FAFB',
                        'background-dark': '#0f172a',
                        'primary': '#D4AF37', 
                    },
                    fontFamily: {
                        display: ['Tajawal', 'Manrope', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/> -->
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet">

    <style>
        body { font-family: 'Tajawal', 'Manrope', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-zinc-900 dark:text-white pb-24">
    
    {{-- 1. رسالة النجاح (الخضراء) --}}
    @if(session('success'))
    <div id="toast-success" class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-zinc-900 dark:bg-white text-white dark:text-black px-6 py-3 rounded-full shadow-2xl z-[100] flex items-center gap-3 animate-bounce">
        <span class="material-symbols-outlined text-green-500 text-xl">check_circle</span>
        <span class="font-bold text-sm">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-success');
            if(toast) {
                toast.style.transition = "opacity 0.5s";
                toast.style.opacity = "0";
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000);
    </script>
    @endif

    {{-- 2. رسالة الخطأ (الحمراء) - هذا هو الجزء الجديد --}}
    @if(session('error'))
    <div id="toast-error" class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-6 py-3 rounded-full shadow-2xl z-[100] flex items-center gap-3 animate-bounce">
        <span class="material-symbols-outlined text-white text-xl">error</span>
        <span class="font-bold text-sm">{{ session('error') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-error');
            if(toast) {
                toast.style.transition = "opacity 0.5s";
                toast.style.opacity = "0";
                setTimeout(() => toast.remove(), 500);
            }
        }, 4000);
    </script>
    @endif
    
    <div class="relative flex h-auto min-h-screen w-full flex-col">
        @yield('content')
    </div>

    {{-- الشريط السفلي للتنقل --}}
    <div class="fixed bottom-0 left-0 right-0 h-20 bg-background-light/90 dark:bg-background-dark/95 backdrop-blur-lg border-t border-zinc-200 dark:border-zinc-800 z-50">
        <div class="flex justify-around items-center h-full px-4">
            
            <a href="/" class="flex flex-col items-center gap-1 {{ request()->is('/') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }}">
                <span class="material-symbols-outlined {{ request()->is('/') ? 'filled' : '' }}">home</span>
                <span class="text-[10px] font-bold">الرئيسية</span>
            </a>

            {{-- أزرار الإدارة --}}
            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('products.create') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('products.create') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }} hover:text-primary transition">
                    <span class="material-symbols-outlined">add_circle</span>
                    <span class="text-[10px] font-medium">إضافة</span>
                </a>

                <a href="{{ route('admin.orders') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('admin.orders*') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }} hover:text-primary transition">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <span class="text-[10px] font-medium">الطلبات</span>
                </a>
            @endif

            <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center gap-1 text-zinc-500 dark:text-zinc-400 hover:text-primary transition">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span class="text-[10px] font-medium">السلة</span>
                {{-- عرض العداد فقط إذا كان أكبر من صفر --}}
                @if(isset($cartCount) && $cartCount > 0)
                <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white dark:border-zinc-900 animate-bounce">
                {{ $cartCount }}
                </span>
                @endif
            </a>
            
            @auth
                <form action="{{ route('logout') }}" method="POST" class="flex flex-col items-center gap-1">
                    @csrf
                    <button type="submit" class="flex flex-col items-center gap-1 text-red-500 hover:text-red-600">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="text-[10px] font-medium">خروج</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-zinc-500 dark:text-zinc-400 hover:text-primary">
                    <span class="material-symbols-outlined">login</span>
                    <span class="text-[10px] font-medium">دخول</span>
                </a>
            @endauth

        </div>
    </div>

</body>
</html>