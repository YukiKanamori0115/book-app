<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社内書籍管理システム - 書籍一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <header class="bg-blue-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-xl font-bold tracking-wider">📚 社内書籍管理システム</h1>
            <div class="flex items-center gap-4 text-sm bg-blue-950 px-4 py-2 rounded-md">
                <span>[ユーザー名: 山田太郎(一般)]</span>
                <button class="text-red-300 hover:text-red-400 font-medium transition">[ログアウト]</button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-md shadow-sm">
            <div class="flex items-center gap-2 text-yellow-800 text-sm font-semibold">
                <span>[※経理部のみ: 書籍管理]</span>
                </div>
        </div>

        <section class="bg-white p-6 rounded-lg shadow-sm mb-8">
            <h2 class="text-md font-bold text-gray-700 mb-3">■ 書籍検索</h2>
            <form action="{{ route('books.index') }}" method="GET" class="flex gap-2 max-w-md">
                <input  type="text" 
                        name="keyword" 
                        value="{{ request('keyword') }}" 
                        placeholder="検索キーワードを入力..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-black">
    
                <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm font-medium whitespace-nowrap">
                    検索
                </button>
            </form>

        <section class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-md font-bold text-gray-700">■ 書籍一覧 (全 {{ $books->count() }} 冊)</h2>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-bold">
                        <tr>
                            <th class="px-6 py-3 text-left">書名 (クリックで詳細)</th>
                            <th class="px-6 py-3 text-left">ISBN13</th>
                            <th class="px-6 py-3 text-left">著者</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($books as $book)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-blue-600 hover:underline cursor-pointer">
                                    <a href="{{ route('books.show', $book->id) }}" class="text-blue-600 hover:underline block w-full h-full">
                                        {{ $book->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-mono">{{ $book->isbn13 }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $book->author }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-400 italic">
                                    登録されている本はありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>
</html>