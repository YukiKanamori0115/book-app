<x-app-layout>
    <x-slot name="title">書籍管理</x-slot>

    {{-- セッションメッセージ --}}
    @if (session('status'))
    <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
        {{ session('status') }}
    </div>
    @endif

    @if (session('error'))
    <div class="mb-4 text-sm font-medium text-red-600 bg-red-50 p-3 rounded-md border border-red-200">
        {{ session('error') }}
    </div>
    @endif

    {{-- 書籍管理（ISBN確認）セクション --}}
    <section class="bg-white p-6 rounded-lg shadow-sm">
        <div class="mb-4">
            <h2 class="text-md font-bold text-gray-700">■ {{ __('書籍管理') }}</h2>
        </div>

        <form action="{{ route('admin.books.checkIsbn') }}" method="POST" class="flex flex-col md:flex-row gap-4 max-w-2xl">
            @csrf

            {{-- ISBN13コード入力フィールド --}}
            <div class="flex-1">
                <input type="text" id="isbn13" name="isbn13" maxlength="17" value="{{ old('isbn13') }}"
                    placeholder="978-4-06-123456-7 または 9784061234567" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono">

                @error('isbn13')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- ボタンのスタイルも一覧の「検索」にそろえる --}}
            <button type="submit"
                class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm font-medium whitespace-nowrap h-[38px] md:h-auto">
                確認する
            </button>
        </form>
    </section>
</x-app-layout>