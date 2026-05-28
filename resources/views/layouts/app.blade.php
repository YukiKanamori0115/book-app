<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '社内書籍管理システム')</title>
    {{-- 💡 非常に重要：このTailwind CSSの読み込みが抜けていると、アイコンが画面いっぱいに巨大化します！ --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    {{-- 🌟 共通ヘッダー --}}
    <header class="bg-blue-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            
            {{-- 💡 左側：ロゴ＆タイトル --}}
            <h1 class="text-2xl font-bold tracking-wider flex items-center gap-3">
                
                {{-- 🎨 サイズは同じく「w-6 h-6（24px）」、色を鮮やかな黄色に！ --}}
                <a href="{{ route('books.index') }}" class="text-yellow-300 hover:text-yellow-100 transition shrink-0" title="書籍一覧へ">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                    </svg>
                </a>

                <span class="text-white select-none">社内書籍管理システム</span>
            </h1>

            {{-- 💡 右側：ユーザーメニュー --}}
            <div class="flex items-center gap-4 text-sm bg-blue-950 px-4 py-2 rounded-md">
                
                {{-- ⚙️ 経理部のみの管理画面リンク --}}
                @if(true)
                    <a href="{{ route('books.admin') }}" class="text-yellow-300 hover:text-yellow-400 font-medium transition flex items-center gap-1">
                        <span>⚙️ 書籍管理画面へ</span>
                    </a>
                @endif

                {{-- 👤 ユーザー名 --}}
                <span class="text-gray-200">山田太郎(一般)</span>
                
                {{-- 🚪 ログアウト --}}
                <a href="#" 
                   class="text-red-300 hover:text-red-400 font-medium transition"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    ログアウト
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

        </div>
    </header>

    {{-- 🌟 各ページの中身（indexやshowDetail）がはめ込まれる場所 --}}
    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

</body>
</html>