<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Book;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のユーザーと本のIDをすべて取得
        $userIds = User::pluck('id')->toArray();
        $bookIds = Book::pluck('id')->toArray();

        // もしUserやBookのデータが空の場合、エラーを防ぐためにダミーのID（1〜3）を入れておく
        if (empty($userIds)) {
            $userIds = [1, 2, 3];
        }
        if (empty($bookIds)) {
            $bookIds = [1, 2, 3];
        }

        // レビューのテストデータ
        $reviews = [
            [
                'user_id'    => $userIds[array_rand($userIds)], // ランダムにユーザーIDを割り当て
                'book_id'    => $bookIds[array_rand($bookIds)], // ランダムに本IDを割り当て
                'rating'     => 5,
                'title'      => '最高の一冊でした！',
                'comment'    => '非常に読みやすく、一気に読破してしまいました。初心者にもおすすめしたい本です。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => $userIds[array_rand($userIds)],
                'book_id'    => $bookIds[array_rand($bookIds)],
                'rating'     => 4,
                'title'      => '実務で役立つ内容',
                'comment'    => '解説が丁寧で分かりやすかったです。少し応用的な内容も含まれているので、何度も読み返します。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => $userIds[array_rand($userIds)],
                'book_id'    => $bookIds[array_rand($bookIds)],
                'rating'     => 3,
                'title'      => '内容は良いが、少し難しい',
                'comment'    => 'テーマは興味深かったのですが、予備知識がないと少し理解するのに時間がかかるかもしれません。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // データベースに挿入
        DB::table('reviews')->insert($reviews);
    }
}
