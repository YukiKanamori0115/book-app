<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社内書籍管理システム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <header class="bg-blue-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="{{ route('books.index') }}" class="hover:opacity-80 transition">
                <h1 class="text-xl font-bold tracking-wider">📚 社内書籍管理システム</h1>
            </a>
            
            @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" 
                            class="flex items-center gap-2 bg-blue-950 px-4 py-2 rounded-md hover:bg-blue-800 transition text-sm">
                        <span>{{ Auth::user()->name }} ({{ Auth::user()->role->name }})</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" style="display: none;" 
                         class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-xl py-1 z-50 border border-gray-100">
                        @can('is-accounting')
                            <a href="{{ route('admin.books.checkForm') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">⚙️ 書籍管理画面</a>
                        @endcan
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">🚪 ログアウト</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        {{-- フラッシュメッセージ --}}
        @if (session('info'))
            <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        {{ $slot }}
    </main>

</body>
</html>