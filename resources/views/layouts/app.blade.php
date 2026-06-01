<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社内書籍管理システム</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <header class="bg-blue-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="{{ route('books.index') }}" class="hover:opacity-80 transition">
                <h1 class="text-xl font-bold tracking-wider">📚 社内書籍管理システム</h1>
            </a>
            @auth
                <div class="flex items-center gap-4 text-sm bg-blue-950 px-4 py-2 rounded-md">
                    <span>[ユーザー名: {{ Auth::user()->name }} ({{ Auth::user()->role->name }})]</span>
                    @can('is-accounting')
                        <a href="{{ route('admin.books.checkForm') }}" class="text-yellow-300 hover:text-yellow-400 font-medium transition underline underline-offset-4">
                            [書籍管理画面]
                        </a>
                    @endcan
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="text-red-300 hover:text-red-400 font-medium transition">
                            [ログアウト]
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

</body>
</html>