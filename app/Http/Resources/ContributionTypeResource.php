<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContributionTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "description" => $this->description,
            "recurrent" => $this->recurrent ? true : false,
            "recurrence_value" => $this->recurrence_value,
            "recurrence_unit" => $this->recurrence_unit,
            "deadline" => $this->deadline,
            "amount" => $this->amount,
            "back_date" => $this->back_date ? true : false,
        ];
    }
}
