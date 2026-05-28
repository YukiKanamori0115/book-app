<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('books', function (Blueprint $table) {
        $table->id();                                    // 1. 書籍ID (PK, AI)
        $table->string('isbn13', 13)->unique();          // 2. ISBN13 (UK, NOT NULL) ※nullableは削除
        $table->string('title');                         // 3. タイトル (NOT NULL) ※nullableは削除
        $table->string('author')->nullable()->default('不明'); // 4. 著者名 (NULL, デフォルト'不明')
        $table->timestamps();                            // 5,6. 作成・更新日時
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
