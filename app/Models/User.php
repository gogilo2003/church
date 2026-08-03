<?php

namespace App\Models;

use App\Traits\HasProfilePhoto;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'phone_number',
        'status',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'permissions',
    ];

    /**
     * The roles that belong to the User
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Accessor to crunch permissions from all assigned roles.
     */
    public function permissions(): Attribute
    {
        return Attribute::get(function () {
            if ($this->is_admin) {
                return ['*'];
            }

            return $this->roles
                ->pluck('permissions')
                ->filter()
                ->map(function ($perms) {
                    return is_string($perms) ? json_decode($perms, true) : $perms;
                })
                ->flatten()
                ->unique()
                ->values()
                ->all();
        });
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isSuperAdmin(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return $this->roles()->whereIn('title', ['Super Admin', 'super_admin'])->exists();
    }

    public function hasCentralPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array('*', $this->permissions, true) || in_array($permission, $this->permissions, true);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $userPerms = $this->permissions;

        return in_array('*', $userPerms, true) || in_array($permission, $userPerms, true);
    }

    /**
     * Get all of the attendances for the User
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all of the offerings for the User
     */
    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }

    /**
     * Get all of the tithes for the User
     */
    public function tithes(): HasMany
    {
        return $this->hasMany(Tithe::class);
    }

    /**
     * Get all scope assignments for the User
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
