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
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'address' => 'required',
        ]);

        $user = auth()->user();
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        // حفظ الطلب في قاعدة البيانات
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_phone' => $request->phone,
            'address' => $request->address,
            'total_amount' => $total,
            'status' => 'pending'
        ]);

        // حفظ التفاصيل
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        // تفريغ السلة
        Cart::where('user_id', $user->id)->delete();

        // تحويل للواتساب (رقمك)
        $myPhone = "967734464015"; 
        
        // تجهيز الرسالة
        $msg = "طلب جديد (#{$order->id}) 📦\n";
        $msg .= "👤 العميل: {$user->name}\n";
        $msg .= "📱 جوال: {$request->phone}\n";
        $msg .= "📍 العنوان: {$request->address}\n";
        $msg .= "💰 الإجمالي: {$total} ريال\n";

        $whatsappUrl = "https://wa.me/$myPhone?text=" . urlencode($msg);

        return redirect()->away($whatsappUrl);
    }
}