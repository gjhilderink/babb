<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password'      => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBestuur(): bool
    {
        return $this->role === 'bestuur';
    }

    public function isGebruiker(): bool
    {
        return $this->role === 'gebruiker';
    }

    public function isAdminOrBestuur(): bool
    {
        return in_array($this->role, ['admin', 'bestuur']);
    }

    public function roleName(): string
    {
        return match($this->role) {
            'admin'     => 'Beheerder',
            'bestuur'   => 'Bestuur',
            'gebruiker' => 'Gebruiker',
            default     => $this->role,
        };
    }
}
