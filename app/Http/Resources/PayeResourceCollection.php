<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PayeResourceCollection extends ResourceCollection
{
    public static $wrap = 'info';

    public function with($request)
    {
        return [
            'data' => PayeResource::collection($this->getdata('schedules')),
            'status'  => '00',
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
            'title'          => '',
            'surname'        => $this->getData('surname'),
            'first_name'     => $this->getData('first_name'),
            'middle_name'    => $this->getData('middle_name'),
            'dob'            => '',
            'gender'         => '',
            'marital'        => '',
            'mobile_phone'   => '',
            'mda'            => $this->getData('mda'),
            'empNo'          => $this->getData('verification_number'),
            'account_number' => $this->getData('account_number'),
            'bank_code'      => $this->getData('bank_code'),
            'anssid'         => '',
        ];
    }

    protected function getData($value)
    {
        return $this->resource[$value]->resource;
    }
}
