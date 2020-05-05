<?php

namespace App\Http\Controllers;

use App\AuditPayroll;
use App\BeneficiaryType;
use App\PaymentCredential;
use App\AuditPayrollCategory;
use function dump;
use function hash_algos;

class InterswitchController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function process(AuditPayrollCategory $audit_payroll_category)
    {
        $month = $audit_payroll_category->monthYear();

        $payment_type = $audit_payroll_category->payment_type_id;
        $domain = $audit_payroll_category->domain()->id;

        $payment_title = $audit_payroll_category->payment_title;

        $mdas = $audit_payroll_category->auditMdaSchedules()->autopayGenerated()->cursor();

        foreach ($mdas as $mda) {
            $payment_credential = $mda->paymentCredential();

            $batch_reference = '';
            $batch_description = "$payment_title ";
            $is_single_debit = $payment_credential->is_single_debit;
            $terminal_id = $payment_credential->terminal_id;
            $account_number = $payment_credential->account_number;

            $sub_mdas = $mda->auditSubMdaSchedules()->autopayGenerated()->cursor();

            foreach ($sub_mdas as $sub_mda) {
                $total_amount = $sub_mda->mdaTotalAmount();
                $beneficiary_codes = $sub_mda->mdaBeneficiaryCodes();
                $beneficiary_account_numbers = $sub_mda->mdaBeneficiaryAccountNumbers();

                //hash terminal_id + beneficiary codes + total amount + beneficiary account numbers
                $mac_data = $this->macData(
                    $terminal_id,
                    $beneficiary_codes,
                    $total_amount,
                    $beneficiary_account_numbers
                );

                $schedules = $sub_mda->autopaySchedules;


            }

        }
        dd('here');
    }

    private function macData($terminal_id, $beneficiary_codes, $total_amount, $beneficiary_account_numbers)
    {
        $data = $terminal_id . $beneficiary_codes . $total_amount . $beneficiary_account_numbers;
        return hash('sha512', $data);
    }
}
