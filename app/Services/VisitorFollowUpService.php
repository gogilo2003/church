<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Member;
use App\Models\VisitorFollowUp;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\VisitorFollowUpRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class VisitorFollowUpService
{
    public function __construct(
        private readonly VisitorFollowUpRepositoryInterface $followUpRepository,
        private readonly MemberRepositoryInterface $memberRepository,
        private readonly MemberService $memberService
    ) {}

    public function getPipelineBoard(): array
    {
        return $this->followUpRepository->getAllByStage();
    }

    public function createVisitorIntake(array $memberData, array $followUpData): VisitorFollowUp
    {
        return DB::transaction(function () use ($memberData, $followUpData) {
            $memberData['status'] = 'visitor';
            $member = $this->memberService->createMember($memberData);

            $followUpData['member_id'] = $member->id;
            if (empty($followUpData['visit_date'])) {
                $followUpData['visit_date'] = now()->toDateString();
            }
            if (empty($followUpData['stage'])) {
                $followUpData['stage'] = 'new';
            }

            return $this->followUpRepository->create($followUpData);
        });
    }

    public function updateStage(int $followUpId, string $newStage): VisitorFollowUp
    {
        $followUp = $this->followUpRepository->findById($followUpId);
        if (! $followUp) {
            throw new \InvalidArgumentException('Follow-up record not found.');
        }

        return DB::transaction(function () use ($followUp, $newStage) {
            $updated = $this->followUpRepository->updateStage($followUp, $newStage);

            if ($newStage === 'converted') {
                $this->memberService->changeMemberStatus($followUp->member, 'active', 'Converted from visitor via follow-up pipeline');
            }

            return $updated;
        });
    }

    public function convertVisitorToMember(int $followUpId): Member
    {
        $followUp = $this->followUpRepository->findById($followUpId);
        if (! $followUp) {
            throw new \InvalidArgumentException('Follow-up record not found.');
        }

        return DB::transaction(function () use ($followUp) {
            $this->followUpRepository->updateStage($followUp, 'converted');
            return $this->memberService->changeMemberStatus($followUp->member, 'active', 'Converted visitor to active member');
        });
    }

    public function assignLeader(int $followUpId, ?int $userId): VisitorFollowUp
    {
        $followUp = $this->followUpRepository->findById($followUpId);
        if (! $followUp) {
            throw new \InvalidArgumentException('Follow-up record not found.');
        }

        return $this->followUpRepository->assignLeader($followUp, $userId);
    }
}
