@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')

@php
    $user = Auth::user();
@endphp

<div class="max-w-2xl mx-auto bg-white shadow p-6 rounded-md">
    <h2 class="text-xl font-semibold mb-4">⚙️ Pengaturan Akun</h2>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi error --}}
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validasi error --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('akun-pengguna.settings.update') }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="role" value="{{ $user->role }}">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" 
                @if($user->role !== 'superadmin') readonly @endif
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Password Baru</label>
            <input type="password" name="password"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-white hover:bg-sky-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
