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
        .cancel-btn { margin-left: 10px; cursor: pointer; color: #cc0000; text-decoration: underline; background: none; border: none; }
        
        /* 星型選択システム用スタイル */
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

    <div id="notification" class="notification-area"></div>

    <div class="section">
        <h3>■ 書籍基本情報</h3>
        <ul>
            <li><strong>タイトル:</strong> {{ $book->title }}</li>
            <li><strong>著者名 :</strong> {{ $book->author }}</li>
            <li><strong>ISBN13 :</strong> {{ $book->isbn13 }}</li>
        </ul>
    </div>

    <div class="section">
        <h3>■ 社員レビュー一覧</h3>
        <div class="review-box">
            <div id="review-list">
                @forelse($book->reviews as $review)
                    <div class="review-item" id="review-{{ $review->id }}">
                        <strong>{{ $review->user->name }} ({{ $review->user->role->name ?? '一般' }})</strong> (★{{ $review->rating }}) : 
                        <span class="comment-text">{{ $review->comment }}</span>
                        
                        @if($review->user_id === Auth::id())
                            <button class="action-btn" onclick="editReview({{ $review->id }}, {{ $review->rating }}, @json($review->comment))">[編集]</button>
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
        <h3>■ レビューを投稿する</h3>
        
        @php $hasReviewed = $book->reviews->contains('user_id', Auth::id()); @endphp
        
        <div id="review-form-wrapper" class="{{ $hasReviewed ? 'disabled-form' : '' }}">
            <p><small>[※未投稿時のみ入力可 / 投稿済時は以下フォームがグレーアウト]</small></p>
            
            <form id="review-form" onsubmit="event.preventDefault(); submitReview();">
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

                <button type="submit" id="submit-btn" {{ $hasReviewed ? 'disabled' : '' }}>
                    [ レビューを投稿する ]
                </button>
                <button type="button" id="cancel-btn" class="cancel-btn" onclick="cancelEdit()" style="display: none;">
                    [ 編集をキャンセル ]
                </button>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const bookId = document.getElementById('book-id').value;
        let userHasReviewed = {{ $hasReviewed ? 'true' : 'false' }}; // 【改善】現在の状態をスクリプトで管理

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
            .then(res => {
                if (!res.ok) throw new Error('HTTPエラーが発生しました。');
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification(data.message);
                    userHasReviewed = true; // 投稿成功なので状態を「投稿済」に
                    loadAllReviews();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(err => alert('送信に失敗しました。時間を置いて再度お試しください。'));
        }

        // 編集モード開始
        function editReview(id, rating, comment) {
            document.getElementById('editing-review-id').value = id;
            document.getElementById('comment').value = comment;
            setStarRating(rating);
            toggleFormDisabled(false); 
            document.getElementById('submit-btn').textContent = '[ レビューを修正する ]';
            document.getElementById('cancel-btn').style.display = 'inline';
            window.scrollTo({ top: document.getElementById('review-form-wrapper').offsetTop, behavior: 'smooth' });
        }

        // 編集キャンセル
        function cancelEdit() {
            resetForm(userHasReviewed); 
        }

        // 削除
        function deleteReview(id) {
            if (!confirm('本当に削除しますか？')) return;

            fetch(`/reviews/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTPエラーが発生しました。');
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification(data.message);
                    userHasReviewed = false; // 削除成功なので状態を「未投稿」に
                    loadAllReviews();
                }
            })
            .catch(err => alert('削除に失敗しました。'));
        }

        // 全件読み込み
        function loadAllReviews() {
            fetch(`/books/${bookId}/reviews-all`)
            .then(res => res.ok ? res.json() : Promise.reject())
            .then(data => {
                const listContainer = document.getElementById('review-list');
                const moreBtnArea = document.getElementById('more-btn-area');
                listContainer.innerHTML = '';

                // 【バックエンド連携の推奨】もしAPI側でhas_reviewedを返せるならここで同期するとより堅牢になります
                if (data.has_reviewed !== undefined) userHasReviewed = data.has_reviewed;

                if (data.reviews.length === 0) {
                    listContainer.innerHTML = '<p id="no-review-text">まだレビューはありません。</p>';
                    moreBtnArea.style.display = 'none';
                    resetForm(userHasReviewed);
                    return;
                }

                data.reviews.forEach(review => {
                    let actionButtons = '';
                    if (review.is_owner) {
                        actionButtons = `
                            <button class="action-btn" onclick="editReview(${review.id}, ${review.rating}, '${escapeJsString(review.comment)}')">[編集]</button> 
                            <button class="action-btn" onclick="deleteReview(${review.id})">[削除]</button>
                        `;
                    }
                    const div = document.createElement('div');
                    div.className = 'review-item';
                    div.id = `review-${review.id}`;
                    div.innerHTML = `
                        <strong>${review.user_name} (${review.role_name})</strong> (★${review.rating}) : 
                        <span class="comment-text">${escapeHtml(review.comment)}</span> 
                        ${actionButtons}
                    `;
                    listContainer.appendChild(div);
                });
                moreBtnArea.style.display = 'none';
                
                // リスト更新に伴い、現在の状態（userHasReviewed）にフォームをリセット
                resetForm(userHasReviewed);
            })
            .catch(() => alert('レビュー一覧の読み込みに失敗しました。'));
        }

        function toggleFormDisabled(isDisabled) {
            const wrapper = document.getElementById('review-form-wrapper');
            const inputs = document.querySelectorAll('#review-form input, #review-form button');
            const starContainer = document.getElementById('star-container');
            if (isDisabled) {
                wrapper.classList.add('disabled-form');
                starContainer.classList.add('disabled-stars');
                inputs.forEach(el => {
                    if(el.id !== 'cancel-btn') el.setAttribute('disabled', 'disabled');
                });
            } else {
                wrapper.classList.remove('disabled-form');
                starContainer.classList.remove('disabled-stars');
                inputs.forEach(el => el.removeAttribute('disabled'));
            }
        }

        function resetForm(shouldDisable) {
            document.getElementById('review-form').reset();
            document.getElementById('editing-review-id').value = '';
            setStarRating(5);
            document.getElementById('submit-btn').textContent = '[ レビューを投稿する ]';
            document.getElementById('cancel-btn').style.display = 'none';
            toggleFormDisabled(shouldDisable);
        }

        function escapeHtml(str) {
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeJsString(str) {
            return str
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")
                .replace(/"/g, '\\"')
                .replace(/\n/g, '\\n')
                .replace(/\r/g, '\\r');
        }
    </script>
</body>
</html>