<?php

namespace App\Compute;

use App\Beneficiary;
use App\Contracts\Computable;

class Tax implements Computable
{
    private Beneficiary $beneficiary;

    private float $annual_basic = 0.0;

    private float $annual_allowance = 0.0;

    private float $annual_gross = 0.0;

    private const PENSION_RATE = 8; //8 percent of annual_gross

    private const NHF_RATE = 2.5; // 2.5 percent of annual_gross

    private const NHIS_RATE = 5; // 5 percent of annual_gross

    private array $amounts = [300000, 300000, 500000, 500000, 1600000, 3200000]; // tax amount break points

    private array $rates = [7, 11, 15, 19, 21, 24]; //Percentage rates

    public function compute(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;

        return $this->getAnnualBasic()
                    ->getAnnualAllowance()
                    ->computeAnnualGross()
                    ->thenComputeTax();
    }

    private function getAnnualBasic()
    {
        $this->annual_basic = $this->beneficiary->basic() * 12;

        return $this;
    }

    private function getAnnualAllowance()
    {
        $this->annual_allowance = $this->beneficiary->totalMonthlyAllowance() * 12;

        return $this;
    }

    private function computeAnnualGross()
    {
        $this->annual_gross = $this->annual_basic + $this->annual_allowance;

        return $this;
    }

    private function thenComputeTax()
    {
        if ($this->beneficiary->isPensioner()) {
            return $this->formatTax($this->annual_basic * 1 / 100);
        }

        if ($this->annual_gross < 400000) {
            return $this->formatTax($this->annual_gross * 1 / 100);
        }

        return $this->formatTax($this->gradedTax());
    }

    private function exemptions()
    {
        return $this->annual_gross * self::PENSION_RATE / 100
            + $this->annual_gross * self::NHF_RATE / 100
            + $this->annual_gross * self::NHIS_RATE / 100;
    }

    private function relief()
    {
        return max($this->annual_gross * 1 / 100, $this->annual_gross * 20 / 100 + 200000);
    }

    private function gradedTax()
    {
        $taxable_income = $this->annual_gross - $this->exemptions();

        $adjusted_taxable = $taxable_income - $this->relief();

        $tax = 0.0;
        $taxed_sum = 0.0;

        for ($i = 0; $i < count($this->amounts); $i++) {
            if ($adjusted_taxable - $taxed_sum <= $this->amounts[$i] or $i === 5) {
                $taxed = $adjusted_taxable - $taxed_sum;
                $tax = $tax + $taxed * $this->rates[$i] / 100;
                break;
            } else {
                $taxed = $this->amounts[$i];
                $taxed_sum = $taxed_sum + $taxed;
                $tax = $tax + $taxed * $this->rates[$i] / 100;
            }
        }

        return $tax;
    }

    public function formatTax($annual_tax)
    {
        $monthly_tax = $annual_tax / 12;

        return round($monthly_tax, 2);
    }
}
