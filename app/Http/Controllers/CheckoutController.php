<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    // 1. عرض صفحة تعبئة البيانات
    // تحديث الدالة لتمرير المجموع ($total) ومنع الخطأ 500
    public function index()
    {
        // 1. جلب السلة مع تفاصيل المنتج
        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        // 2. إذا السلة فارغة، ارجعه للمنتجات
        if($cartItems->isEmpty()){
            return redirect()->route('products.index');
        }

        // 3. 🔥 حساب المجموع (هذا ما كان ينقص الصفحة) 🔥
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        // 4. إرسال السلة + المجموع للصفحة
        return view('checkout.index', compact('cartItems', 'total'));
    }

    // 2. حفظ الطلب -> تفريغ السلة -> تحويل للواتساب
    // نسخة الطوارئ: إرسال واتساب مباشرة وتفريغ السلة (بدون حفظ في القاعدة لتجنب الأخطاء)
    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'phone' => 'required',
            'address' => 'required',
        ]);

        $user = auth()->user();
        
        // 2. جلب السلة
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        
        if($cartItems->isEmpty()){
            return redirect()->route('products.index');
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        // 3. تجهيز رسالة الواتساب فوراً
        $msg = "طلب جديد (عاجل) 📦\n";
        $msg .= "👤 العميل: {$request->name}\n"; // نأخذ الاسم من الفورم
        $msg .= "📱 جوال: {$request->phone}\n";
        $msg .= "📍 العنوان: {$request->address}\n";
        $msg .= "💰 الإجمالي: {$total} ريال\n";
        $msg .= "------------------\n";
        $msg .= "المنتجات:\n";
        foreach ($cartItems as $item) {
            $msg .= "- {$item->product->name} (x{$item->quantity})\n";
        }

        // 4. تنظيف السلة (مهم جداً)
        Cart::where('user_id', $user->id)->delete();

        // 5. التوجيه للواتساب
        $myPhone = "967734464015"; 
        $whatsappUrl = "https://wa.me/$myPhone?text=" . urlencode($msg);

        return redirect()->away($whatsappUrl);
    }
}