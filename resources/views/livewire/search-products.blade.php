<div class="relative flex-1">
    {{-- مربع البحث --}}
    <div class="relative group">
        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-zinc-400 group-focus-within:text-primary transition">search</span>
        </span>
        
        <input 
            wire:model.live.debounce.300ms="search" 
            type="text" 
            placeholder="ابحث عن منتج..." 
            class="w-full bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-full py-2.5 pl-10 pr-4 outline-none border border-zinc-200 dark:border-zinc-700 focus:border-primary focus:ring-1 focus:ring-primary transition text-sm shadow-sm"
        >
        
        {{-- أيقونة تحميل صغيرة تظهر أثناء الكتابة --}}
        <div wire:loading class="absolute inset-y-0 right-3 flex items-center">
            <span class="animate-spin h-4 w-4 border-2 border-primary border-t-transparent rounded-full"></span>
        </div>
    </div>

    {{-- القائمة المنسدلة للنتائج (تظهر فقط إذا وجد نتائج) --}}
    @if(strlen($search) >= 2)
        <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-zinc-900 rounded-xl shadow-xl border border-zinc-100 dark:border-zinc-700 z-50 overflow-hidden">
            @if(count($results) > 0)
                <ul>
                    @foreach($results as $product)
                        <li class="border-b last:border-0 border-zinc-50 dark:border-zinc-800">
                        <a href="{{ route('products.show', $product->id) }}" wire:navigate class="flex items-center gap-3 p-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                                <img src="{{ $product->image }}" class="w-10 h-10 rounded-lg object-cover" alt="">
                                <div>
                                    <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-xs text-primary font-bold">{{ $product->price }} ر.ي</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-4 text-center text-zinc-500 text-sm">
                    لا توجد نتائج لـ "{{ $search }}" 😕
                </div>
            @endif
        </div>
    @endif
</div>