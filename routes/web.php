<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\BookController;

// 「/books」にアクセスしたらBookControllerのindexという関数を動かす
Route::get('/books', [BookController::class, 'index'])
->middleware(['auth'])
->name('books.index');

