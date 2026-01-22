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
    public function addToCart($productId)
    {
        try {
            $sessionId = \Illuminate\Support\Facades\Session::getId();
            $userId = auth()->id();

            // التحقق من وجود المنتج في السلة
            if (auth()->check()) {
                $cartItem = Cart::where('user_id', $userId)
                                ->where('product_id', $productId)
                                ->first();
            } else {
                $cartItem = Cart::where('session_id', $sessionId)
                                ->where('product_id', $productId)
                                ->first();
            }

            // التحديث أو الإنشاء
            if ($cartItem) {
                $cartItem->quantity += 1;
                $cartItem->save();
            } else {
                // هنا غالباً تحدث المشكلة
                Cart::create([
                    'product_id' => $productId,
                    'quantity' => 1,
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                ]);
            }

            return redirect()->back()->with('success', 'تمت إضافة المنتج للسلة بنجاح ✅');

        } catch (\Exception $e) {
            // هذا السطر سيطبع الخطأ على الشاشة بدلاً من 500
            die('<div style="background:#f8d7da; color:#721c24; padding:20px; text-align:center; font-family:sans-serif; direction:ltr;">
                    <h1>🚨 تم كشف الخطأ!</h1>
                    <h3>صور هذه الشاشة وارسلها لي:</h3>
                    <p style="font-size:18px; font-weight:bold; border:2px dashed red; padding:10px;">' . $e->getMessage() . '</p>
                 </div>');
        }
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