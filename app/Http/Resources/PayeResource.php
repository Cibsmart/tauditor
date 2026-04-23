<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function array_key_exists;
use function number_format;

class PayeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'grade' => $this->beneficiary_cadre,
            'designation' => $this->designation,
            'basic' => $this->formatValue($this->basic_pay),
            'gross' => $this->formatValue($this->gross_pay),
            'nhf' => $this->getValue(['nhf'], $this->deductions),
            'nhis' => $this->getValue(['ashis', 'ashia'], $this->deductions),
            'nsitf' => '0',
            'pension' => $this->getValue(['ansg_pen', 'pension'], $this->deductions),
            'tax' => $this->getValue(['tax'], $this->deductions),
            'month' => $this->month_name,
            'year' => "$this->year",
        ];
    }

    protected function getValue(array $keys, array $deductions)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $deductions)) {
                return $this->formatValue($deductions[$key]);
            }
        }

        return '0';
    }

    protected function formatValue($value)
    {
        return number_format($value, 2, '.', '');
    }
}
