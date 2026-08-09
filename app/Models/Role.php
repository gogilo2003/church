<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'display_name',
        'description',
        'is_system_role',
        'permissions',
    ];

    protected $casts = [
        'is_system_role' => 'boolean',
        'permissions' => 'array',
    ];

    public function isSystemRole(): bool
    {
        return (bool) $this->is_system_role;
    }

    /**
     * The users that belong to the Role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}

