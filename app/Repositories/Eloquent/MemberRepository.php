<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class MemberRepository implements MemberRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Member::with(['household', 'orgUnit', 'visitorFollowUp.assignedUser'])->latest();

        if (! empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(phone) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(member_number) LIKE ?', [$search]);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['org_unit_id'])) {
            $query->where('org_unit_id', $filters['org_unit_id']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?Member
    {
        return Member::with(['household.members', 'orgUnit', 'user', 'visitorFollowUp.assignedUser', 'lifecycleLogs.changedByUser'])->find($id);
    }

    public function create(array $data): Member
    {
        $member = new Member();
        $this->assignProperties($member, $data);
        $member->save();

        return $member;
    }

    public function update(Member $member, array $data): Member
    {
        $this->assignProperties($member, $data);
        $member->save();

        return $member;
    }

    public function delete(Member $member): bool
    {
        return (bool) $member->delete();
    }

    public function updateStatus(Member $member, string $newStatus): Member
    {
        $member->status = $newStatus;
        $member->save();

        return $member;
    }

    private function assignProperties(Member $member, array $data): void
    {
        if (array_key_exists('org_unit_id', $data)) {
            $member->org_unit_id = $data['org_unit_id'] ? (int) $data['org_unit_id'] : null;
        }
        if (array_key_exists('household_id', $data)) {
            $member->household_id = $data['household_id'] ? (int) $data['household_id'] : null;
        }
        if (array_key_exists('user_id', $data)) {
            $member->user_id = $data['user_id'] ? (int) $data['user_id'] : null;
        }
        if (array_key_exists('member_number', $data)) {
            $member->member_number = $data['member_number'];
        }
        if (array_key_exists('first_name', $data)) {
            $member->first_name = (string) $data['first_name'];
        }
        if (array_key_exists('middle_name', $data)) {
            $member->middle_name = $data['middle_name'];
        }
        if (array_key_exists('last_name', $data)) {
            $member->last_name = (string) $data['last_name'];
        }
        if (array_key_exists('gender', $data)) {
            $member->gender = (string) $data['gender'];
        }
        if (array_key_exists('marital_status', $data)) {
            $member->marital_status = (string) $data['marital_status'];
        }
        if (array_key_exists('occupation', $data)) {
            $member->occupation = $data['occupation'];
        }
        if (array_key_exists('national_id', $data)) {
            $member->national_id = $data['national_id'];
        }
        if (array_key_exists('phone', $data)) {
            $member->phone = (string) $data['phone'];
        }
        if (array_key_exists('email', $data)) {
            $member->email = $data['email'];
        }
        if (array_key_exists('box_no', $data)) {
            $member->box_no = $data['box_no'];
        }
        if (array_key_exists('post_code', $data)) {
            $member->post_code = $data['post_code'];
        }
        if (array_key_exists('town', $data)) {
            $member->town = $data['town'];
        }
        if (array_key_exists('address', $data)) {
            $member->address = $data['address'];
        }
        if (array_key_exists('date_of_birth', $data)) {
            $member->date_of_birth = $data['date_of_birth'];
        }
        if (array_key_exists('status', $data)) {
            $member->status = (string) $data['status'];
        }
        if (array_key_exists('spiritual_milestones', $data)) {
            $member->spiritual_milestones = $data['spiritual_milestones'];
        }
        if (array_key_exists('date_joined', $data)) {
            $member->date_joined = $data['date_joined'];
        }
        if (array_key_exists('photo', $data)) {
            $member->photo = $data['photo'];
        }
    }
}
