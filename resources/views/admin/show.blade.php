@extends('layouts.app')

@section('content')
{{-- تنسيق خاص للطباعة فقط: يخفي الأزرار والقوائم عند الضغط على طباعة --}}
<style>
    @media print {
        body * {
            visibility: hidden; /* إخفاء كل شيء */
        }
        #printable-area, #printable-area * {
            visibility: visible; /* إظهار الفاتورة فقط */
        }
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        /* إخفاء الأزرار عند الطباعة */
        .no-print {
            display: none !important;
        }
    }
</style>
<div class="p-6 pb-24 min-h-screen">
    
    <a href="{{ route('admin.orders') }}" class="flex items-center gap-2 text-zinc-500 hover:text-primary mb-6 transition">
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
        <span class="text-xs font-bold">عودة للطلبات</span>
    </a>

    <div class="max-w-2xl mx-auto bg-white dark:bg-zinc-800 rounded-3xl shadow-lg border border-zinc-100 dark:border-zinc-700 overflow-hidden">
        
        <div class="bg-zinc-900 dark:bg-black p-6 flex justify-between items-center text-white">
            <div>
                <h2 class="text-xl font-black">طلب #{{ $order->id }}</h2>
                <p class="text-xs text-zinc-400 mt-1">{{ $order->created_at->format('Y-m-d h:i A') }}</p>
            </div>
            <div class="text-left">
                <div class="text-sm opacity-70">الإجمالي</div>
                <div class="text-2xl font-black text-primary">{{ $order->total_amount }} ر.ي</div>
            </div>
        </div>

        <div class="p-6 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <h3 class="text-xs font-bold text-zinc-500 uppercase mb-3">بيانات التوصيل</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="block text-[10px] text-zinc-400">الاسم</span>
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $order->customer_name }}</span>
                </div>
                <div>
                    <span class="block text-[10px] text-zinc-400">الهاتف</span>
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $order->customer_phone }}</span>
                </div>
                <div class="col-span-2">
                    <span class="block text-[10px] text-zinc-400">العنوان</span>
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $order->address }}</span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-xs font-bold text-zinc-500 uppercase mb-3">المنتجات المطلوبة</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex gap-4 items-center">
                    
                    {{-- 1. مكان الصورة: نفحص هل المنتج موجود --}}
                    <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-900 rounded-lg overflow-hidden flex items-center justify-center">
                        @if($item->product)
                            <img src="{{ $item->product->image }}" class="w-full h-full object-cover">
                        @else
                            {{-- في حال كان المنتج محذوفاً نهائياً نظهر علامة --}}
                            <span class="material-symbols-outlined text-zinc-400 text-sm">block</span>
                        @endif
                    </div>

                    <div class="flex-1">
                        {{-- 2. الاسم: نفحص هل المنتج موجود --}}
                        <h4 class="font-bold text-sm text-zinc-900 dark:text-white">
                            @if($item->product)
                                {{ $item->product->name }}
                            @else
                                <span class="text-red-500 italic">(منتج محذوف)</span>
                            @endif
                        </h4>
                        
                        <p class="text-xs text-zinc-500">{{ $item->price }} ر.ي × {{ $item->quantity }}</p>
                    </div>

                    <div class="font-bold text-zinc-900 dark:text-white">
                        {{ $item->price * $item->quantity }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="p-4 bg-zinc-50 dark:bg-black/20 border-t dark:border-zinc-700 text-center">
            <button onclick="window.print()" class="text-xs font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">print</span>
                طباعة الفاتورة
            </button>
        </div>
    </div>
</div>
@endsection