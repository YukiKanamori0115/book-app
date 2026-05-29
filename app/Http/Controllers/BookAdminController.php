<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookAdminController extends Controller
{
    /**
     * F-07: 書籍管理画面(ISBN確認フォーム)表示
     */
    public function checkForm()
    {
        return view('admin.books.check');
    }

    /**
     * F-08: ISBN確認処理 (同期送信・API通信＆分岐)
     */
    public function checkIsbn(Request $request)
    {
        // 13桁の半角数字のみを許容するバリデーション
        $request->validate([
            'isbn13' => ['required', 'string', 'size:13', 'regex:/^[0-9]+$/'],
        ], [
            'isbn13.regex' => 'ISBNは13桁の半角数字で入力してください。',
        ]);

        $isbn = $request->input('isbn13');

        // 仕様: すでにローカルDBに登録済み（既登録）かを確認
        $existsInDb = Book::where('isbn13', $isbn)->exists();
        if ($existsInDb) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'この書籍は既にシステムに登録されています。');
        }

        try {
            // openBD APIへ通信 (タイムアウト5秒制限)
            $response = Http::timeout(5)->get("https://api.openbd.jp/v1/get?isbn={$isbn}");

            if ($response->failed()) {
                throw new \Exception('openBD API connection failed');
            }

            $data = $response->json();

            // openBDに対象ISBNのデータが存在しない場合の分岐
            if (empty($data) || $data[0] === null) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', '指定されたISBNの書籍情報がopenBDに見つかりませんでした。');
            }

            // 書誌情報のパース (必要な3項目のみを安全に抽出)
            $summary = $data[0]['summary'] ?? [];

            // 出版社・価格・フラグ等は除外し、扱うのは以下の3項目のみ
            $queryParams = [
                'isbn13' => $summary['isbn'] ?? $isbn,
                'title'  => $summary['title'] ?? '',
                'author' => $summary['author'] ?? '不明',
            ];

            return redirect()->route('admin.books.create', $queryParams);
        } catch (\Exception $e) {
            Log::error("ISBN確認エラー: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '外部APIとの通信に失敗しました。時間をおいて再度お試しください。');
        }
    }

    /**
     * F-09: 書籍登録画面表示 (APIから引き継いだデータを初期値としてセット)
     */
    public function create(Request $request)
    {
        // URLパラメータから必須の3項目のみを抽出し、初期値用として配列に格納
        $book = [
            'isbn13' => $request->query('isbn13'),
            'title'  => $request->query('title'),
            'author' => $request->query('author', '不明'),
        ];

        // ガード処理: 必須であるISBNとタイトルがURLに含まれていない場合は確認画面に戻す
        if (empty($book['isbn13']) || empty($book['title'])) {
            return redirect()->route('admin.books.checkForm')
                ->with('error', 'ISBNの確認を最初に行ってください。');
        }

        return view('admin.books.create', compact('book'));
    }

    /**
     * F-10: 書籍登録処理 (データベースへの書き込み)
     */
    public function store(Request $request)
    {
        // バリデーション対象も3項目のみ
        $validated = $request->validate([
            'isbn13' => ['required', 'string', 'size:13', 'unique:books,isbn13'],
            'title'  => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
        ]);

        // 著者名が未入力や空文字の時は「不明」をアプリケーション側でも補完
        if (empty($validated['author'])) {
            $validated['author'] = '不明';
        }

        // データベースに登録実行（物理削除のみのため、is_deletedは完全に除外）
        Book::create([
            'isbn13' => $validated['isbn13'],
            'title'  => $validated['title'],
            'author' => $validated['author'],
        ]);

        // 完了後、仕様書に従って書籍一覧画面へ転送
        return redirect()->route('books.index')
            ->with('status', '書籍の登録が完了しました。');
    }
}
