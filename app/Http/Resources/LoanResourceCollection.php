<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use function number_format;

class LoanResourceCollection extends ResourceCollection
{
    public function with($request)
    {
        return [
            'is_staff_valid'  => 'true',
            'message' => 'Successfully',
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
            'data' => [
                'staff_id' => $this->getData('staff_id'),
                 'staff_name' => $this->getData('staff_name'),
                 'staff_cadre' => $this->getData('staff_cadre'),
                 'staff_mda' => $this->getData('staff_mda'),
                 'bank_name' => $this->getData('bank_name'),
                 'account_number' => $this->getData('account_number'),
                 'previous_net_pays' => $this->getdata('schedules')
            ]
        ];
    }

    protected function getData($value)
    {
        return $this->resource[$value];
    }
}
