<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\LoanMandate;
use Illuminate\Support\Facades\Request;

class FidelityMandateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function mandate()
    {
        $search = Request::query('search');

        $mandates = LoanMandate::query()
                               ->orderBy('status')
                               ->search(Request::query('search'))
                               ->paginate()
                               ->transform(fn($m) => [
                                   'id'                      => $m->id,
                                   'name'                    => $m->beneficiary->name,
                                   'verification_number'     => $m->staff_id,
                                   'bvn'                     => $m->bvn,
                                   'account_number'          => $m->account_number,
                                   'loan_amount'             => $m->formatted_loan_amount,
                                   'collection_amount'       => $m->formatted_collection_amount,
                                   'total_collection_amount' => $m->total_collection_amount,
                                   'number_of_repayments'    => $m->number_of_repayments,
                                   'number_repaid'           => 0,
                                   'status'                  => $m->loan_status->name,
                                   'color'                   => $m->color,
                                   'date_disbursed'          => $m->formatted_disbursement_date,
                               ]);

        return Inertia::render('Fidelity/Mandate', [
            'search'   => $search,
            'mandates' => $mandates,
        ]);
    }
}
