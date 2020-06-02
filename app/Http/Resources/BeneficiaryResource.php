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
            'monthly_gross_income' => number_format($this->gross_pay, 2, '.', ''),
            'yearly_gross_income' => number_format($this->gross_pay * 12, 2, '.', ''),
            'total_deductions' => number_format($this->total_deduction, 2, '.', ''),
            'monthly_tax_paid' => array_key_exists('tax', $this->deductions)
                ? number_format($this->deductions['tax'], 2, '.', '')
                : '',
            'pension' => array_key_exists('ansg_pen', $this->deductions)
                ? number_format($this->deductions['ansg_pen'], 2, '.', '')
                : '',
            'nhf' => array_key_exists('nhf', $this->deductions)
                ? number_format($this->deductions['nhf'], 2, '.', '')
                : '',
            'nhis' => array_key_exists('ashis', $this->deductions)
                ? number_format($this->deductions['ashis'], 2, '.', '')
                : '',
            'nsitf' => '',
            'life_assurance' => '',
        ];
    }
}
