<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * 書籍一覧画面を表示
     */
    public function index(Request $request)
    {
        // 1. クエリビルダを開始
        $query = Book::query();

        // 2. ISBN検索（優先・完全一致）
        if ($request->filled('isbn')) {
            $isbn = str_replace('-', '', $request->isbn);
            $query->where('isbn13', $isbn);
        } 
        // 3. キーワード検索（タイトルまたは著者・あいまい検索）
        elseif ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('author', 'LIKE', "%{$keyword}%");
            });
        }

        // 4. 結果を取得（リレーションも一緒にロード）
        $books = $query->with('reviews')->get();

        // 5. 検索結果が0件の場合のメッセージ設定
        if (($request->filled('isbn') || $request->filled('keyword')) && $books->isEmpty()) {
            session()->flash('info', '該当する書籍は見つかりませんでした。');
        }

        // 6. ビューに渡す
        return view('books.index', compact('books'));
    }

    /**
     * 書籍詳細画面を表示
     */
    public function show($id)
    {
        // URLの {id} を元に、データベースから書籍を1件取得
        $book = Book::with('reviews.user')->findOrFail($id);

        return view('books.show', compact('book'));
    }
}