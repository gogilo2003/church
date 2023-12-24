<?php

namespace App\Http\Resources;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContributionTypeResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $data = [
            "id" => $this->id,
            "description" => $this->description,
            "recurrent" => $this->recurrent ? true : false,
            "recurrence_value" => $this->recurrence_value,
            "recurrence_unit" => $this->recurrence_unit,
            "deadline" => $this->deadline,
            "amount" => $this->amount,
            "back_date" => $this->back_date ? true : false,
        ];

        if ($this->RelationLoaded('contributions')) {
            $members = Member::all();
            $registered = $this->contributions;
            $contributions = $members->map(function ($member) use ($registered) {

                $contribution = $registered->where('member_id', $member->id)->first();

                $id = $contribution ? $contribution->id : null;
                $date = $contribution ? $contribution->contribution_date : null;
                $status = $contribution ? $contribution->contribution_status : null;
                $amount = $contribution ? $contribution->amount : 0;
                $paid = $contribution ? $contribution->payments->sum('amount') : 0;
                $balance = $contribution ? $contribution->amount - $contribution->payments->sum('amount') : 0;

                return (object)[
                    "id" => $id,
                    "member" => ["id" => $member->id, "name" => sprintf("%s %s", $member->first_name, $member->last_name)],
                    "date" => $date,
                    "status" => $status,
                    "amount" => $amount,
                    "paid" => $paid,
                    "balance" => $balance,
                ];
            });
            $data['contributions'] = $contributions;
        }
        return $data;
    }
}
