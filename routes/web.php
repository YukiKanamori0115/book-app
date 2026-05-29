<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookAdminController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 社内書籍管理システムのルーティング定義
|
*/

// =========================================================================
// 0. ルートURL (/) へのアクセス制御 (404対策・仕様補完)
// =========================================================================
Route::get('/', function () {
    // ログイン済みなら書籍一覧、未ログインならログイン画面へリダイレクト
    return Auth::check()
        ? redirect()->route('books.index')
        : redirect()->route('login');
});

// =========================================================================
// 1. 未ログインユーザー専用ルート (Guest Middleware)
// =========================================================================
Route::middleware('guest')->group(function () {
    // ログイン画面表示 (F-01)
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // ログイン処理 (F-02)
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// =========================================================================
// 2. 認証済みユーザー共通ルート (Auth Middleware - 一般社員・経理部共通)
// =========================================================================
Route::middleware('auth')->group(function () {

    // ログアウト処理 (F-03)
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // --- 書籍閲覧セクション ---
    // 書籍一覧・検索画面表示 (F-04)
    Route::get('/books', [BookController::class, 'index'])
        ->name('books.index');

    // 書籍詳細画面表示 (F-05)
    Route::get('/books/{id}', [BookController::class, 'show'])
        ->name('books.show')
        ->whereNumber('id');

    // --- レビュー非同期処理セクション (F-06 / 設計書補完分) ---
    Route::post('/books/{book_id}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store')
        ->whereNumber('book_id');

    Route::put('/reviews/{id}', [ReviewController::class, 'update'])
        ->name('reviews.update')
        ->whereNumber('id');

    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy')
        ->whereNumber('id');

    Route::get('/books/{book_id}/reviews-all', [ReviewController::class, 'getAllReviews'])
        ->name('reviews.all')
        ->whereNumber('book_id');
});

// =========================================================================
// 3. 経理部社員専用ルート (Auth & Role Middleware)
// =========================================================================
// ※カスタムミドルウェア 'role:経理部' により、一般社員のアクセスを拒否(403)します。
Route::middleware(['auth', 'role:経理部'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // --- ISBN確認処理 ---
        // 書籍管理画面(ISBN確認)表示 (F-07)
        Route::get('/books/check', [BookAdminController::class, 'checkForm'])
            ->name('books.checkForm');

        // ISBN確認処理 (F-08)
        Route::post('/books/check', [BookAdminController::class, 'checkIsbn'])
            ->name('books.checkIsbn');

        // --- 書籍マスタ登録 ---
        // 書籍登録画面表示 (F-09)
        Route::get('/books/create', [BookAdminController::class, 'create'])
            ->name('books.create');

        // 書籍登録処理 (F-10)
        Route::post('/books', [BookAdminController::class, 'store'])
            ->name('books.store');

        // --- 書籍マスタ編集・削除 ---
        // 書籍編集画面表示 (F-11)
        Route::get('/books/{id}/edit', [BookAdminController::class, 'edit'])
            ->name('books.edit')
            ->whereNumber('id');

        // 書籍更新処理 (F-12)
        Route::put('/books/{id}', [BookAdminController::class, 'update'])
            ->name('books.update')
            ->whereNumber('id');

        // 書籍削除処理 (削除フラグ更新) (F-12)
        Route::delete('/books/{id}', [BookAdminController::class, 'destroy'])
            ->name('books.destroy')
            ->whereNumber('id');
    });

// =========================================================================
// 参考画面: Laravel Breeze で自動生成されるプロフィール管理ルート
// =========================================================================
//ダッシュボード画面
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// プロフィール画面
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
