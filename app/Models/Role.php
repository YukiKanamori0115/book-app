<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * 複数代入を許可する属性（テーブル定義書に基づく）
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // 権限名（'一般社員', '経理部'）
    ];

    /**
     * usersテーブルとの1対多のリレーションを定義
     * 1つの権限（Role）に対して、複数のユーザー（User）が所属します
     */
    public function users(): HasMany
    {
        // roles.id と users.role_id を自動で紐付けます
        return $this->hasMany(User::class);
    }
}
