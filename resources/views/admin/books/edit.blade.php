<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('書籍編集・削除') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
                {{ session('status') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-6">既存の登録書籍データです。内容の修正、削除が可能です。</p>

                <form action="{{ route('admin.books.update', $book->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT') <div>
                        <label class="block text-sm font-medium text-gray-700">ISBN13（変更不可）</label>
                        <input type="text" name="isbn13" value="{{ old('isbn13', $book['isbn13']) }}" readonly
                            class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 text-gray-600 shadow-sm cursor-not-allowed focus:outline-none focus:ring-0 focus:border-gray-300">

                        {{-- 編集画面では基本エラーになりませんが、念のため残す場合はそのままでOK --}}
                        @error('isbn13') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">書籍タイトル <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $book['title']) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">著者名 <span class="text-red-500">*</span></label>
                        <input type="text" name="author" value="{{ old('author', $book['author']) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between items-center pt-4 border-gray-100 mt-6">
                        <a href="{{ route('admin.books.checkForm') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 shadow-sm hover:bg-gray-50">
                            戻る
                        </a>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white shadow-sm hover:bg-indigo-700">
                            書籍情報を更新する
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-red-100 bg-red-50 -mx-6 -mb-6 p-6 rounded-b-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-sm font-bold text-red-800">危険な操作ゾーン</h4>
                            <p class="text-xs text-red-600 m-0 mt-1">この書籍をデータベースから完全に削除します。<br>この書籍のレビューも自動で削除されます。</p>
                        </div>
                        <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('本当にこの書籍情報を完全に削除しますか？\nこの操作は取り消せません。');">
                            @csrf
                            @method('DELETE') <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white shadow-sm hover:bg-red-700">
                                完全に削除する
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>