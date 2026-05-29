<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社内書籍管理システム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js (Breezeのドロップダウン動作に必須) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    {{-- 🌟 紺色ヘッダー (ドロップダウン統合版) --}}
    <header class="bg-blue-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wider">📚 社内書籍管理システム</h1>

            {{-- ユーザーメニュー (ドロップダウン) --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 bg-blue-950 px-4 py-2 rounded-md text-sm hover:bg-blue-800 transition">
                    <span>{{ Auth::user()->name ?? 'ユーザー' }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                {{-- ドロップダウンの中身 --}}
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg py-1 z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">プロフィール</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">ログアウト</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- 🌟 メインコンテンツ --}}
    <main class="max-w-7xl mx-auto px-4 py-8">
        {{-- 警告エリア --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-md shadow-sm">
            <div class="flex items-center gap-2 text-yellow-800 text-sm font-semibold">
                <span>[※経理部のみ: 書籍管理]</span>
            </div>
        </div>

        {{-- ここに各ページの中身がはめ込まれます --}}
        {{ $slot }}
    </main>

</body>
</html>
