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

                {{-- ■ 書籍基本情報（右横に画像が配置されるよう改修） --}}
                <div class="section border border-gray-200 p-5 rounded-lg bg-gray-50">
                    <h3 class="font-bold text-lg mb-4">■ 書籍基本情報</h3>
                    
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="flex-1 space-y-2">
                            <p><strong>・タイトル:</strong> {{ $book->title }}</p>
                            <p><strong>・著者名 :</strong> {{ $book->author }}</p>
                            <p><strong>・ISBN13 :</strong> {{ $book->isbn }}</p>
                        </div>
                        
                        <div class="w-32 md:w-36 flex-shrink-0 bg-white p-2 rounded border border-gray-300 shadow-sm">
                            <img src="https://books.google.com/books/content?id=&vid=ISBN:{{ $book->isbn }}&printsec=frontcover&img=1&zoom=1" 
                                 alt="{{ $book->title }}の表紙" 
                                 class="w-full h-auto object-cover rounded"
                                 onerror="this.onerror=null; this.src='{{ asset('images/no-image.jpg') }}';">
                        </div>
                    </div>
                </div>

                {{-- ■ 社員レビュー一覧 --}}
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

                        <div class="more-btn-area" id="more-btn-area" style="{{ $book->reviews->isEmpty() ? 'display:none;' : '' }}">