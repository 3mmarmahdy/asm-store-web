@extends('layouts.app')

@section('content')
<div class="relative bg-white dark:bg-zinc-900 min-h-screen pb-48">
    
    <a href="{{ route('products.index') }}" class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/80 dark:bg-black/50 backdrop-blur text-zinc-900 dark:text-white shadow-sm hover:bg-primary transition">
        <span class="material-symbols-outlined">arrow_forward</span>
    </a>

    <div class="w-full h-[45vh] bg-zinc-100 dark:bg-zinc-800 relative">
        @if($product->image)
            <img src="{{ $product->image }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
        @endif
        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-white dark:from-zinc-900 to-transparent"></div>
    </div>

    <div class="px-6 -mt-6 relative z-10">
        <div class="flex justify-between items-start mb-2">
            <h1 class="text-2xl font-black text-zinc-900 dark:text-white leading-tight w-2/3">{{ $product->name }}</h1>
            <div class="flex flex-col items-end">
                <span class="text-xl font-black text-primary">{{ $product->price }}</span>
                <span class="text-xs text-zinc-400">ريال يمني</span>
            </div>
        </div>

        <div class="flex items-center gap-1 mb-6">
            <span class="material-symbols-outlined text-yellow-400 text-sm filled">star</span>
            <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">4.8</span>
            <span class="text-xs text-zinc-400">(120 تقييم)</span>
        </div>

        <div class="mb-8">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-2">الوصف</h3>
            <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed text-sm">
                {{ $product->description ?? 'لا يوجد وصف لهذا المنتج.' }}
            </p>
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-6 mt-6">
            @auth
        @if(Auth::user()->is_admin)
        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-6 mt-6">
            
            <h3 class="text-[10px] font-bold text-zinc-400 mb-3 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">admin_panel_settings</span>
                لوحة التحكم بالمنتج
            </h3>

            <div class="flex gap-3">
                <a href="{{ route('products.edit', $product->id) }}" class="flex-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    تعديل
                </a>

                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="flex-1" onsubmit="return confirm('هل أنت متأكد من حذف المنتج؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-red-100 dark:hover:bg-red-900/40 transition">
                        <span class="material-symbols-outlined text-sm">delete</span>
                        حذف
                    </button>
                </form>
            </div>

        </div>
        @endif
@endauth
            </div>
        </div>
    </div>

    <div class="fixed bottom-20 left-0 right-0 p-4 bg-white/95 dark:bg-zinc-900/95 backdrop-blur border-t border-zinc-100 dark:border-zinc-800 z-40 shadow-[0_-5px_20px_rgba(0,0,0,0.05)]">
        <div class="flex gap-3 max-w-lg mx-auto">
            <!-- <button class="flex-1 bg-zinc-900 dark:bg-zinc-800 text-white py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-zinc-800 transition">
                <span class="material-symbols-outlined text-sm">shopping_cart</span>
                أضف للسلة
            </button> -->
            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-zinc-900 dark:bg-zinc-800 text-white py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-zinc-800 transition">
        <span class="material-symbols-outlined text-sm">shopping_cart</span>
        أضف للسلة
                </button>
            </form>
            
            <button class="flex-1 bg-primary text-black py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-yellow-500 transition shadow-lg shadow-primary/20">
                شراء الآن
            </button>
        </div>
    </div>
</div>
@endsection