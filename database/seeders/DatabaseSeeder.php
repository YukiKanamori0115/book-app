<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 💡 順番が超重要です！
        $this->call([
            RoleSeeder::class,  // ① まず役割を作る
            UserSeeder::class,  // ② 次にその役割を持ったユーザーを作る
            BookSeeder::class,  // ③ 最後に本を作る
            ReviewSeeder::class,
        ]);
    }
}
