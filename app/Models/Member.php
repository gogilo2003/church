<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    use HasFactory;

    protected $appends = ['photo_url'];
    protected $dates = ['date_of_birth'];

    /**
     * The groups that belong to the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * Get the URL to the members's photo.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function photoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->photo
                ? Storage::disk('public')->url($this->photo)
                : $this->defaultProfilePhotoUrl();
        });
    }
    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', sprintf("%s %s", $this->first_name, $this->last_name)))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * The attendances that belong to the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attendances(): BelongsToMany
    {
        return $this->belongsToMany(Attendance::class);
    }

    /**
     * Get all of the contributions for the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * The sms that belong to the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sms(): BelongsToMany
    {
        return $this->belongsToMany(Sms::class, 'member_sms', 'member_id', 'sms_id')
            ->withPivot('status', 'messageId')
            ->withTimestamps();
    }
}
