<x-app-layout>
    {{-- ページ上部のヘッダー見出しエリア --}}
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $book->title }} - 書籍詳細
            </h2>
            <div>
                <a href="{{ route('books.index') }}" class="text-sm text-gray-600 hover:text-gray-900">[← 一覧に戻る]</a>
            </div>
        </div>
    </x-slot>

    {{-- インラインスタイルの定義 --}}
    <style>
        /* レビュー本文が長すぎる場合にスクロールバーを出す設定 */
        .comment-scroll-box {
            max-height: 150px;    /* 縦の最大高さを150px（およそ5〜6行分）に制限 */
            overflow-y: auto;     /* 150pxを超えたら自動で縦スクロールバーを表示 */
            overflow-x: hidden;   /* 横スクロールは出さずに自動折り返しさせる */
            padding-right: 5px;   /* スクロールバーと文字が被らないように隙間をあける */
            margin-top: 5px;
        }
        .notification-area {
            background: #e0f7fa;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #00acc1;
            display: none;
        }

        .section {
            margin-bottom: 25px;
        }

        .review-box {
            border: 1px solid #ddd;
            padding: 15px;
            background: #fafafa;
        }

        .review-item {
            border-bottom: 1px dashed #ccc;
            padding: 10px 0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .more-btn-area {
            text-align: center;
            margin-top: 15px;
        }

        .more-btn {
            cursor: pointer;
            color: #fff;
            background-color: #6b7280; /* bg-gray-500 */
            border: none;
            font-size: 90%;
            font-weight: bold;
            padding: 6px 16px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .more-btn:hover {
            background-color: #4b5563; /* bg-gray-600 */
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .disabled-form {
            background: #f0f0f0;
            opacity: 0.6;
            pointer-events: none;
            padding: 15px;
            border: 1px solid #ccc;
        }

        .action-btn {
            margin-left: 10px;
            cursor: pointer;
            color: #0066cc;
            text-decoration: underline;
            background: none;
            border: none;
            font-size: 100%;
        }

        .cancel-btn {
            margin-left: 10px;
            cursor: pointer;
            color: #cc0000;
            text-decoration: underline;
            background: none;
            border: none;
        }

        /* 星型選択システム用スタイル */
        .star-rating-input {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 4px;
            margin: 0 10px;
        }

        .star-rating-input span {
            font-size: 24px;
            cursor: pointer;
            color: #ccc;
            transition: color 0.2s;
        }

        .star-rating-input:not(.disabled-stars) span:hover,
        .star-rating-input:not(.disabled-stars) span:hover~span,
        .star-rating-input span.active,
        .star-rating-input span.active~span {
            color: #f5b301;
        }

        .disabled-stars span {
            cursor: default;
        }
    </style>

    {{-- メインコンテンツ --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                    <strong>{{ $review->user->name }} ({{ $review->user->role->name ?? '一般' }})</strong>
                                    (★{{ $review->rating }}) :
                                    <div class="comment-scroll-box">
                                        <span class="comment-text" style="white-space: pre-wrap;">{{ $review->comment }}</span>
                                    </div>

                                    @if($review->user_id === Auth::id())
                                        <button class="action-btn"
                                            onclick="editReview({{ $review->id }}, {{ $review->rating }}, '{{ addslashes($review->comment) }}')">[編集]</button>
                                        <button class="action-btn" onclick="deleteReview({{ $review->id }})">[削除]</button>
                                    @endif
                                </div>
                            @empty
                                <p id="no-review-text">まだレビューはありません。</p>
                            @endforelse
                        </div>

                        {{-- 重複していた箇所を1つの綺麗な構造に整理しました --}}
                        <div class="more-btn-area" id="more-btn-area" style="{{ $book->reviews->isEmpty() ? 'display:none;' : '' }}">
                            <button type="button" id="more-btn" class="more-btn" onclick="toggleReviews()">↓もっと見る</button>
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
                                <div class="star-rating-input {{ $hasReviewed ? 'disabled-stars' : '' }}"
                                    id="star-container">
                                    <span data-value="5" class="active">★</span>
                                    <span data-value="4" class="active">★</span>
                                    <span data-value="3" class="active">★</span>
                                    <span data-value="2" class="active">★</span>
                                    <span data-value="1" class="active">★</span>
                                </div>
                                <span id="rating-display">(5)</span> (1〜5)
                            </div>

                            <div class="form-group">
                                <label class="shrink-0" for="comment">コメント :</label>
                                <textarea id="comment" name="comment" rows="4"
                                    class="border gray-300 rounded ml-2 p-1 text-black w-full max-w-lg block mt-1" {{ $hasReviewed ? 'disabled' : '' }} placeholder="レビューを入力してください"></textarea>
                            </div>

                            <button type="submit" id="submit-btn"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50"
                                {{ $hasReviewed ? 'disabled' : '' }}>
                                [ レビューを投稿する ]
                            </button>
                            <button type="button" id="cancel-btn" class="cancel-btn" onclick="cancelEdit()"
                                style="display: none;">
                                [ 編集をキャンセル ]
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- JavaScript セクション --}}
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const bookId = document.getElementById('book-id').value;
        let userHasReviewed = {{ $hasReviewed ? 'true' : 'false' }};

        // 星型評価のクリック制御
        document.getElementById('star-container').addEventListener('click', function (e) {
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
            notifyArea.textContent = '【' + message + ' 】';
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

            let url = `/books/${bookId}/reviews`;
            let method = 'POST';

            if (reviewId) {
                url = `/reviews/${reviewId}`;
                method = 'PUT';
            }

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ rating: rating, comment: comment })
            })
                .then(res => {
                    if (!res.ok) throw new Error('HTTPエラーが発生しました。');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification(data.message);
                        userHasReviewed = true;
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
                        userHasReviewed = false;
                        loadAllReviews();
                    }
                })
                .catch(err => alert('削除に失敗しました。'));
        }

        // 全件読み込みに関連する変数と関数
        let isAllReviewsShown = false;

        function toggleReviews() {
            if (isAllReviewsShown) {
                location.reload(); // すでに全件表示されていたら、リロードして元に戻す
            } else {
                loadAllReviews();  // まだ表示されていなければ、全件読み込む
            }
        }

        function loadAllReviews() {
    fetch(`/books/${bookId}/reviews-all`)
        .then(res => res.ok ? res.json() : Promise.reject())
        .then(data => {
            const listContainer = document.getElementById('review-list');
            const moreBtnArea = document.getElementById('more-btn-area');
            const moreBtn = document.getElementById('more-btn'); 
            listContainer.innerHTML = '';

            if (data.has_reviewed !== undefined) userHasReviewed = data.has_reviewed;

            if (data.reviews.length === 0) {
                listContainer.innerHTML = '<p id="no-review-text">まだレビューはありません。</p>';
                if (moreBtnArea) moreBtnArea.style.display = 'none';
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
                
                // ★ここです！ comment-scroll-box の div タグをしっかり組み込んでいます
                div.innerHTML = `
                    <strong>${review.user_name} (${review.role_name})</strong> (★${review.rating}) : 
                    <div class="comment-scroll-box">
                        <span class="comment-text" style="white-space: pre-wrap;">${escapeHtml(review.comment)}</span> 
                    </div>
                    ${actionButtons}
                `;
                listContainer.appendChild(div);
            });

            if (moreBtnArea) moreBtnArea.style.display = 'block';
            if (moreBtn) {
                moreBtn.textContent = '↑閉じる';
            }
            isAllReviewsShown = true; 

            resetForm(userHasReviewed);
        })
        .catch(() => alert('レビュー一覧の読み込みに失敗しました。'));
}

        function toggleFormDisabled(isDisabled) {
            const wrapper = document.getElementById('review-form-wrapper');
            const inputs = document.querySelectorAll('#review-form input, #review-form button, #review-form textarea'); // textareaを追加して制御できるように修正
            const starContainer = document.getElementById('star-container');
            if (isDisabled) {
                wrapper.classList.add('disabled-form');
                starContainer.classList.add('disabled-stars');
                inputs.forEach(el => {
                    if (el.id !== 'cancel-btn') el.setAttribute('disabled', 'disabled');
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
</x-app-layout>