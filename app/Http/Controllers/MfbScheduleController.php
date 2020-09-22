<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\AuditPayroll;
use Illuminate\Support\Facades\Auth;

class MfbScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $mfb = Auth::user()->microfinanceBank;

        dd($mfb);

        $payrolls = Auth::user()->auditPayrolls()
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->paginate()
                        ->transform(fn (AuditPayroll $payroll) => [
                            'id'                => $payroll->id,
                            'month'             => $payroll->month_name,
                            'year'              => $payroll->year,
                            'categories'        => $payroll->auditPaymentCategories
                                ->transform(function ($category) {
                                    $uploaded_count = $category->countOfMdasSchedulesUploaded();
                                    $autopay_count = $category->countOfMdasAutopayGenerated();

                                    return [
                                        'id'              => $category->id,
                                        'payment_type_id' => $category->payment_type_id,
                                        'payment_type'    => $category->paymentTypeName(),
                                        'payment_title'   => $category->payment_title,
                                        'autopay_status'  => $category->autopay_status,
                                        'mda_count'       => $category->mdaCount(),
                                        'uploaded_count'  => $uploaded_count,
                                        'autopay_count'   => $autopay_count,
                                    ];
                                }),
                        ]);

        return Inertia::render('MFBSchedule/Index', [
            'payrolls' => $payrolls,
        ]);
    }
}
