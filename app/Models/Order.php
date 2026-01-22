<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // 🔥 فتح الحماية للسماح بحفظ الطلب 🔥
    protected $guarded = [];

    // علاقة الطلب بالمستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الطلب بالمنتجات
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}