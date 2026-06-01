<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // ユーザーが使うロールを先に作成する
        Role::factory()->create(['id' => 1]);
    }

    /**
     * 基本的な画面表示のテスト
     */
    public function test_書籍一覧画面が表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewHas('books');
    }

    /**
     * キーワード（あいまい）検索のテスト
     */
    public function test_キーワードで書籍検索ができる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田太郎']);
        Book::factory()->create(['title' => 'PHPの歩き方', 'author' => '鈴木次郎']);

        // 「山田」で検索
        $response = $this->get(route('books.index', ['keyword' => '山田']));

        $response->assertSee('Laravel入門');
        $response->assertDontSee('PHPの歩き方');
    }

    /**
     * ISBN完全一致検索のテスト
     */
    public function test_ISBNで完全一致検索ができる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Book::factory()->create(['title' => '特定の書籍', 'isbn13' => '9784123456789']);
        Book::factory()->create(['title' => '別の書籍', 'isbn13' => '9784999999999']);

        // ISBNで検索
        $response = $this->get(route('books.index', ['isbn' => '9784123456789']));

        $response->assertSee('特定の書籍');
        $response->assertDontSee('別の書籍');
    }

    /**
     * 検索結果0件時のメッセージテスト
     */
    public function test_存在しない検索でメッセージが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('books.index', ['keyword' => '存在しない本']));

        $response->assertSee('該当する書籍は見つかりませんでした。');
    }
}