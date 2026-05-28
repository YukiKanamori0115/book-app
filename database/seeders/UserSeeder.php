<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // テストユーザー（社員）を登録
        DB::table('users')->insert([
            'employee_id' => '0001',
            'name' => '山田太郎',
            'password' => Hash::make('password123'), // ログイン用パスワード
            'role_id' => 1, // 1: 管理者
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}