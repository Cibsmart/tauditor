<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PayloadCollection extends ResourceCollection
{
    public function with($request)
    {
        return [
            'status' => true,
            'message' => 'Successfull',
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'address' => $this->resource['address'],
            'phone_number' => '',
            'nigerian_beneficiaries' => $this->resource['nigerians'],
            'foreign_beneficiaries' => '',
            'beneficiaries' => BeneficiaryResource::collection($this->resource['schedules']),
        ];
    }
}
