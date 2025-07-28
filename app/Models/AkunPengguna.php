<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property bool $can_manage_users
 * @property bool $can_manage_data
 */
class AkunPengguna extends Authenticatable
{

    
    use Notifiable;

    protected $table = 'akun_pengguna';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_manage_users',
        'can_manage_data',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function canManageUsers(): bool
    {
        return (bool) $this->can_manage_users;
    }

    public function canManageData(): bool
    {
        return (bool) $this->can_manage_data;
    }
}
