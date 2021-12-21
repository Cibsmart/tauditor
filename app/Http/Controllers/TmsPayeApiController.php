<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\AuditPayroll;
use App\Models\AuditPayrollCategory;
use Illuminate\Support\Facades\Auth;
use App\Actions\GenerateAndSendPayeData;

class TmsPayeApiController extends Controller
{

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()->orderBy('year', 'desc')->orderBy('month', 'desc')
                        ->paginate()
                        ->transform(fn (AuditPayroll $payroll) => [
                            'id'           => $payroll->id,
                            'month'        => $payroll->month_name,
                            'year'         => $payroll->year,
                            'created_by'   => $payroll->createdBy(),
                            'date_created' => $payroll->dateCreated(),
                            'is_current'   => $payroll->currentMonth(),
                            'categories'   => $payroll->auditPaymentCategories
                                ->transform(fn ($category) => [
                                    'id'              => $category->id,
                                    'payment_type_id' => $category->payment_type_id,
                                    'payment_type'    => $category->paymentTypeName(),
                                    'payment_title'   => $category->payment_title,
                                    'head_count'      => number_format($category->head_count),
                                    'uploaded'        => $category->payeData()->firstWhere(
                                        'successful',
                                        1
                                    )?->successful,
                                    'failed'          => $category->payeData()->firstWhere(
                                        'failed',
                                        1
                                    )?->failed,
                                ]),
                        ]);

        return Inertia::render('TmsPayeData/Index', ['payrolls' => $payrolls]);
    }

    public function upload($category)
    {
        $category = AuditPayrollCategory::findOrFail($category);

        GenerateAndSendPayeData::dispatch($category);

        $message = "$category->payment_title CSV File is been Generated in the Background, but not Sent to API";

        return back()->with('success', $message);
    }
}
