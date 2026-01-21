@extends('layouts.app')

@section('content')

<div class="sticky top-0 z-20 bg-background-light/95 dark:bg-background-dark/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 p-4 pb-3">
    <div class="flex items-center justify-between gap-3">
        <form action="{{ route('products.search') }}" method="GET" class="flex-1">
            <div class="relative group">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-zinc-400 group-focus-within:text-primary transition">search</span>
                </span>
                <input type="text" name="query" placeholder="ابحث عن..." 
                       class="w-full bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-full py-2.5 pl-10 pr-4 outline-none border border-zinc-200 dark:border-zinc-700 focus:border-primary focus:ring-1 focus:ring-primary transition text-sm shadow-sm">
            </div>
        </form>
        <button class="flex items-center justify-center text-zinc-600 dark:text-zinc-300 relative w-10 h-10 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-zinc-900"></span>
        </button>
    </div>
</div>

<div class="p-4 space-y-10"> 
    <div class="relative w-full h-80 rounded-3xl overflow-hidden shadow-2xl group">
        <div class="absolute inset-0 bg-cover bg-center transition duration-1000 group-hover:scale-105"
     style="background-image: url('{{ asset('iii2.jpg') }}');">
     </div>
        <div class="absolute inset-0 bg-black/60 transition"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
            <h1 class="text-white text-4xl md:text-5xl font-black mb-4 drop-shadow-lg tracking-tight">فن الكتابة</h1>
            <p class="text-zinc-200 text-sm md:text-base mb-8 font-medium drop-shadow-md max-w-md leading-relaxed">
                اكتشف مجموعتنا الفاخرة من أدوات الخط العربي الأصيلة
            </p>
            <a href="#latest-products" class="inline-block bg-primary text-black text-sm md:text-base font-bold px-10 py-3.5 rounded-full hover:bg-yellow-400 hover:scale-105 transition transform shadow-[0_0_20px_rgba(212,175,55,0.4)]">
                تسوق الآن
            </a>
        </div>
    </div>

    <div>
        <div class="flex gap-6 overflow-x-auto no-scrollbar pb-4 justify-center items-center">
    
    @foreach([
        'ink.jpg'   => 'حبر',
        'pen.jpg'   => 'قلم',
        'paper.jpg' => 'ورق',
        'set.jpg'   => 'أطقم'
    ] as $image => $name)

    <a href="{{ route('products.search', ['query' => $name]) }}" 
       class="flex flex-col items-center gap-3 min-w-[80px] group cursor-pointer">
        
        <div class="p-[3px] rounded-full bg-gradient-to-tr from-zinc-300 to-zinc-400 dark:from-zinc-700 dark:to-zinc-600 group-hover:from-primary group-hover:to-primary transition duration-300">
            <div class="w-20 h-20 rounded-full border-[3px] border-white dark:border-zinc-900 bg-white dark:bg-zinc-800 p-2 overflow-hidden relative">
                <img src="{{ asset($image) }}" 
                     class="w-full h-full object-contain transform group-hover:scale-110 transition duration-500" 
                     alt="{{ $name }}">
            </div>
        </div>
        <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 group-hover:text-primary transition translate-y-0 group-hover:-translate-y-1">
            {{ $name }}
        </span>
    </a>
    @endforeach

</div>
    </div>

    <div id="latest-products" class="pt-2">
        <div class="flex justify-between items-end mb-6 px-2">
            <h2 class="text-xl font-black text-zinc-900 dark:text-white flex items-center gap-2">
                <span class="w-2 h-6 bg-primary rounded-full"></span>
                أحدث المنتجات
            </h2>
            <a href="#" class="text-xs text-zinc-500 hover:text-primary font-bold transition">عرض الكل</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 pb-24">
            @forelse($products as $product)
            <div class="bg-white dark:bg-zinc-800 rounded-3xl p-3 shadow-sm border border-zinc-100 dark:border-zinc-700/50 group hover:border-primary/50 transition relative flex flex-col justify-between hover:shadow-xl duration-300 h-full">
                
                <div class="relative">
                    <a href="{{ route('products.show', $product->id) }}" class="block relative aspect-square rounded-2xl overflow-hidden bg-zinc-50 dark:bg-zinc-900 mb-3">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    </a>

                    {{-- هنا كان الخطأ، تم التصحيح لاستخدام is_admin --}}
                    @if(auth()->check() && auth()->user()->is_admin)
                    <div class="absolute top-2 left-2 flex gap-2 z-10">
                        
                        <a href="{{ route('products.edit', $product->id) }}" class="w-8 h-8 flex items-center justify-center bg-white/90 dark:bg-black/80 backdrop-blur-sm rounded-full text-blue-500 hover:bg-blue-500 hover:text-white transition shadow-md" title="تعديل">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>

                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white/90 dark:bg-black/80 backdrop-blur-sm rounded-full text-red-500 hover:bg-red-500 hover:text-white transition shadow-md" title="حذف">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>

                    </div>
                    @endif
                    
                    <div class="px-1">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white truncate mb-1">{{ $product->name }}</h3>
                    </div>
                </div>

                <div class="flex justify-between items-center px-1 mt-2 border-t border-zinc-100 dark:border-zinc-700/50 pt-2">
                    <p class="text-sm font-black text-primary flex items-center gap-1">
                        {{ $product->price }} <span class="text-[10px] text-zinc-400 font-normal">ر.ي</span>
                    </p>
                    
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm hover:bg-primary hover:text-black transition">
                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                    <span class="material-symbols-outlined text-3xl text-zinc-400">inventory_2</span>
                </div>
                <p class="text-zinc-500 text-sm font-medium">المتجر فارغ حالياً</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection