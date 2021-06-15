<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\AuditPayroll;
use App\Models\AuditPaySchedule;
use App\Models\AuditPayrollCategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use function number_format;

class TmsPayeApiController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index()
    {
        $payrolls = Auth::user()->auditPayrolls()->orderBy('year', 'desc')->orderBy('month', 'desc')
                        ->paginate()
                        ->transform(fn(AuditPayroll $payroll) => [
                            'id'           => $payroll->id,
                            'month'        => $payroll->month_name,
                            'year'         => $payroll->year,
                            'created_by'   => $payroll->createdBy(),
                            'date_created' => $payroll->dateCreated(),
                            'is_current'   => $payroll->currentMonth(),
                            'categories'   => $payroll->auditPaymentCategories
                                                      ->transform(fn($category) => [
                                                          'id'              => $category->id,
                                                          'payment_type_id' => $category->payment_type_id,
                                                          'payment_type'    => $category->paymentTypeName(),
                                                          'payment_title'   => $category->payment_title,
                                                          'head_count'      => number_format($category->head_count),
                                                          'uploaded'        => $category->payeData()->firstWhere('successful',
                                                              1)?->successful,
                                                          'failed'          => $category->payeData()->firstWhere('failed',
                                                              1)?->failed,
                                                      ]),
                        ]);

        return Inertia::render('TmsPayeData/Index', ['payrolls' => $payrolls]);
    }

    public function upload($category)
    {
        $category = AuditPayrollCategory::find($category);

        $file_name = $this->prepareFile($category);

        $response = $this->uploadToApi($category, $file_name);

        $category->payeData()->create([
            'user_id'    => Auth::id(),
            'status'     => $response->status(),
            'response'   => collect($response->json()),
            'successful' => $response->successful(),
            'failed'     => $response->failed(),
            'client'     => $response->clientError(),
            'server'     => $response->serverError(),
        ]);

        return back();
    }

    protected function uploadToApi($category, $path)
    {
        $filename = basename($path);

        $content = Storage::disk('local')->get($path);

        $staff_type = $category->staff_type;

        $key = 'paye.'.$staff_type;

        $config = config($key);

        return Http::attach(
            'attachment',
            $content,
            $filename
        )->withHeaders([
            'Authorization'   => base64_encode($config['id'].':'.$config['secret']),
            'ProjectID'       => $config['project_id'],
            'ProjectName'     => $config['project_name'],
            'ProjectCategory' => $config['project_category'],
        ])->post($config['url']);
    }

    protected function prepareFile($category)
    {
        $month = $category->month();

        $year = $category->year();

        $month_name = $this->monthName($month);

        $staff_type = Str::upper($category->staff_type);

        $file_name = "paye/$staff_type PAYE DATA - $month_name $year.csv";

        $file_exists = Storage::disk('local')->exists($file_name);

        if ($file_exists) {
            return $file_name;
        }

        $header = $this->formatContent($this->getHeader());

        Storage::disk('local')->put($file_name, $header);

        AuditPaySchedule::query()
                        ->allSchedules()
                        ->where('audit_payroll_category_id', $category->id)
                        ->where('payment_type_id', 'sal')
                        ->orderBy('audit_mda_schedule_id')
                        ->orderBy('audit_sub_mda_schedule_id')
                        ->orderBy('verification_number')
                        ->chunk(10, function ($schedules) use ($month, $year, $file_name){
                            $data = '';

                            foreach ($schedules as $schedule) {
                                $content = $this->formatContent(
                                    $this->getContent($schedule, $month, $year)
                                );

                                $data .= $content.PHP_EOL;
                            }

                            Storage::disk('local')->append($file_name, $data);
                        });

        return $file_name;
    }

    protected function getContent(AuditPaySchedule $schedule, $month, $year)
    {
        [$surname, $first_name, $middle_name] = $this->splitName($schedule->beneficiary_name);

        return [
            'title'          => '',
            'surname'        => $surname,
            'first_name'     => $first_name,
            'middle_name'    => $middle_name,
            'date_of_birth'  => '',
            'gender'         => '',
            'marital_status' => '',
            'mobile_number'  => '',
            'mda'            => $schedule->mda_name,
            'employee_no'    => $schedule->verification_number,
            'account_number' => $schedule->account_number,
            'bank_code'      => $schedule->bank_code,
            'grade'          => $schedule->beneficiary_cadre,
            'designation'    => $schedule->designation,
            'basic_pay'      => $this->formatValue($schedule->basic_pay),
            'gross_pay'      => $this->formatValue($schedule->gross_pay),
            'nhf'            => $this->getValue(['nhf'], $schedule->deductions),
            'nhis'           => $this->getValue(['ashis', 'ashia'], $schedule->deductions),
            'nsift'          => '',
            'pension'        => $this->getValue(['ansg_pen', 'pension'], $schedule->deductions),
            'tax'            => $this->getValue(['tax'], $schedule->deductions),
            'month'          => $month,
            'year'           => $year,
        ];
    }

    protected function splitName($name)
    {
        $names = Str::of($name)->trim()
                    ->explode(' ');

        $surname = $names->last();
        $first_name = $names->first();
        $middle_name = array_diff($names->all(), [$first_name, $surname]);

        $middle_name = Str::of($this->formatContent($middle_name))
                          ->replace(',', ' ');

        return [$surname, $first_name, $middle_name];
    }

    protected function getHeader()
    {
        return [
            'title'          => 'Title',
            'surname'        => 'SurName',
            'first_name'     => 'FirstName',
            'middle_name'    => 'MiddleName',
            'date_of_birth'  => 'Dob',
            'gender'         => 'Gender',
            'marital_status' => 'Marital',
            'mobile_number'  => 'MobilePhone',
            'mda'            => 'Mda',
            'employee_no'    => 'EmpNo',
            'account_number' => 'AccountNumber',
            'bank_code'      => 'BankCode',
            'grade'          => 'Grade',
            'designation'    => 'Designation',
            'basic_pay'      => 'Basic',
            'gross_pay'      => 'Gross',
            'nhf'            => 'Nhf',
            'nhis'           => 'Nhis',
            'nsift'          => 'Nsift',
            'pension'        => 'Pension',
            'tax'            => 'Tax',
            'month'          => 'Month',
            'year'           => 'Year',
        ];
    }

    protected function formatContent($data)
    {
        return collect($data)->join(',');
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

    protected function formatValue($value)
    {
        return number_format($value, 2, '.', '');
    }

    public function monthName($month)
    {
        $months = [
            '', 'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER',
            'NOVEMBER', 'DECEMBER',
        ];

        return $months[$month];
    }
}
