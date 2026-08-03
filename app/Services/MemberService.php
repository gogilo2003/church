<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Member;
use App\Models\MemberLifecycleLog;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class MemberService
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository
    ) {}

    public function getPaginatedMembers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->memberRepository->getPaginated($filters, $perPage);
    }

    public function findMember(int $id): ?Member
    {
        return $this->memberRepository->findById($id);
    }

    public function createMember(array $data): Member
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['member_number'])) {
                $data['member_number'] = $this->generateMemberNumber();
            }

            if (empty($data['status'])) {
                $data['status'] = 'active';
            }

            $member = $this->memberRepository->create($data);

            // Log initial lifecycle status
            $log = new MemberLifecycleLog();
            $log->member_id = $member->id;
            $log->from_status = 'initial';
            $log->to_status = $member->status;
            $log->changed_by_user_id = Auth::id();
            $log->reason = 'Member registered';
            $log->save();

            return $member;
        });
    }

    public function updateMember(Member $member, array $data): Member
    {
        return DB::transaction(function () use ($member, $data) {
            $oldStatus = $member->status;
            $updatedMember = $this->memberRepository->update($member, $data);

            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $log = new MemberLifecycleLog();
                $log->member_id = $updatedMember->id;
                $log->from_status = $oldStatus;
                $log->to_status = (string) $data['status'];
                $log->changed_by_user_id = Auth::id();
                $log->reason = 'Member profile updated';
                $log->save();
            }

            return $updatedMember;
        });
    }

    public function changeMemberStatus(Member $member, string $newStatus, ?string $reason = null): Member
    {
        return DB::transaction(function () use ($member, $newStatus, $reason) {
            $oldStatus = $member->status;
            $updatedMember = $this->memberRepository->updateStatus($member, $newStatus);

            $log = new MemberLifecycleLog();
            $log->member_id = $updatedMember->id;
            $log->from_status = $oldStatus;
            $log->to_status = $newStatus;
            $log->changed_by_user_id = Auth::id();
            $log->reason = $reason ?? 'Status lifecycle transition';
            $log->save();

            return $updatedMember;
        });
    }

    public function deleteMember(Member $member): bool
    {
        return $this->memberRepository->delete($member);
    }

    private function generateMemberNumber(): string
    {
        $year = date('Y');
        $nextId = (Member::max('id') ?? 0) + 1;
        return sprintf('MEM-%s-%04d', $year, $nextId);
    }
}
