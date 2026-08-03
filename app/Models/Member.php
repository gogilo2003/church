<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_unit_id',
        'household_id',
        'user_id',
        'member_number',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'marital_status',
        'occupation',
        'national_id',
        'phone',
        'email',
        'box_no',
        'post_code',
        'town',
        'address',
        'date_of_birth',
        'status',
        'spiritual_milestones',
        'date_joined',
        'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_joined' => 'date',
        'spiritual_milestones' => 'array',
    ];

    protected $appends = ['photo_url', 'full_name'];

    public function fullName(): Attribute
    {
        return Attribute::get(function () {
            return trim(implode(' ', array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
            ])));
        });
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'org_unit_id');
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class, 'household_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function households(): BelongsToMany
    {
        return $this->belongsToMany(Household::class, 'household_member')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function visitorFollowUp(): HasOne
    {
        return $this->hasOne(VisitorFollowUp::class);
    }

    public function lifecycleLogs(): HasMany
    {
        return $this->hasMany(MemberLifecycleLog::class)->latest();
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function photoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->photo
                ? Storage::disk('public')->url($this->photo)
                : $this->defaultProfilePhotoUrl();
        });
    }

    protected function defaultProfilePhotoUrl(): string
    {
        $name = $this->full_name ?: 'Member';
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=4F46E5&background=EEF2FF';
    }

    public function attendances(): BelongsToMany
    {
        return $this->belongsToMany(Attendance::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function sms(): BelongsToMany
    {
        return $this->belongsToMany(Sms::class, 'member_sms', 'member_id', 'sms_id')
            ->withPivot('status', 'messageId')
            ->withTimestamps();
    }
}
