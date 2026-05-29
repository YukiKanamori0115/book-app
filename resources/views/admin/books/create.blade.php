<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('書籍マスタ登録確認') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-sm text-gray-600 mb-6">openBDから取得した情報です。内容を確認し、修正があれば書き換えて登録してください。</p>

                <form action="{{ route('admin.books.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">ISBN13（変更不可）</label>
                        <input type="text" name="isbn13" value="{{ old('isbn13', $book['isbn13']) }}" readonly
                            class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 text-gray-600 shadow-sm">
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

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('admin.books.checkForm') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 shadow-sm hover:bg-gray-50">
                            戻る
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white shadow-sm hover:bg-blue-700">
                            この内容でマスタ登録する
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>