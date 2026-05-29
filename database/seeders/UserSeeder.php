<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // テストユーザー（一般社員）を登録
        DB::table('users')->insert([
            'employee_id' => '0001',
            'name' => '山田太郎',
            'password' => Hash::make('password123'), // ログイン用パスワード
            'role_id' => 2, // 2: 一般社員
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // テストユーザー（経理部）を登録
        DB::table('users')->insert([
            'employee_id' => '0002',
            'name' => '田中花子',
            'password' => Hash::make('password123'), // ログイン用パスワード
            'role_id' => 1, // 1: 経理部
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
