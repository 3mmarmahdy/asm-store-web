<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // 🔥 هذا هو الجزء الناقص الذي يسبب الخطأ 500 🔥
    // نحن نسمح هنا للكود بتعبئة هذه البيانات
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'session_id',
    ];

    // علاقة السلة مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    // علاقة السلة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}