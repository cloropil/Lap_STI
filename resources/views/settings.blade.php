@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="flex justify-center items-center min-h-[calc(100vh-200px)] px-4">
    <div class="w-full max-w-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pengaturan Tema</h2>

        <form method="POST" action="{{ route('settings.toggleTheme') }}" class="space-y-4">
            @csrf
            <div>
                <label for="theme_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Pilih Mode:
                </label>
                <select id="theme_mode" name="theme_mode"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="light" {{ session('theme_mode') == 'light' ? 'selected' : '' }}>Terang</option>
                    <option value="dark" {{ session('theme_mode') == 'dark' ? 'selected' : '' }}>Gelap</option>
                </select>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
