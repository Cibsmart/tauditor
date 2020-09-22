<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AuditSubMdaSchedule;
use Illuminate\Support\Facades\Storage;

class InterswitchController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function process(Request $request)
    {
        $request->validate([
            'audit_sub_mda' => ['required', 'integer', 'exists:audit_sub_mda_schedules,id'],
        ]);

        $sub_mda = AuditSubMdaSchedule::find($request->audit_sub_mda);

        $audit_payroll_category = $sub_mda->payrollCategory();

        $month = $audit_payroll_category->monthYear(true);

        $month_full = $audit_payroll_category->monthYear();

        $payment_type = $audit_payroll_category->payment_type_id;

        $domain = $audit_payroll_category->domain()->id;

        $mda = $sub_mda->auditMdaSchedule;

        $payment_credential = $mda->paymentCredential();

        $is_single_debit = $payment_credential->is_single_debit ? 'true' : 'false';
        $terminal_id = $payment_credential->terminal_id;
        $account_number = $payment_credential->account_number;
        $bank_code = $payment_credential->bank->code;
        $account_type = $payment_credential->account_type;
        $pan = $payment_credential->pan;
        $beneficiary_type_id = $payment_credential->beneficiary_type_id;

        $directory = $domain === 'state'
            ? "in_dir/$domain $payment_type $month_full"
            : "in_dir/$domain $payment_type $month_full - $beneficiary_type_id";

        $sub_mda_name = $sub_mda->sub_mda_name;

        $file_name = "$directory/$sub_mda_name.csv";

        $total_amount = $sub_mda->autopayTotalAmount();
        $beneficiary_codes = $sub_mda->mdaBeneficiaryCodes();
        $beneficiary_account_numbers = $sub_mda->mdaBeneficiaryAccountNumbers();

        $batch_reference = $this->generateBatchReference($sub_mda->id);
        $batch_description = $this->getBatchDescription(
            $payment_type,
            $sub_mda_name,
            $month
        );

        $mac_data = $this->macData(
            $terminal_id,
            $beneficiary_codes,
            $total_amount,
            $beneficiary_account_numbers
        );

        $row_one_data = [
            'batch_reference'   => $batch_reference,
            'batch_description' => $batch_description,
            'is_single_debit'   => $is_single_debit,
            'terminal_id'       => $terminal_id,
        ];

        $row_one_content = $this->formatContent($row_one_data);
        Storage::disk('local')->put($file_name, $row_one_content);

        $row_two_data = [
            'secure_data'           => '',
            'source_account_number' => $account_number,
            'source_account_type'   => $account_type,
            'bank_cbn_code'         => $bank_code,
            'encrypted_pin'         => '',
            'mac_data'              => $mac_data,
        ];
        $row_two_content = $this->formatContent($row_two_data);
        Storage::disk('local')->append($file_name, $row_two_content);

        $schedules = $sub_mda->autopaySchedules()->cursor();

        foreach ($schedules as $schedule) {
            $data = [
                'payment_reference' => $this->generatePaymentReference($schedule->id),
                'amount'            => $schedule->amount * 100,
                'narration'         => $this->generateNarration($schedule->narration),
                'beneficiary_code'  => $schedule->beneficiary_code,
                'beneficiary_email' => 'test@test.com',
                'cbn_code'          => $schedule->cbn_code,
                'account_number'    => $schedule->account_number,
                'account_type'      => $schedule->account_type,
                'is_prepaid_load'   => 'false',
                'currency_code'     => $schedule->currency,
                'beneficiary_name'  => Str::of($schedule->beneficiary_name)->replace(',', ''),
                'mobile_number'     => '08080808080',
            ];

            $content = $this->formatContent($data);
            Storage::disk('local')->append($file_name, $content);
        }

        $autopay_file_name = 'IN/'.basename($file_name);

        $content = Storage::disk('local')->get($file_name);

        $disk = "sftp_$domain";

        $success = true;

        try{
            $success = Storage::disk($disk)->put($autopay_file_name, $content);
        } catch (Exception $e){
            return back()->with('error', $e->getMessage());
        }

        if (! $success) {
            return back()->with('error', "Autopay Schedule for $month_full $sub_mda_name Failed");
        }

        $sub_mda->autopayUploaded();

        return back()->with('success',
            "Autopay Schedule for $month_full $sub_mda_name Created and Uploaded Successfully");
    }

    private function macData($terminal_id, $beneficiary_codes, $total_amount, $beneficiary_account_numbers)
    {
        $data = $terminal_id.$beneficiary_codes.$total_amount.$beneficiary_account_numbers;
        return hash('sha512', $data);
    }

    private function formatContent($data)
    {
        return collect($data)->join(',');
    }

    private function generateNarration($narration)
    {
        $unique_id = uniqid();

        return Str::upper(Str::of($narration)
                             ->append($unique_id)
                             ->replace(' ', ''));
    }

    private function generateBatchReference($sub_mda)
    {
        $unique_id = uniqid();

        return Str::upper(Str::of($sub_mda)->append($unique_id)
                             ->replace(' ', ''));
    }

    private function generatePaymentReference($pay_schedule_id)
    {
        $unique_id = uniqid();

        return Str::upper(Str::of($pay_schedule_id)->append($unique_id)
                             ->replace(' ', ''));
    }

    private function getBatchDescription($payment_type, $sub_mda_name, $month_full)
    {
        $unique_id = uniqid();

        return Str::upper(Str::of($payment_type)
                             ->append($month_full)
                             ->upper()->append('_')
                             ->replace(' ', '')
                             ->append(Str::of($sub_mda_name)->slug('_'))
                             ->append('_')
                             ->append($unique_id));
    }

    protected static function pad($string, $padding)
    {
        return str_pad($string, $padding, '0', STR_PAD_LEFT);
    }
}
