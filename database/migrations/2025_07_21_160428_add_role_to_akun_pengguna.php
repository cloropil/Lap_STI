<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('akun_pengguna', function (Blueprint $table) {
            // Menambahkan kolom role
            $table->enum('role', ['superadmin', 'admin', 'staff'])->default('staff');
            // Menambahkan kolom untuk hak akses
            $table->boolean('can_manage_users')->default(false);
            $table->boolean('can_manage_data')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('akun_pengguna', function (Blueprint $table) {
            // Menghapus kolom yang ditambahkan
            $table->dropColumn(['can_manage_users', 'can_manage_data']);
            $table->dropColumn('role');
        });
    }
};