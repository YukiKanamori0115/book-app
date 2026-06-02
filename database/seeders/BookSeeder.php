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
        Book::create([
            'isbn13' => '9784295017936',
            'title' => 'スッキリわかるJava入門',
            'author' => '中山清喬 国本大悟 フレアリンク',
        ]);Book::create([
            'isbn13' => '9784297128524',
            'title' => '図解でやさしくわかるネットワークのしくみ超入門 : フルカラーイラストでネットワークがわかる',
            'author' => '網野衛二',
        ]);Book::create([
            'isbn13' => '9784797380941',
            'title' => '新しいLinuxの教科書',
            'author' => '三宅英明 大角祐介',
        ]);
    }
}
