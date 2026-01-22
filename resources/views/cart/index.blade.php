@extends('layouts.app')

@section('content')
<div class="p-4 pb-48 min-h-screen">
    
    <h1 class="text-2xl font-black mb-6 text-zinc-900 dark:text-white flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">shopping_cart</span>
        سلة المشتريات
    </h1>

    @if($cartItems->count() > 0)
        
        <div class="space-y-4">
            @foreach($cartItems as $item)
            <div class="flex gap-4 bg-white dark:bg-zinc-800 p-3 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-700/50">
                
                <div class="w-24 h-24 bg-zinc-100 dark:bg-zinc-900 rounded-xl overflow-hidden shrink-0">
                    {{-- التعديل: إزالة asset وعرض الصورة مباشرة --}}
                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700">
                </div>

                <div class="flex-1 flex flex-col justify-between py-1">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white line-clamp-1">{{ $item->product->name }}</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">الكمية: {{ $item->quantity }}</p>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-black text-primary">{{ $item->product->price * $item->quantity }} <span class="text-[10px] font-normal text-zinc-400">ر.ي</span></p>
                        
                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 bg-red-50 dark:bg-red-900/20 p-2 rounded-lg hover:bg-red-100 transition">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="fixed bottom-20 left-0 right-0 bg-white dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800 p-4 z-40">
            <div class="max-w-lg mx-auto">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-zinc-500 dark:text-zinc-400 font-bold">المجموع الكلي:</span>
                    <span class="text-2xl font-black text-zinc-900 dark:text-white">{{ $total }} <span class="text-sm font-normal text-zinc-500">ر.ي</span></span>
                </div>
                <a href="{{ route('checkout.index') }}" class="w-full block text-center bg-primary hover:bg-yellow-600 text-white font-bold py-4 rounded-xl transition shadow-lg transform active:scale-95">
                متابعة الشراء (تعبئة العنوان)
                </a>

    @else
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-24 h-24 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-5xl text-zinc-300">shopping_basket</span>
            </div>
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">السلة فارغة</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-6">لم تقم بإضافة أي منتجات بعد</p>
            <a href="/" class="bg-zinc-900 dark:bg-white text-white dark:text-black px-8 py-3 rounded-full font-bold hover:scale-105 transition">
                تصفح المنتجات
            </a>
        </div>
    @endif

</div>
@endsection