<a href="{{ route('cart.index') }}" wire:navigate class="relative flex flex-col items-center gap-1 {{ request()->routeIs('cart.*') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }}">
    <span class="material-symbols-outlined">shopping_cart</span>
    <span class="text-[10px] font-medium">السلة</span>
    
    @if($cartCount > 0)
    <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white dark:border-zinc-900 animate-bounce transition-all duration-300">
        {{ $cartCount }}
    </span>
    @endif
</a>