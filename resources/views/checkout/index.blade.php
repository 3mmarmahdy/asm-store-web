@extends('layouts.app')

@section('content')
<div class="p-4 pb-24 min-h-screen">
    
    <a href="{{ route('cart.index') }}" class="flex items-center gap-2 text-zinc-500 hover:text-primary mb-6 transition">
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
        <span class="text-xs font-bold">رجوع للسلة</span>
    </a>

    <h1 class="text-2xl font-black mb-2 text-zinc-900 dark:text-white">إتمام الطلب</h1>
    <p class="text-sm text-zinc-500 mb-8">يرجى تعبئة بيانات التوصيل</p>

    {{-- بداية الفورم المصحح --}}
    <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-zinc-800 p-5 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-700">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person_pin</span>
                بيانات العميل
            </h3>
            
            <div class="space-y-4">
                {{-- 1. تعديل اسم الحقل ليصبح name --}}
                <div>
                    <label class="block text-xs font-bold mb-2 text-zinc-500">الاسم الكامل</label>
                    <input type="text" name="name" required value="{{ auth()->user()->name }}" placeholder="مثال: محمد أحمد"
                           class="w-full p-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border-none focus:ring-2 focus:ring-primary text-zinc-900 dark:text-white transition">
                </div>

                {{-- 2. تعديل اسم الحقل ليصبح phone --}}
                <div>
                    <label class="block text-xs font-bold mb-2 text-zinc-500">رقم الهاتف</label>
                    <input type="tel" name="phone" required placeholder="77xxxxxxx"
                           class="w-full p-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border-none focus:ring-2 focus:ring-primary text-zinc-900 dark:text-white transition">
                </div>

                {{-- 3. اسم الحقل address صحيح مسبقاً --}}
                <div>
                    <label class="block text-xs font-bold mb-2 text-zinc-500">العنوان بالتفصيل</label>
                    <textarea name="address" rows="2" required placeholder="المدينة - الحي - أقرب معلم"
                              class="w-full p-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border-none focus:ring-2 focus:ring-primary text-zinc-900 dark:text-white transition resize-none"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-zinc-50 dark:bg-zinc-800/50 p-5 rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4 text-sm">ملخص الفاتورة</h3>
            
            <div class="space-y-2 mb-4">
                @foreach($cartItems as $item)
                <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
                    <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                    <span>{{ $item->product->price * $item->quantity }} ر.ي</span>
                </div>
                @endforeach
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3 flex justify-between items-center">
                <span class="font-black text-zinc-900 dark:text-white">الإجمالي النهائي</span>
                <span class="font-black text-xl text-primary">{{ $total }} <span class="text-xs text-zinc-500 font-normal">ر.ي</span></span>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary text-black py-4 rounded-xl font-black text-lg hover:bg-yellow-500 transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            تأكيد الطلب
        </button>
    </form>
</div>
@endsection