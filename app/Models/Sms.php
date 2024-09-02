<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sms extends Model
{
    use HasFactory;

    /**
     * The recipients that belong to the Sms
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_sms', 'sms_id', 'member_id');
    }
}
