<?php

namespace App\Models;

use App\Enums\RoleType;
use App\Enums\UserStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'birth_date',
        'role_type',
        'status',
        'last_login_at',
        'banned_until',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'birth_date' => 'date',
            'role_type' => RoleType::class,
            'status' => UserStatus::class,
            'banned_until' => 'datetime',
        ];
    }

    public function isSystemLevel(): bool
    {
        return $this->role_type === RoleType::System;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSystemAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function canManageUsers(): bool
    {
        return $this->can('user.manage') || $this->hasRole('admin');
    }

    public function isBanned(): bool
    {
        if ($this->status !== UserStatus::Banned) {
            return false;
        }

        if ($this->banned_until && now()->greaterThanOrEqualTo($this->banned_until)) {
            $this->unban();

            return false;
        }

        return true;
    }

    public function unban(): void
    {
        $this->update([
            'status' => UserStatus::Active,
            'banned_until' => null,
            'ban_reason' => null,
        ]);
    }
}
