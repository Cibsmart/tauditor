<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\AuditPayrollCategory;
use Illuminate\Support\Facades\Storage;
use function is_int;
use function collect;
use function str_pad;
use function basename;
use function random_int;
use const STR_PAD_LEFT;

class InterswitchController extends Controller
{
    private int $reference_id = 1;

    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function process(AuditPayrollCategory $audit_payroll_category)
    {
        $month = $audit_payroll_category->monthYear(true);

        $month_full = $audit_payroll_category->monthYear();

        $payment_type = $audit_payroll_category->payment_type_id;

        $domain = $audit_payroll_category->domain()->id;

        $mdas = $audit_payroll_category->auditMdaSchedules()->autopayGenerated()->cursor();

        foreach ($mdas as $mda) {
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

            $sub_mdas = $mda->auditSubMdaSchedules()->autopayGenerated()->cursor();

            foreach ($sub_mdas as $sub_mda) {
                $sub_mda_name = $sub_mda->sub_mda_name;

                $file_name = "$directory/$sub_mda_name.csv";

                $total_amount = $sub_mda->mdaTotalAmount();
                $beneficiary_codes = $sub_mda->mdaBeneficiaryCodes();
                $beneficiary_account_numbers = $sub_mda->mdaBeneficiaryAccountNumbers();

                $batch_reference = $this->generateBatchReference($sub_mda->id, $payment_type, $month);
                $batch_description = $this->getBatchDescription(
                    $beneficiary_type_id,
                    $sub_mda_name,
                    $month_full
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
                        'payment_reference' => $schedule->payment_reference,
                        'amount'            => $schedule->amount * 100,
                        'narration'         => $schedule->narration,
                        'beneficiary_code'  => $schedule->beneficiary_code,
                        'beneficiary_email' => 'test@test.com',
                        'cbn_code'          => $schedule->cbn_code,
                        'account_number'    => $schedule->account_number,
                        'account_type'      => $schedule->account_type,
                        'is_prepaid_load'   => 'false',
                        'currency_code'     => $schedule->currency,
                        'beneficiary_name'  => $schedule->beneficiary_name,
                        'mobile_number'     => '08080808080',
                    ];
                    $content = $this->formatContent($data);
                    Storage::disk('local')->append($file_name, $content);
                }
            }

            $files = Storage::allFiles($directory);

            foreach ($files as $file) {
//                $file_name = basename($file);
                $file_name = 'IN/anambra_test.csv';

                $content = Storage::disk('local')->get($file);

                $x = Storage::disk('sftp')->put($file_name, $content);

            }
        }

        return back()->with('success', "Autopay Schedule for $month_full Created and Uploaded Successfully");
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

    private function generateBatchReference($sub_mda, $payemnt_type, $payment_month)
    {
        $reference_id = $this->pad($this->reference_id++, 2);

        $sub_mda_id = $this->pad($sub_mda, 6);

        $random_numbers = $this->pad(random_int(1, 99), 3);

        return Str::upper(Str::of($payemnt_type)->append($payment_month)
                             ->append($random_numbers, $sub_mda_id)
                             ->append($reference_id)
                             ->replace(' ', ''));
    }

    private function getBatchDescription($beneficiary_type_id, $sub_mda_name, $month_full)
    {
        return Str::upper(Str::of($beneficiary_type_id)->append(' - ')
                             ->append($sub_mda_name)
                             ->append(' - ')
                             ->append($month_full));
    }

    protected static function pad($string, $padding)
    {
        return is_int($string) ? str_pad($string, $padding, '0', STR_PAD_LEFT) : $string;
    }
}
