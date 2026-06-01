<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Gate; // 💡 これを追加

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
        // 既存のログイン後のリダイレクト設定
        RedirectIfAuthenticated::redirectUsing(function () {
            return route('books.index');
        });

        // 💡 権限判定（Gate）をここに直接記述します！
        Gate::define('is-accounting', function ($user) {
            return $user->role && $user->role->name === '経理部';
        });
    }
}