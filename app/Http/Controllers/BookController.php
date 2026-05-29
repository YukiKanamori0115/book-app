<?php

namespace App\Http\Controllers;

use App\Models\Book; // 💡Bookモデルを使う宣言
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        // 1. データベースからすべての本を取得する
        $books = Book::all();

        // 2. 「books/index」という画面（Blade）に、本のデータを渡して表示する
        return view('books.index', compact('books'));
    }
}