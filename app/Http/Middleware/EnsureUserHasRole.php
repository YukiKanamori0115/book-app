<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * ハンドル処理：リクエストのアクセス権限を判定
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role ルーティング側から渡される権限名（例: '経理部'）
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. 未ログインならログイン画面へ強制転送
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. ログインユーザーのroleリレーション経由で名前を比較判定
        // users.role_id ➔ roles.id ➔ roles.name
        if (!$request->user()->role || $request->user()->role->name !== $role) {
            // 権限が一致しない場合は403エラー（閲覧禁止）を返却
            abort(403, 'この操作を行う権限がありません。');
        }

        return $next($request);
    }
}
