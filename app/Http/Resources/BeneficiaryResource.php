<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;
use function number_format;
use function array_key_exists;

class BeneficiaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->beneficiary_name,
            'designation' => $this->designation,
            'mobile_phone' => '',
            'email' => '',
            'monthly_gross_income' => $this->formatValue($this->gross_pay),
            'yearly_gross_income' => $this->formatValue($this->gross_pay * 12),
            'total_deductions' => $this->formatValue($this->total_deduction),
            'monthly_tax_paid' => $this->getValue(['tax'], $this->deductions),
            'pension' => $this->getValue(['ansg_pen', 'pension'], $this->deductions),
            'nhf' => $this->getValue(['nhf'], $this->deductions),
            'nhis' => $this->getValue(['ashis', 'ashia'], $this->deductions),
            'nsitf' => '',
            'life_assurance' => '',
        ];
    }

    protected function getValue(array $keys, array $deductions)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $deductions)) {
                return $this->formatValue($deductions[$key]);
            }
        }

        return '';
    }

    public function formatValue($value)
    {
        return number_format($value, 2, '.', '');
    }
}
