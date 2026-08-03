<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\VisitorFollowUp;
use Illuminate\Database\Eloquent\Collection;

interface VisitorFollowUpRepositoryInterface
{
    public function getAllByStage(): array;

    public function findById(int $id): ?VisitorFollowUp;

    public function create(array $data): VisitorFollowUp;

    public function updateStage(VisitorFollowUp $followUp, string $newStage): VisitorFollowUp;

    public function assignLeader(VisitorFollowUp $followUp, ?int $userId): VisitorFollowUp;

    public function updateNotes(VisitorFollowUp $followUp, ?string $notes): VisitorFollowUp;
}
