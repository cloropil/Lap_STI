<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AkunPengguna;

class AkunPenggunaSeeder extends Seeder
{
    public function run(): void
    {
        AkunPengguna::truncate(); // Kosongkan tabel dulu (opsional)

        AkunPengguna::create([
            'name' => 'aidil',
            'email' => 'aidil@pln.com',
            'password' => Hash::make('aidil123'),
            'role' => 'superadmin',
            'can_manage_users' => true,
            'can_manage_data' => true,
        ]);

        AkunPengguna::create([
            'name' => 'budi',
            'email' => 'budi@pln.com',
            'password' => Hash::make('budi123'),
            'role' => 'admin',
            'can_manage_users' => false,
            'can_manage_data' => true,
        ]);

        AkunPengguna::create([
            'name' => 'Staff',
            'email' => 'staff@pln.com',
            'password' => Hash::make('staf123'),
            'role' => 'staff',
            'can_manage_users' => false,
            'can_manage_data' => false,
        ]);
    }
}
