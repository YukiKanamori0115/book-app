<?php

namespace Database\Seeders;

use App\Models\Book; // 💡Bookモデルを使う宣言
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1種類目の本を登録
        Book::create([
            'isbn13' => '9784798165882',
            'title' => 'Laravel Webアプリ開発',
            'author' => '山田太郎',
        ]);

        // 2種類目の本を登録
        Book::create([
            'isbn13' => '9780000000000',
            'title' => '達人プログラマー',
            'author' => '大阪花子',
        ]);

    }
}
