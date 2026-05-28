{{-- 💡 ステップ1で作った共通レイアウトをベースとして使うよ、という宣言 --}}
@extends('layouts.app')

{{-- 💡 このページのタイトルを指定 --}}
@section('title', '社内書籍管理システム - 書籍一覧')

{{-- 💡 この @section('content') 〜 @endsection の中身が、共通枠の @yield('content') に合体します --}}
@section('content')

    {{-- ■ 書籍検索セクション --}}
    <section class="bg-white p-6 rounded-lg shadow-sm mb-8">
        <h2 class="text-md font-bold text-gray-700 mb-3">■ 書籍検索</h2>
        <form action="{{ route('books.index') }}" method="GET" class="flex gap-2 max-w-xl">
            <input type="text" 
                   name="keyword" 
                   value="{{ request('keyword') }}"
                   placeholder="検索キーワードを入力..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition shadow-sm whitespace-nowrap">
                検索
            </button>
            @if(request('keyword'))
                <a href="{{ route('books.index') }}" 
                   class="text-gray-500 hover:text-gray-700 text-sm font-medium px-2 py-2 transition whitespace-nowrap">
                    クリア
                </a>
            @endif
        </form>
    </section>

    {{-- ■ 書籍一覧セクション --}}
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
                            <td class="px-6 py-4 font-medium">
                                <a href="{{ route('books.showDetail', $book) }}" class="text-blue-600 hover:underline block">
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