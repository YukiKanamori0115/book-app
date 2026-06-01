<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * レビュー新規投稿 (POST /books/{book_id}/reviews)
     */
    public function store(Request $request, $book_id)
    {
        $userId = Auth::id();
        $book = Book::findOrFail($book_id);
        
        $validated = $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        // 【新規投稿モード】二重投稿チェック
        $exists = Review::where('book_id', $book->id)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'error'   => '既にレビューを投稿済みです。'
            ], 400);
        }

        Review::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'rating'  => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'レビューを投稿しました'
        ]);
    }

    /**
     * レビュー修正 (PUT /reviews/{id})
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        // ログインユーザー本人のレビューか確認して取得
        $review = Review::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $review->update([
            'rating'  => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'レビューを修正しました'
        ]);
    }

    /**
     * レビュー削除（非同期対応）(DELETE /reviews/{id})
     */
    public function destroy($id)
    {
        // ルートパラメータ {id} からレビューを取得
        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error'   => '削除権限がありません。'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'レビューを削除しました'
        ]);
    }

    /**
     * 「↓もっと見る」用・リフレッシュ用の全件取得 (GET /books/{book_id}/reviews-all)
     */
    public function getAllReviews($book_id)
    {
        $book = Book::findOrFail($book_id);

        // ユーザーに紐づく権限テーブル(role)もまとめてロード
        $reviews = $book->reviews()->with('user.role')->get()->map(function ($review) {
            return [
                'id'         => $review->id,
                'user_name'  => $review->user->name,
                'role_name'  => $review->user->role->name ?? '一般', 
                'rating'     => $review->rating,
                'comment'    => $review->comment,
                'is_owner'   => $review->user_id === Auth::id(),
                
            ];
        });

        // 前回の修正（同期用フラグの返却）と構文修正を維持
        return response()->json([
            'reviews'      => $reviews,
            'has_reviewed' => $book->reviews()->where('user_id', Auth::id())->exists(),
        ]);
    }
}