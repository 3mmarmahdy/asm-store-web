<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\IsAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| 1. روابط المصادقة
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

/*
|--------------------------------------------------------------------------
| 2. روابط العميل العامة
|--------------------------------------------------------------------------
*/
Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

// السلة
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

// الدفع
Route::get('/checkout', [OrderController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');


/*
|--------------------------------------------------------------------------
| 3. روابط المدير (المحمية)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', IsAdmin::class])->group(function () {
    
    // إدارة المنتجات
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // إدارة الطلبات (تم التصحيح هنا لاستخدام الدالة الجديدة adminIndex)
    Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders');
    Route::get('/admin/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
});

/*
|--------------------------------------------------------------------------
| 4. عرض المنتج
|--------------------------------------------------------------------------
*/
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/fix-permissions', function () {
    try {
        // البحث عن حساب المدير أو إنشاؤه
        $user = User::firstOrNew(['email' => 'admin@store.com']); // استخدمنا نفس ايميلك في Seeder
        
        $user->name = 'Admin Ammar';
        if (!$user->exists) {
            $user->password = Hash::make('123456');
        }

        // كود ذكي: يفحص الأعمدة الموجودة ويملؤها
        $messages = [];

        // 1. إذا كان النظام يستخدم is_admin
        if (Schema::hasColumn('users', 'is_admin')) {
            $user->is_admin = true; // أو 1
            $messages[] = "تم تفعيل is_admin ✅";
        }

        // 2. إذا كان النظام يستخدم role (كما في السجلات)
        if (Schema::hasColumn('users', 'role')) {
            $user->role = 'admin';
            $messages[] = "تم تفعيل role = admin ✅";
        }

        // 3. احتياط: إذا كان يستخدم usertype
        if (Schema::hasColumn('users', 'usertype')) {
            $user->usertype = 'admin';
            $messages[] = "تم تفعيل usertype ✅";
        }

        $user->save();

        return "<h1>تم إصلاح الصلاحيات بنجاح! 🚀</h1>" .
               "<p><strong>البريد:</strong> admin@store.com</p>" .
               "<p><strong>كلمة المرور:</strong> 123456</p>" .
               "<h3>التفاصيل:</h3><ul><li>" . implode('</li><li>', $messages) . "</li></ul>" .
               "<br><a href='/login'>اذهب لصفحة الدخول</a>";

    } catch (\Exception $e) {
        return "حدث خطأ: " . $e->getMessage();
    }
});