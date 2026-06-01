<x-app-layout>
    {{-- タイトルが必要な場合はここにも書けますし、必要なければ省略も可能です --}}
    
    {{-- 書籍検索セクション --}}
    <section class="bg-white p-6 rounded-lg shadow-sm mb-8">
        <h2 class="text-md font-bold text-gray-700 mb-3">■ 書籍検索</h2>
        <form action="{{ route('books.index') }}" method="GET" class="flex gap-2 max-w-md">
            <input type="text" name="keyword" value="{{ request('keyword') }}" 
                   placeholder="検索キーワードを入力..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-black">
    
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm font-medium whitespace-nowrap">
                検索
            </button>
        </form>
    </section>

    {{-- 書籍一覧セクション --}}
    <section class="bg-white p-6 rounded-lg shadow-sm">
        <div class="mb-4">
            <h2 class="text-md font-bold text-gray-700">■ 書籍一覧 (全 {{ $books->count() }} 冊)</h2>
        </div>
        
        {{-- テーブルの中身などはそのまま --}}
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                {{-- ...以下略... --}}
            </table>
        </div>
    </section>
</x-app-layout>