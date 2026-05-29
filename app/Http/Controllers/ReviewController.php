<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * レビュー投稿および修正（非同期対応）
     */
    public function storeOrUpdate(Request $request, Book $book)
    {
        $userId = Auth::id();
        
        $validated = $request->validate([
            'review_id' => 'nullable|integer|exists:reviews,id',
            'rating'    => 'required|integer|between:1,5',
            'comment'   => 'required|string|max:1000',
        ]);

        // 【修正モード】
        if (!empty($validated['review_id'])) {
            $review = Review::where('id', $validated['review_id'])
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

        // 【新規投稿モード】
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
     * レビュー削除（非同期対応）
     */
    public function destroy(Review $review)
    {
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
     * 「↓もっと見る」用・リフレッシュ用の全件取得
     */
    public function getAllReviews(Book $book)
    {
        // ユーザーに紐づく権限テーブル(role)もまとめてロード
        $reviews = $book->reviews()->with('user.role')->get()->map(function ($review) {
            return [
                'id'         => $review->id,
                'user_name'  => $review->user->name,
                'role_name'  => $review->user->role->name ?? '一般', // 権限名を取得
                'rating'     => $review->rating,
                'comment'    => $review->comment,
                'is_owner'   => $review->user_id === Auth::id(),
            ];
        });

        return response()->json([
            'reviews' => $reviews,
            'has_reviewed' => $book->reviews()->where('user_id', Auth::id())->exists()
        ]);
    }
}
