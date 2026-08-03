<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\VisitorFollowUp;
use App\Repositories\Contracts\VisitorFollowUpRepositoryInterface;

final class VisitorFollowUpRepository implements VisitorFollowUpRepositoryInterface
{
    public function getAllByStage(): array
    {
        $stages = ['new', 'contacted', 'visited', 'in_classes', 'converted', 'dropped'];

        $followUps = VisitorFollowUp::with(['member.orgUnit', 'assignedUser'])
            ->latest('visit_date')
            ->get();

        $grouped = [];
        foreach ($stages as $stage) {
            $grouped[$stage] = $followUps->where('stage', $stage)->values()->all();
        }

        return $grouped;
    }

    public function findById(int $id): ?VisitorFollowUp
    {
        return VisitorFollowUp::with(['member', 'assignedUser'])->find($id);
    }

    public function create(array $data): VisitorFollowUp
    {
        $followUp = new VisitorFollowUp();
        $this->assignProperties($followUp, $data);
        $followUp->save();

        return $followUp;
    }

    public function updateStage(VisitorFollowUp $followUp, string $newStage): VisitorFollowUp
    {
        $followUp->stage = $newStage;
        $followUp->last_contacted_at = now();
        $followUp->save();

        return $followUp;
    }

    public function assignLeader(VisitorFollowUp $followUp, ?int $userId): VisitorFollowUp
    {
        $followUp->assigned_user_id = $userId;
        $followUp->save();

        return $followUp;
    }

    public function updateNotes(VisitorFollowUp $followUp, ?string $notes): VisitorFollowUp
    {
        $followUp->notes = $notes;
        $followUp->last_contacted_at = now();
        $followUp->save();

        return $followUp;
    }

    private function assignProperties(VisitorFollowUp $followUp, array $data): void
    {
        if (array_key_exists('member_id', $data)) {
            $followUp->member_id = (int) $data['member_id'];
        }
        if (array_key_exists('assigned_user_id', $data)) {
            $followUp->assigned_user_id = $data['assigned_user_id'] ? (int) $data['assigned_user_id'] : null;
        }
        if (array_key_exists('stage', $data)) {
            $followUp->stage = (string) $data['stage'];
        }
        if (array_key_exists('visit_date', $data)) {
            $followUp->visit_date = $data['visit_date'];
        }
        if (array_key_exists('visit_purpose', $data)) {
            $followUp->visit_purpose = $data['visit_purpose'];
        }
        if (array_key_exists('prayer_requests', $data)) {
            $followUp->prayer_requests = $data['prayer_requests'];
        }
        if (array_key_exists('notes', $data)) {
            $followUp->notes = $data['notes'];
        }
        if (array_key_exists('last_contacted_at', $data)) {
            $followUp->last_contacted_at = $data['last_contacted_at'];
        }
    }
}
