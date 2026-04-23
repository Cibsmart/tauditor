<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LoanResourceCollection extends ResourceCollection
{
    public function with($request)
    {
        return [
            'is_staff_valid' => 'true',
            'message' => 'Successfully',
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'staff_id' => $this->getData('staff_id'),
                'staff_name' => $this->getData('staff_name'),
                'staff_cadre' => $this->getData('staff_cadre'),
                'staff_mda' => $this->getData('staff_mda'),
                'payment_history' => $this->getdata('schedules'),
            ],
        ];
    }

    protected function getData($value)
    {
        return $this->resource[$value];
    }
}
