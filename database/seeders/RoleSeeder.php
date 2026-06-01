<?php

namespace Database\Seeders;

use App\Models\Role; // 💡もしRoleモデルがなければ、ここを DB::table('roles') に書き換える必要がありますが、まずはこれで試します
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 役割データを登録
        DB::table('roles')->insert([
            ['id' => 1, 'name' => '経理部', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => '一般社員', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}