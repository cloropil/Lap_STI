<?php

namespace App\Services;

use App\Models\AkunPengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class AkunPenggunaService
{
    public function getAll()
    {
        return AkunPengguna::all();
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['can_manage_users'] = $data['can_manage_users'] ?? false;
        $data['can_manage_data'] = $data['can_manage_data'] ?? false;

        $data['name'] = $data['name'] ?? $data['nama'] ?? null;
        unset($data['nama']);

        return AkunPengguna::create($data);
    }

    public function findById($id)
    {
        return AkunPengguna::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $this->authorizeSuperadmin();

        $akun = $this->findById($id);

        $akun->name = $data['name'] ?? $akun->name;
        $akun->email = $data['email'] ?? $akun->email;
        $akun->role = $data['role'] ?? $akun->role;
        $akun->can_manage_users = $data['can_manage_users'] ?? false;
        $akun->can_manage_data = $data['can_manage_data'] ?? false;

        if (!empty($data['password'])) {
            $akun->password = Hash::make($data['password']);
        }

        $akun->save();

        return $akun;
    }

    public function updateAkunSendiri($id, array $data)
    {
        /** @var AkunPengguna $user */
        $user = Auth::user();

        if (!$user || $user->id != $id) {
            throw new AuthorizationException('Anda tidak memiliki izin untuk mengubah akun ini.');
        }

        $akun = $this->findById($id);
        $akun->name = $data['name'] ?? $akun->name;

        // Email hanya bisa diubah oleh superadmin
        if ($user->role === 'superadmin' && isset($data['email'])) {
            $akun->email = $data['email'];
        }

        // Pastikan field role tetap terisi agar validasi tidak error
        $akun->role = $akun->role;

        if (!empty($data['password'])) {
            $akun->password = Hash::make($data['password']);
        }

        $akun->save();

        return $akun;
    }

    public function delete($id)
    {
        $this->authorizeSuperadmin();

        $akun = $this->findById($id);
        return $akun->delete();
    }

    private function authorizeSuperadmin(): void
    {
        /** @var AkunPengguna $user */
        $user = Auth::user();

        if (!$user || $user->role !== 'superadmin') {
            throw new AuthorizationException('Hanya superadmin yang dapat melakukan aksi ini.');
        }
    }
}
