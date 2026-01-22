<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     if($this->app->environment('production')) {
    //         \Illuminate\Support\Facades\URL::forceScheme('https');
    //     }
    // }
    public function boot(): void
    {
        // 1. إجبار HTTPS
        if($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // 2. 🔥 كود عداد السلة الذكي 🔥
        // هذا الكود يرسل متغير $cartCount لكل صفحات الموقع
        view()->composer('*', function ($view) {
            $count = 0;
            try {
                if (auth()->check()) {
                    $count = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
                } else {
                    $count = \App\Models\Cart::where('session_id', \Illuminate\Support\Facades\Session::getId())->sum('quantity');
                }
            } catch (\Exception $e) {
                $count = 0;
            }
            $view->with('cartCount', $count);
        });
    }
}
