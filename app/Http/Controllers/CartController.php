<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // 1. عرض صفحة السلة (مع إصلاح مشكلة المنتجات المحذوفة)
    public function index()
    {
        // جلب السلة حسب نوع المستخدم
        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();
        } else {
            $cartItems = Cart::where('session_id', Session::getId())->with('product')->get();
        }

        // ✨ التعديل الجديد: تنظيف السلة تلقائياً ✨
        // نحذف أي عنصر في السلة لم يعد منتجه موجوداً في قاعدة البيانات
        $cartItems = $cartItems->filter(function ($item) {
            if (!$item->product) {
                $item->delete(); // حذف السطر من قاعدة بيانات السلة
                return false;    // استبعاده من القائمة الحالية
            }
            return true;
        });

        // حساب الإجمالي (الآن نضمن أن product موجود دائماً ولا يسبب خطأ)
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    // 2. إضافة منتج للسلة
    // نسخة دالة الإضافة مع كاشف الأخطاء
    // لاحظ الاسم أصبح add ليطابق web.php
    public function add($productId)
    {
        $sessionId = \Illuminate\Support\Facades\Session::getId();
        $userId = auth()->id();

        // 1. فحص هل المنتج موجود مسبقاً؟
        if (auth()->check()) {
            $cartItem = Cart::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();
        } else {
            $cartItem = Cart::where('session_id', $sessionId)
                            ->where('product_id', $productId)
                            ->first();
        }

        // 2. زيادة الكمية أو إنشاء جديد
        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            Cart::create([
                'product_id' => $productId,
                'quantity' => 1,
                'session_id' => $sessionId,
                'user_id' => $userId,
            ]);
        }

        return redirect()->back()->with('success', 'تمت الإضافة للسلة! 🛒');
    }

    // 3. حذف منتج من السلة
    public function destroy($id)
    {
        Cart::destroy($id);
        return redirect()->route('cart.index');
    }
    // 4. إتمام الشراء والتحويل للواتساب (الدالة الجديدة)
    public function checkout()
    {
        $userId = auth()->id();
        
        // جلب عناصر السلة
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();

        if($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'السلة فارغة!');
        }

        // حساب المجموع الكلي
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        // تجهيز نص رسالة الواتساب
        $customerName = auth()->user()->name;
        $orderDate = date('Y-m-d H:i');
        
        $msg = "مرحباً، طلب جديد من المتجر! 🛍️\n";
        $msg .= "------------------------\n";
        $msg .= "👤 العميل: *$customerName*\n";
        $msg .= "📅 التاريخ: $orderDate\n";
        $msg .= "💰 الإجمالي: *$total ريال*\n";
        $msg .= "------------------------\n";
        $msg .= "المنتجات:\n";

        foreach($cartItems as $item) {
            $msg .= "- " . $item->product->name . " (العدد: " . $item->quantity . ")\n";
        }

        $msg .= "\nيرجى تأكيد الطلب وتجهيزه.";

        // رقمك (اليمن)
        $myPhone = "967734464015";

        // إفراغ السلة بعد إرسال الطلب (مهم جداً حتى لا يشتري نفس الأشياء مرتين)
        Cart::where('user_id', $userId)->delete();

        // التوجيه للواتساب
        $whatsappUrl = "https://wa.me/$myPhone?text=" . urlencode($msg);
        return redirect()->away($whatsappUrl);
    }
}