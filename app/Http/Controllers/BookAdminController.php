<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // ユニーク制約の除外設定に必要

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
        // 1. バリデーションの前に、リクエストデータを数字のみに一括置換
        $request->merge([
            'isbn13' => preg_replace('/[^0-9]/', '', $request->input('isbn13'))
        ]);

        // 2. バリデーション
        $request->validate([
            'isbn13' => ['required', 'string', 'size:13', 'regex:/^[0-9]+$/'],
        ], [
            'isbn13.size'  => 'ISBNは13桁の半角数字（ハイフン除く）で入力してください。',
            'isbn13.regex' => 'ISBNは13桁の半角数字で入力してください。',
        ]);

        // 3. 安全に取得
        $isbn = $request->input('isbn13'); // ここには13桁の数字だけが入っています

        // 【機能拡張】すでにローカルDBに登録済みの場合は、即座に「編集画面」へリダイレクト
        $existingBook = Book::where('isbn13', $isbn)->first();
        if ($existingBook) {
            return redirect()->route('admin.books.edit', $existingBook->id)
                ->with('status', '指定された書籍は既に登録されています。情報を編集できます。');
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
            $authorName = $summary['author'] ?? '不明';

            // 1. 先に「／著」や「生没年（1896-1933）」を削除
            $authorName = preg_replace('/(／著)?(,\d{4}-\d{4})?$/u', '', $authorName);

            // 2. さらに名前の途中に入り込む「全角・半角のカンマ」を削除
            $authorName = str_replace([',', ','], '', $authorName);

            $queryParams = [
                'isbn13' => $summary['isbn'] ?? $isbn,
                'title'  => $summary['title'] ?? '',
                'author' => $authorName, // 1,2で修正した名前を入れる
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
        return redirect()->route('admin.books.checkForm')
            ->with('status', '書籍の登録が完了しました。');
    }

    /**
     * F-11: 書籍編集画面表示
     */
    public function edit($id)
    {
        // 指定されたIDがなければ自動で404エラーを返す安全設計
        $book = Book::findOrFail($id);
        return view('admin.books.edit', compact('book'));
    }

    /**
     * F-12: 書籍更新処理
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        // バリデーション: isbn13は「自分自身のIDを除いてユニーク」にするルールを適用
        $validated = $request->validate([
            'isbn13' => ['required', 'string', 'size:13', 'regex:/^[0-9]+$/', Rule::unique('books', 'isbn13')->ignore($book->id)],
            'title'  => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
        ]);

        if (empty($validated['author'])) {
            $validated['author'] = '不明';
        }

        // データベースのレコードを更新
        $book->update($validated);

        // 更新後は要求仕様に従い、一覧画面にフラッシュメッセージ付きでリダイレクト
        return redirect()->route('admin.books.checkForm')
            ->with('status', '書籍情報を更新しました。');
    }

    /**
     * F-13: 書籍削除処理 (物理削除)
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        // データベースから物理削除（完全抹消）を実行
        $book->delete();

        // 削除完了後は、再び書籍を扱いやすいようISBN確認画面（管理トップ）へ戻す
        return redirect()->route('admin.books.checkForm')
            ->with('status', '書籍情報をデータベースから削除しました。');
    }
}
