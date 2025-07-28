@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div x-data="{ openId: null, notif: { show: false, message: '', type: 'success' } }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

    {{-- Notifikasi ringan --}}
    <div 
        x-show="notif.show"
        x-transition
        x-init="$watch('notif.show', val => val && setTimeout(() => notif.show = false, 3000))"
        :class="notif.type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
        class="fixed top-5 right-5 px-4 py-2 rounded shadow text-sm z-50"
        x-text="notif.message"
        style="display: none;"
    ></div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500">Kelola akun pengguna dan perannya.</p>
        </div>
        <a href="{{ route('akun-pengguna.create') }}"
           class="inline-block bg-sky-600 hover:bg-sky-700 text-white text-sm px-4 py-2 rounded-md shadow">
            Tambah Pengguna
        </a>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-sky-50 border border-sky-200 rounded-md p-4">
            <p class="text-sm text-gray-600">Superadmin</p>
            <p class="text-xl font-semibold text-sky-700">{{ $countSuperadmin ?? 0 }}</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <p class="text-sm text-gray-600">Admin</p>
            <p class="text-xl font-semibold text-yellow-700">{{ $countAdmin ?? 0 }}</p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
            <p class="text-sm text-gray-600">Staff</p>
            <p class="text-xl font-semibold text-gray-700">{{ $countStaff ?? 0 }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-md shadow border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium">Kelola Pengguna</th>
                    <th class="px-4 py-3 font-medium">Kelola Data</th>
                    <th class="px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($akun as $pengguna)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-800">{{ $pengguna->name }}</td>
                    <td class="px-4 py-2 text-gray-800">{{ $pengguna->email }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 text-xs font-medium rounded 
                            {{ $pengguna->role === 'superadmin' ? 'bg-sky-100 text-sky-700' : ($pengguna->role === 'admin' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($pengguna->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ $pengguna->can_manage_users ? 'Ya' : 'Tidak' }}
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ $pengguna->can_manage_data ? 'Ya' : 'Tidak' }}
                    </td>
                    <td class="px-4 py-2 space-x-3">
                        <button 
                            class="text-sky-600 hover:underline text-sm"
                            @click="openId = openId === {{ $pengguna->id }} ? null : {{ $pengguna->id }}"
                        >Edit</button>

                        @if(Auth::id() !== $pengguna->id)
                        <form action="{{ route('akun-pengguna.destroy', $pengguna->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-sm">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>

                {{-- Form Edit Inline --}}
                <tr x-show="openId === {{ $pengguna->id }}" class="bg-gray-50 border-t" style="display: none;">
                    <td colspan="6" class="p-4">
                        <form method="POST" action="{{ route('akun-pengguna.update', $pengguna->id) }}" @submit.prevent="
                            fetch($el.action, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                body: new FormData($el)
                            }).then(res => {
                                if (!res.ok) throw new Error('Gagal memperbarui data.');
                                notif.message = 'Data berhasil diperbarui.';
                                notif.type = 'success';
                                notif.show = true;
                                openId = null;
                                window.location.reload();
                            }).catch(err => {
                                notif.message = err.message;
                                notif.type = 'error';
                                notif.show = true;
                            });
                        " class="space-y-4">

                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Nama</label>
                                    <input type="text" name="name" value="{{ old('name', $pengguna->name) }}"
                                           class="w-full border-gray-300 rounded text-sm focus:ring-sky-500 focus:border-sky-500">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $pengguna->email) }}"
                                           class="w-full border-gray-300 rounded text-sm focus:ring-sky-500 focus:border-sky-500">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Role</label>
                                    <select name="role" class="w-full border-gray-300 rounded text-sm focus:ring-sky-500 focus:border-sky-500">
                                        <option value="superadmin" @selected($pengguna->role === 'superadmin')>Superadmin</option>
                                        <option value="admin" @selected($pengguna->role === 'admin')>Admin</option>
                                        <option value="staff" @selected($pengguna->role === 'staff')>Staff</option>
                                    </select>
                                </div>
                                <div class="flex flex-col justify-center gap-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="can_manage_users" value="1" {{ $pengguna->can_manage_users ? 'checked' : '' }} class="text-blue-600 rounded focus:ring-0">
                                        <span class="ml-2 text-sm text-gray-700">Kelola Pengguna</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="can_manage_data" value="1" {{ $pengguna->can_manage_data ? 'checked' : '' }} class="text-blue-600 rounded focus:ring-0">
                                        <span class="ml-2 text-sm text-gray-700">Kelola Data</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="openId = null"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
