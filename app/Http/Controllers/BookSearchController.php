<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookSearchController extends Controller
{
    //書籍管理（ISBN検索）
    public function isbnCheck(Request $request)
    {
        // 数字以外をすべて排除する（一番安全）
        $isbn = preg_replace('/[^0-9]/', '', $request->isbn);

        //自社DB検索（購入済みかチェック）
        $book = Book::where('isbn13', $isbn)->first();

        // 既に登録済みの場合は書籍編集画面(F10)へ
        if ($book) {
            return redirect()->route('books.edit', $book->id);
        }

        // 登録されていないのでopenBDでAPI検索
        $response = Http::get("https://api.openbd.jp/v1/get?isbn={$isbn}");
        // JSON配列で返るので変換
        $data = $response->json();

        // APIに書籍が存在するかチェック
        //openBDからデータが返ってこなかった場合
        if (!$data || !$data[0]) {
            return back()->with('error', 'ISBNが存在しません');
        }
        //書籍情報取得
        $summary = $data[0]['summary'] ?? null;
        //ISBNはあるが中身が破損している場合
        if (!$summary) {
            return back()->with('error', '書籍情報が取得できません');
        }
        //書籍がある場合は書籍登録画面に移動（F-09）
        return redirect()->route('books.create', [
            'isbn'   => $isbn,
            'title'  => $summary['title'] ?? '',
            'author' => $summary['author'] ?? '',
        ]);
    }
}
