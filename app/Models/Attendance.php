<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The members that belong to the Attendance
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class);
    }

    public function attendanceDate(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->isoFormat('ddd, Do MMM, Y'),
            set: fn ($value) => Carbon::parse($value),
        );
    }
}
