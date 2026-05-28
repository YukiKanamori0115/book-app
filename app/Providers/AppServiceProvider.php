<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated; // 💡これを上に1行追加

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
    public function boot(): void
    {
        // 💡ここにログイン後のリダイレクト先（/books）を設定します！
        RedirectIfAuthenticated::redirectUsing(function () {
            return route('books.index'); // もしくは return '/books'; でもOK
        });
    }
}