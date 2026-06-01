@extends('layouts.app')

@section('title', '書籍一覧')

@section('content')
    {{-- 書籍検索セクション --}}
    <section class="bg-white p-6 rounded-lg shadow-sm mb-8">
        <h2 class="text-md font-bold text-gray-700 mb-3">■ 書籍検索</h2>
        <form action="{{ route('books.index') }}" method="GET" class="flex gap-2 max-w-md">
            <input type="text" 
                   name="keyword" 
                   value="{{ request('keyword') }}" 
                   placeholder="検索キーワードを入力..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-black">
    
            <button type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm font-medium whitespace-nowrap">
                検索
            </button>
        </form>
    </section>

    {{-- 書籍一覧セクション --}}
    <section class="bg-white p-6 rounded-lg shadow-sm">
        <div class="mb-4">
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
@endsection