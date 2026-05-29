<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('書籍マスタ管理（ISBN確認）') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('error'))
                <div class="mb-4 text-sm font-medium text-red-600">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('admin.books.checkIsbn') }}" method="POST" class="max-w-md">
                    @csrf
                    <div class="mb-6">
                        <label for="isbn13" class="block text-sm font-medium text-gray-700 mb-2">ISBN13コード（半角数字13桁）</label>
                        <div class="flex gap-2">
                            <input type="text" id="isbn13" name="isbn13" maxlength="13" value="{{ old('isbn13') }}" placeholder="9784798165882" required
                                class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 flex-1">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                確認する
                            </button>
                        </div>
                        @error('isbn13')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>