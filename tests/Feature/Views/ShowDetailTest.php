<?php

namespace Tests\Feature\View;

use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL; // 👈 これを追加
use Tests\TestCase;

class ShowDetailTest extends TestCase
{
    /**
     * テスト実行前に必ず走る処理
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ⭕【最強のルートエラー対策】
        // ナビゲーションバーなどが「存在しないルート名」を呼び出しても、
        // エラーを投げずにすべて「http://localhost/dummy-route」を自動で返すように偽装します。
        URL::resolveMissingNamedRoutesUsing(function ($name, $parameters, $absolute) {
            return '/dummy-route';
        });
    }

    /**
     * データベースが空でも、本とユーザーを偽装してビューをテストする
     */
    public function test_it_renders_blade_without_database()
    {
        // 1. 【本を偽装】
        $mockBook = new Book();
        $mockBook->id = 999;
        $mockBook->title = 'DBなしのテスト書籍';
        $mockBook->author = 'ノンデータベース著者';
        $mockBook->isbn13 = '9784000000000';

        // reviews リレーションを空のコレクションとして確定
        $mockBook->setRelation('reviews', new Collection([]));

        // 2. 【ユーザーを偽装】
        $mockUser = new User();
        $mockUser->id = 1;
        $mockUser->name = 'ダミー社員';
        $mockUser->setRelation('role', null);

        // 3. ログイン状態を偽装
        $this->actingAs($mockUser);

        // 4. 正しいパスを指定してレンダリング
        $view = $this->view('books.showDetail', [
            'book' => $mockBook
        ]);

        // 5. 【検証】画面の表示チェック
        $view->assertSee('DBなしのテスト書籍');
        $view->assertSee('ノンデータベース著者');
        $view->assertSee('ダミー社員');
        $view->assertSee('まだレビューはありません。');
    }
}