<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $book->title }} - 書籍詳細</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.6; }
        .header-area { border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; }
        .notification-area { background: #e0f7fa; padding: 10px; margin-bottom: 20px; border-left: 5px solid #00acc1; display: none; }
        .section { margin-bottom: 25px; }
        .review-box { border: 1px solid #ddd; padding: 15px; background: #fafafa; }
        .review-item { border-bottom: 1px dashed #ccc; padding: 10px 0; }
        .review-item:last-child { border-bottom: none; }
        .more-btn-area { text-align: center; margin-top: 10px; }
        .more-btn { cursor: pointer; color: #555; background: none; border: none; font-size: 90%; font-weight: bold; }
        .form-group { margin-bottom: 15px; display: flex; align-items: center; }
        .disabled-form { background: #f0f0f0; opacity: 0.6; pointer-events: none; padding: 15px; border: 1px solid #ccc; }
        .action-btn { margin-left: 10px; cursor: pointer; color: #0066cc; text-decoration: underline; background: none; border: none; font-size: 100%; }
        
        /* ★ 星型選択システム用スタイル */
        .star-rating-input { display: inline-flex; flex-direction: row-reverse; gap: 4px; margin: 0 10px; }
        .star-rating-input span { font-size: 24px; cursor: pointer; color: #ccc; transition: color 0.2s; }
        .star-rating-input:not(.disabled-stars) span:hover,
        .star-rating-input:not(.disabled-stars) span:hover ~ span,
        .star-rating-input span.active,
        .star-rating-input span.active ~ span { color: #f5b301; }
        .disabled-stars span { cursor: default; }
    </style>
</head>
<body>

    <!-- ヘッダーエリア -->
    <div class="header-area">
        <div><a href="{{ route('books.index') }}">[← 一覧に戻る]</a></div>
        <div>
            <span>[ユーザー名: {{ Auth::user()->name }}({{ Auth::user()->role->name ?? '一般' }})]</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn">[ログアウト]</button>
            </form>
        </div>
    </div>

    <!-- 通知エリア -->
    <div id="notification" class="notification-area"></div>

    <!-- ■ 書籍基本情報 -->
    <div class="section">
        <h3>■ 書籍基本情報</h3>
        <ul>
            <li><strong>タイトル:</strong> {{ $book->title }}</li>
            <li><strong>著者名 :</strong> {{ $book->author }}</li>
            <li><strong>ISBN13 :</strong> {{ $book->isbn13 }}</li>
        </ul>
    </div>

    <!-- ■ 社員レビュー一覧 -->
    <div class="section">
        <h3>■ 社員レビュー一覧</h3>
        <div class="review-box">
            <div id="review-list">
                @forelse($book->reviews as $review)
                    <div class="review-item" id="review-{{ $review->id }}">
                        <strong>{{ $review->user->name }} ({{ $review->user->role->name ?? '一般' }})</strong> (★{{ $review->rating }}) : 
                        <span class="comment-text">{{ $review->comment }}</span>
                        
                        @if($review->user_id === Auth::id())
                            <button class="action-btn" onclick="editReview({{ $review->id }}, {{ $review->rating }}, '{{ e($review->comment) }}')">[編集]</button>
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

    <!-- ■ レビューを投稿する -->
    <div class="section">
        <h3>■ レビューを投稿する</h3>
        
        @php $hasReviewed = $book->reviews->contains('user_id', Auth::id()); @endphp
        
        <div id="review-form-wrapper" class="{{ $hasReviewed ? 'disabled-form' : '' }}">
            <p><small>[※未投稿時のみ入力可 / 投稿済時は以下フォームがグレーアウト]</small></p>
            
            <form id="review-form">
                <input type="hidden" id="book-id" value="{{ $book->id }}">
                <input type="hidden" id="editing-review-id" value="">
                <input type="hidden" id="rating" name="rating" value="5">

                <div class="form-group">
                    <label>おすすめ度:</label>
                    <div class="star-rating-input {{ $hasReviewed ? 'disabled-stars' : '' }}" id="star-container">
                        <span data-value="5" class="active">★</span>
                        <span data-value="4" class="active">★</span>
                        <span data-value="3" class="active">★</span>
                        <span data-value="2" class="active">★</span>
                        <span data-value="1" class="active">★</span>
                    </div>
                    <span id="rating-display">(5)</span> (1〜5)
                </div>

                <div class="form-group">
                    <label for="comment">コメント :</label>
                    <input type="text" id="comment" name="comment" size="50" {{ $hasReviewed ? 'disabled' : '' }}>
                </div>

                <button type="button" id="submit-btn" onclick="submitReview()" {{ $hasReviewed ? 'disabled' : '' }}>
                    [ レビューを投稿する ]
                </button>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const bookId = document.getElementById('book-id').value;

        // 星型評価のクリック制御
        document.getElementById('star-container').addEventListener('click', function(e) {
            if (this.classList.contains('disabled-stars')) return;
            if (e.target.tagName === 'SPAN') {
                const value = parseInt(e.target.getAttribute('data-value'));
                setStarRating(value);
            }
        });

        function setStarRating(value) {
            document.getElementById('rating').value = value;
            document.getElementById('rating-display').textContent = `(${value})`;
            const stars = document.querySelectorAll('#star-container span');
            stars.forEach(star => {
                const starVal = parseInt(star.getAttribute('data-value'));
                if (starVal <= value) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        // 通知処理
        function showNotification(message) {
            const notifyArea = document.getElementById('notification');
            notifyArea.textContent = '【通知エリア: ' + message + ' (非同期表示)】';
            notifyArea.style.display = 'block';
            setTimeout(() => { notifyArea.style.display = 'none'; }, 5000);
        }

        // 送信
        function submitReview() {
            const reviewId = document.getElementById('editing-review-id').value;
            const rating = document.getElementById('rating').value;
            const comment = document.getElementById('comment').value;

            if (!comment.trim()) {
                alert('コメントを入力してください。');
                return;
            }

            fetch(`/books/${bookId}/reviews`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ review_id: reviewId, rating: rating, comment: comment })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message);
                    loadAllReviews();
                    resetForm(true); 
                } else if (data.error) {
                    alert(data.error);
                }
            });
        }

        // 編集
        function editReview(id, rating, comment) {
            document.getElementById('editing-review-id').value = id;
            document.getElementById('comment').value = comment;
            setStarRating(rating);
            toggleFormDisabled(false); 
            document.getElementById('submit-btn').textContent = '[ レビューを修正する ]';
            window.scrollTo({ top: document.getElementById('review-form-wrapper').offsetTop, behavior: 'smooth' });
        }

        // 削除
        function deleteReview(id) {
            if (!confirm('本当に削除しますか？')) return;

            fetch(`/reviews/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message);
                    loadAllReviews();
                    resetForm(false); 
                }
            });
        }

        // 全件読み込み
        function loadAllReviews() {
            fetch(`/books/${bookId}/reviews-all`)
            .then(res => res.json())
            .then(data => {
                const listContainer = document.getElementById('review-list');
                const moreBtnArea = document.getElementById('more-btn-area');
                listContainer.innerHTML = '';

                if (data.reviews.length === 0) {
                    listContainer.innerHTML = '<p id="no-review-text">まだレビューはありません。</p>';
                    moreBtnArea.style.display = 'none';
                    return;
                }

                data.reviews.forEach(review => {
                let actionButtonsContainer = document.createDocumentFragment();

    if (review.is_owner) {
        // 編集ボタンの作成
        const editBtn = document.createElement('button');
        editBtn.className = 'action-btn';
        editBtn.textContent = '[編集]';
        editBtn.addEventListener('click', () => {
            editReview(review.id, review.rating, review.comment);
        });

        // 削除ボタンの作成
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'action-btn';
        deleteBtn.textContent = '[削除]';
        deleteBtn.addEventListener('click', () => {
            deleteReview(review.id);
        });

        actionButtonsContainer.appendChild(editBtn);
        // ボタンの間にスペースを挟む
        actionButtonsContainer.appendChild(document.createTextNode(' ')); 
        actionButtonsContainer.appendChild(deleteBtn);
    }

    // レビューアイテム（親要素）の作成
    const div = document.createElement('div');
    div.className = 'review-item';
    div.id = `review-${review.id}`;

    // テキスト部分の作成（textContent を使うことで自動エスケープされ安全）
    const infoSpan = document.createElement('span');
    infoSpan.innerHTML = `<strong>${escapeHtml(review.user_name)} (${escapeHtml(review.role_name)})</strong> (★${review.rating}) : `;

    const commentSpan = document.createElement('span');
    commentSpan.className = 'comment-text';
    commentSpan.textContent = review.comment; // HTMLエスケープ不要で安全

    // 要素を組み立てて画面に追加
    div.appendChild(infoSpan);
    div.appendChild(commentSpan);
    div.appendChild(document.createTextNode(' ')); // 隙間用
    div.appendChild(actionButtonsContainer);
    
    listContainer.appendChild(div);
});

// ボタンエリアの非表示（元のコードの文脈維持）
moreBtnArea.style.display = 'none';
// }); } ※元のコードで閉じタグが乱れていたため、関数の終端に合わせて調整してください


/**
 * フォームの無効化 / 有効化 切り替え
 */
function toggleFormDisabled(isDisabled) {
    const wrapper = document.getElementById('review-form-wrapper');
    const starContainer = document.getElementById('star-container');
    const inputs = document.querySelectorAll('#review-form input, #review-form button');

    if (isDisabled) {
        wrapper.classList.add('disabled-form');
        starContainer.classList.add('disabled-stars');
        inputs.forEach(el => el.setAttribute('disabled', 'disabled'));
    } else {
        wrapper.classList.add('disabled-form'); // バグ修正: remove に変更してください
        wrapper.classList.remove('disabled-form');
        starContainer.classList.remove('disabled-stars');
        inputs.forEach(el => el.removeAttribute('disabled'));
    }
}

/**
 * フォームのリセット
 */
function resetForm(shouldDisable) {
    document.getElementById('review-form').reset();
    document.getElementById('editing-review-id').value = '';
    setStarRating(5);
    document.getElementById('submit-btn').textContent = '[ レビューを投稿する ]';
    toggleFormDisabled(shouldDisable);
}

/**
 * HTMLのエスケープ処理
 */
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}