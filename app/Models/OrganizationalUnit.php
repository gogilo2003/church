<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationalUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'hierarchy_level_id',
        'parent_id',
        'name',
        'code',
        'email',
        'phone',
        'address',
        'status',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(HierarchyLevel::class, 'hierarchy_level_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function ancestors(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'org_unit_closures',
            'descendant_id',
            'ancestor_id'
        )->withPivot('depth');
    }

    public function descendants(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'org_unit_closures',
            'ancestor_id',
            'descendant_id'
        )->withPivot('depth');
    }

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'org_unit_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'org_unit_id');
    }

    public function tithes(): HasMany
    {
        return $this->hasMany(Tithe::class, 'org_unit_id');
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class, 'org_unit_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'organizational_unit_id');
    }
}
