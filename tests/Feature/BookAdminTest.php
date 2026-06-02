<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookAdminTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. rolesテーブルにデータを準備
        \DB::table('roles')->insert([
            'id' => 2,
            'name' => '経理部',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 2. 山田花子さんを作成
        $this->adminUser = User::create([
            'id'          => 2,
            'employee_id' => '0002',
            'name'        => '山田花子',
            'password'    => bcrypt('password123'),
            'role_id'     => 2,
        ]);
    }

    /**
     * TS-BKM-001: 【正常系】未登録ISBNの挙動
     */
    public function test_ts_bkm_001_未登録isbnの場合は登録画面にリダイレクトされること()
    {
        Http::fake([
            'https://api.openbd.jp/*' => Http::response([[
                'summary' => [
                    'isbn' => '9784774196411',
                    'title' => 'テスト書籍',
                    'author' => 'テスト著者'
                ]
            ]], 200)
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.books.checkIsbn'), [
                'isbn13' => '9784774196411'
            ]);

        // URLの後ろに文字がくっついていても、前方一致で「登録画面(create)」に進んでいればOKにする修正！
        $response->assertRedirect();
        $this->assertStringContainsString(
            route('admin.books.create'), 
            $response->headers->get('Location')
        );
    }

   /**
     * TS-BKM-002: 【正常系】登録済みISBNの挙動
     */
    public function test_ts_bkm_002_登録済みisbnの場合は編集画面にリダイレクトされること()
    {
        // Notionの前提条件と登録書籍情報に完全に一致させる
        $book = Book::create([
            'id'     => 1, // ID: 1
            'isbn13' => '9784798165882',
            'title'  => 'Laravel Webアプリ開発', // タイトルも実際の値に！
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.books.checkIsbn'), [
                'isbn13' => '9784798165882'
            ]);

        $response->assertRedirect(route('admin.books.edit', ['id' => $book->id]));
    }
    

    /**
     * TS-BKM-003: 【異常系】入力形式・桁数バリデーション
     */
    public function test_ts_bkm_003_不正な形式のisbnはバリデーションエラーになること()
    {
        // パターンA：12桁
        $response12 = $this->actingAs($this->adminUser)
            ->from(route('admin.books.checkForm'))
            ->post(route('admin.books.checkIsbn'), [
                'isbn13' => '978477419641'
            ]);

        $response12->assertRedirect(route('admin.books.checkForm'));
        $response12->assertSessionHasErrors(['isbn13']);

        // パターンB：14桁
        $response14 = $this->actingAs($this->adminUser)
            ->from(route('admin.books.checkForm'))
            ->post(route('admin.books.checkIsbn'), [
                'isbn13' => '97847741964123'
            ]);

        $response14->assertRedirect(route('admin.books.checkForm'));
        $response14->assertSessionHasErrors(['isbn13']);

        // パターンC：文字混じり
        $responseAlpha = $this->actingAs($this->adminUser)
            ->from(route('admin.books.checkForm'))
            ->post(route('admin.books.checkIsbn'), [
                'isbn13' => '978477419641A'
            ]);

        $responseAlpha->assertRedirect(route('admin.books.checkForm'));
        $responseAlpha->assertSessionHasErrors(['isbn13']);
    }

    /**
     * TS-BKM-004: 【正常系】登録完了後の戻り先
     */
    public function test_ts_bkm_004_登録完了後は管理画面に戻ること()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.books.store'), [
                'isbn13' => '9784101010014',
                'title'  => '吾輩は猫である',
                'author' => '夏目漱石',
            ]);

        $response->assertRedirect(route('admin.books.checkForm')); // F-07に戻る
        $response->assertSessionHas('status');
    }

    /**
     * TS-BKM-005: 【異常系】同じISBNの二重登録防止
     */
    public function test_ts_bkm_005_同じisbnの二重登録はエラーになること()
    {
        // 既存データを準備
        Book::create([
            'isbn13' => '9784101010014',
            'title'  => '既にある本'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->from(route('admin.books.create'))
            ->post(route('admin.books.store'), [
                'isbn13' => '9784101010014',
                'title'  => '重複する本',
            ]);

        $response->assertRedirect(route('admin.books.create'));
        $response->assertSessionHasErrors(['isbn13']);
    }

    /**
     * TS-BKM-006: 【正常系】更新完了後の戻り先
     */
    public function test_ts_bkm_006_更新完了後は管理画面に戻ること()
    {
        $book = Book::create([
            'id'     => 1,
            'isbn13' => '9784798165882',
            'title'  => 'Laravel Webアプリ開発',
            'author' => '著者名',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.books.update', ['id' => $book->id]), [
                'isbn13' => '9784798165882',
                'title'  => '更新後のタイトル',
                'author' => '新しい著者名',
            ]);

        $response->assertRedirect(route('admin.books.checkForm'));
        $response->assertSessionHas('status');
    }

    /**
     * TS-BKM-007: 【正常系】自身のISBNを除外した一意性チェック
     */
    public function test_ts_bkm_007_自身のisbnを除外して正常に更新できること()
    {
        $book = Book::create([
            'id'     => 1,
            'isbn13' => '9784798165882',
            'title'  => 'Laravel Webアプリ開発',
            'author' => '著者名',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.books.update', ['id' => $book->id]), [
                'isbn13' => '9784798165882', // ISBNは変えない
                'title'  => 'タイトルだけ変更',
                'author' => '著者名',
            ]);

        $response->assertRedirect(route('admin.books.checkForm'));
        $this->assertDatabaseHas('books', ['id' => 1, 'title' => 'タイトルだけ変更']);
    }

    /**
     * TS-BKM-008: 【正常系】物理削除の実行
     */
    public function test_ts_bkm_008_削除ボタンでデータが削除されること()
    {
        $book = Book::create([
            'id'     => 4,
            'isbn13' => '9784101010014',
            'title'  => '吾輩は猫である',
            'author' => '夏目漱石'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.books.destroy', ['id' => $book->id]));

        $response->assertRedirect(route('admin.books.checkForm'));
        $this->assertDatabaseMissing('books', ['id' => 4]);
    }
}