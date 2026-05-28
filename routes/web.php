<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\BookController;

// 「/books」にアクセスしたらBookControllerのindexという関数を動かす
Route::get('/books', [BookController::class, 'index'])
    // ->middleware(['auth']) // 💡 頭に「//」を付けて一時的に無効化します！
    ->name('books.index');

    // routes/web.php

// 💡 ログアウト処理の通り道を追加！
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
    // routes/web.php

// 💡 書籍のタイトルをクリックすると書籍の詳細ページにとぶ
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.showDetail');

// routes/web.php

// 💡 経理部専用の書籍管理画面
Route::get('/admin/books', [BookController::class, 'adminIndex'])->name('books.admin');