<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberLifecycleLog extends Model
{
    use HasFactory;

    protected $table = 'member_lifecycle_logs';

    protected $fillable = [
        'member_id',
        'from_status',
        'to_status',
        'changed_by_user_id',
        'reason',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
