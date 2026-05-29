<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    /**
     * 複数代入（一括保存）を許可する属性
     * 仕様変更に伴い、扱う3項目のみを指定します
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'isbn13',
        'title',
        'author',
    ];

    /**
     * reviewsテーブルとの1対多のリレーションを定義
     * 1つの書籍（Book）に対して、複数のレビュー（Review）が紐づきます
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
