@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Tambah Pengguna Baru</h1>

    {{-- Tampilkan pesan error --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
            <strong>Terjadi kesalahan:</strong>
            <ul class="list-disc ml-4 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('akun-pengguna.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                   required>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                   required>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" id="password"
                   class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                   required>
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" id="role"
                    class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                    required>
                <option value="">-- Pilih Role --</option>
                <option value="superadmin" @selected(old('role') === 'superadmin')>Superadmin</option>
                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                <option value="staff" @selected(old('role') === 'staff')>Staff</option>
            </select>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('akun-pengguna.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md mr-2">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm text-white bg-sky-600 hover:bg-sky-700 rounded-md shadow">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
