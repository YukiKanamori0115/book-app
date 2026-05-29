<?php

namespace App\Http\Controllers;

use App\Models\Book; // 💡Bookモデルを使う宣言
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * 書籍一覧画面を表示
     */
    public function index(Request $request)
{
    // 1. 入力されたキーワードを取得
    $keyword = $request->input('keyword');

    // 2. クエリビルダを開始
    $query = Book::query();

    // 3. キーワードがある場合のみ、あいまい検索（LIKE）を追加
    if (!empty($keyword)) {
        $query->where(function($q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
                ->orWhere('author', 'LIKE', "%{$keyword}%");
        });
    }

    // 4. 結果を取得（リレーションも一緒にロードしておくと効率的です）
    $books = $query->with('reviews')->get();

    // 5. ビューに渡す
    return view('books.index', compact('books'));
}
}