<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorFollowUp extends Model
{
    use HasFactory;

    protected $table = 'visitor_follow_ups';

    protected $fillable = [
        'member_id',
        'assigned_user_id',
        'stage',
        'visit_date',
        'visit_purpose',
        'prayer_requests',
        'notes',
        'last_contacted_at',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'last_contacted_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
