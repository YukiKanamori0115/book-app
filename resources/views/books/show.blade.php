<x-app-layout>
    <x-slot name="title">書籍詳細</x-slot>

    {{-- ヘッダーエリア --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }} - 書籍詳細
        </h2>
        <div>
            <a href="{{ route('books.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">[← 一覧に戻る]</a>
        </div>
    </div>

    {{-- メインコンテンツのコンテナ --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
        
        <div id="notification" class="notification-area"></div>

        <div class="section">
            <h3 class="font-bold text-lg mb-2">■ 書籍基本情報</h3>
            <ul class="list-disc pl-5">
                <li><strong>タイトル:</strong> {{ $book->title }}</li>
                <li><strong>著者名 :</strong> {{ $book->author }}</li>
                <li><strong>ISBN13 :</strong> {{ $book->isbn13 }}</li>
            </ul>
        </div>

        <div class="section">
            <h3 class="font-bold text-lg mb-2">■ 社員レビュー一覧</h3>
            <div class="review-box">
                <div id="review-list">
                    @forelse($book->reviews as $review)
                        <div class="review-item" id="review-{{ $review->id }}">
                            <strong>{{ $review->user->name }} ({{ $review->user->role->name ?? '一般' }})</strong> (★{{ $review->rating }}) : 
                            <span class="comment-text">{{ $review->comment }}</span>
                            @if($review->user_id === Auth::id())
                                <button class="action-btn" onclick="editReview({{ $review->id }}, {{ $review->rating }}, '{{ addslashes($review->comment) }}')">[編集]</button>
                                <button class="action-btn" onclick="deleteReview({{ $review->id }})">[削除]</button>
                            @endif
                        </div>
                    @empty
                        <p id="no-review-text">まだレビューはありません。</p>
                    @endforelse
                </div>
                <div class="more-btn-area" id="more-btn-area" style="{{ $book->reviews->isEmpty() ? 'display:none;' : '' }}">
                    <button type="button" class="more-btn" onclick="loadAllReviews()">↓もっと見る</button>
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="font-bold text-lg mb-2">■ レビューを投稿する</h3>
            @php $hasReviewed = $book->reviews->contains('user_id', Auth::id()); @endphp
            <div id="review-form-wrapper" class="{{ $hasReviewed ? 'disabled-form' : '' }}">
                <form id="review-form" onsubmit="event.preventDefault(); submitReview();">
                    <input type="hidden" id="book-id" value="{{ $book->id }}">
                    <input type="hidden" id="editing-review-id" value="">
                    <input type="hidden" id="rating" name="rating" value="5">
                    <div class="form-group">
                        <label>おすすめ度:</label>
                        <div class="star-rating-input {{ $hasReviewed ? 'disabled-stars' : '' }}" id="star-container">
                            @for($i=5; $i>=1; $i--)
                                <span data-value="{{$i}}" class="active">★</span>
                            @endfor
                        </div>
                        <span id="rating-display">(5)</span>
                    </div>
                    <div class="form-group">
                        <label for="comment">コメント :</label>
                        <input type="text" id="comment" name="comment" size="50" class="border gray-300 rounded ml-2 p-1 text-black" {{ $hasReviewed ? 'disabled' : '' }}>
                    </div>
                    <button type="submit" id="submit-btn" class="bg-blue-500 text-white px-4 py-2 rounded">投稿</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .notification-area { background: #e0f7fa; padding: 10px; margin-bottom: 20px; border-left: 5px solid #00acc1; display: none; }
        .section { margin-bottom: 25px; }
        .review-box { border: 1px solid #ddd; padding: 15px; background: #fafafa; }
        .review-item { border-bottom: 1px dashed #ccc; padding: 10px 0; }
        .action-btn { color: #0066cc; text-decoration: underline; background: none; border: none; margin-left: 5px; }
        .star-rating-input span { cursor: pointer; color: #ccc; }
        .star-rating-input span.active { color: #f5b301; }
        .disabled-form { opacity: 0.6; pointer-events: none; }
    </style>
    
    <script>
        // (以前のJSコードをここに配置)
    </script>
</x-app-layout>