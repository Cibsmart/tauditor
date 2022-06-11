<?php

namespace App\Http\Controllers;

use App\Models\LoanMandate;
use App\Models\LoanStatus;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class FidelityMandateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $filters = Request::all('search', 'status', 'processed');

        $mandates = LoanMandate::query()
                               ->orderBy('status')
                               ->filter(Request::only('search', 'status', 'processed'))
                               ->paginate()
                               ->transform(fn ($m) => [
                                   'id'                      => $m->id,
                                   'name'                    => $m->beneficiary->name,
                                   'verification_number'     => $m->staff_id,
                                   'bvn'                     => $m->bvn,
                                   'account_number'          => $m->account_number,
                                   'reference'               => $m->reference,
                                   'loan_amount'             => $m->formatted_loan_amount,
                                   'collection_amount'       => $m->formatted_collection_amount,
                                   'total_collection_amount' => $m->total_collection_amount,
                                   'number_of_repayments'    => $m->number_of_repayments,
                                   'number_repaid'           => $m->deductionCount(),
                                   'status'                  => $m->loan_status->name,
                                   'color'                   => $m->color,
                                   'date_disbursed'          => $m->formatted_disbursement_date,
                                   'processed'               => $m->processed,
                               ]);

        return Inertia::render('Fidelity/Index', [
            'filters'  => $filters,
            'mandates' => $mandates,
            'statuses' => LoanStatus::all(),
        ]);
    }

    public function show(LoanMandate $mandate)
    {
        return Inertia::render('Fidelity/Show', [
            'mandate' => [
                'id'                      => $mandate->id,
                'name'                    => $mandate->beneficiary->name,
                'verification_number'     => $mandate->staff_id,
                'bvn'                     => $mandate->bvn,
                'account_number'          => $mandate->account_number,
                'reference'               => $mandate->reference,
                'loan_amount'             => $mandate->formatted_loan_amount,
                'collection_amount'       => $mandate->formatted_collection_amount,
                'total_collection_amount' => $mandate->total_collection_amount,
                'number_of_repayments'    => $mandate->number_of_repayments,
                'number_repaid'           => 0,
                'status'                  => $mandate->loan_status->name,
                'color'                   => $mandate->color,
                'date_disbursed'          => $mandate->formatted_disbursement_date,
                'processed'               => $mandate->processed,
            ],
        ]);
    }

    public function store()
    {
        $validated = request()->validate([
            'mandate_id' => 'required|numeric',
        ]);

        $mandate = LoanMandate::find($validated['mandate_id']);

        $mandate->process();

        return back(302)->with('success', 'Loan Mandate has been marked as processed');
    }
}
