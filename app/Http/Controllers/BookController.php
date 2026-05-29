<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * 書籍一覧（検索機能付き）
     */
    public function index(Request $request) // 💡引数に Request $request を追加
    {
        // 1. 検索キーワードを画面から受け取る（空っぽの場合もあります）
        $keyword = $request->input('keyword');

        // 2. 書籍のクエリ（検索の準備）を始める
        $query = Book::query();

        // 3. もしキーワードが入力されていたら、絞り込みを行う
        if (!empty($keyword)) {
            $query->where('title', 'LIKE', "%{$keyword}%")      // タイトルにキーワードを含む
                  ->orWhere('author', 'LIKE', "%{$keyword}%")   // または、著者にキーワードを含む
                  ->orWhere('isbn13', 'LIKE', "%{$keyword}%");  // または、ISBNにキーワードを含む
        }

        // 4. 条件に合うデータをデータベースから取得する
        $books = $query->get();

        // 5. 画面にデータを渡して表示
        return view('books.index', compact('books'));
    }
    /**
     * 書籍詳細を表示する
     */
    public function show(Book $book)
    {
        // 💡 開くファイル名を 'books.showDetail' に変更！
        return view('books.showDetail', compact('book'));
    }
}