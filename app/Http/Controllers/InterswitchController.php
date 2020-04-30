<?php

namespace App\Http\Controllers;

use App\AuditPayroll;

class InterswitchController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function process(AuditPayroll $audit_payroll)
    {
        $month = $audit_payroll->month();

        //Per batch items
        $batch_reference = '';
        $batch_description = '';
        $is_single_debit = false;
        $terminal_id = '';
        $mac_data = ''; //hash terminal_id + beneficiary code + total amount + beneficiary account number

        dd($month);
    }
}
