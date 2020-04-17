<?php


namespace App\Actions;


use Carbon\Carbon;
use Illuminate\Support\Str;
use App\AuditSubMdaSchedule;

class GenerateAutoPayScheduleAction
{
    protected $year;
    protected $month;
    protected $domain;
    protected $payment;
    protected $narration;
    protected $reference;
    protected $reference_id = 1;

    protected $pay_comm_i;
    protected $pay_comm_ii;

    protected $pay_comm_i_amount;
    protected $pay_comm_ii_amount;

    protected $pay_comm_i_charge;
    protected $pay_comm_ii_charge;

    protected const INTERSWITCH_CHARGE = 20;

    protected AuditSubMdaSchedule $sub_mda;

    public function execute(AuditSubMdaSchedule $sub_mda)
    {
        $this->sub_mda = $sub_mda;

        $this->initializePayComms();

        $this->generateAutoPayScheduleFor();
    }

    private function generateAutoPayScheduleFor()
    {
        $schedules = $this->sub_mda->auditPaySchedules;

        $schedule = $schedules->first();

        $this->year = $schedule->year;
        $this->month = $schedule->month;
        $this->payment = $schedule->pension ? '_PEN_' : '_SAL_';

        [$commercial_schedules, $microfinance_schedules] = $schedules->partition(fn($schedule) => $schedule->bankable_type == 'commercial');

        /**
         * ___________________________________________________
         * Commercial Bank Users
         * ___________________________________________________
         */
        $commercial_users = $commercial_schedules->count();

        $this->pay_comm_i_amount = $this->pay_comm_i_charge * $commercial_users;
        $this->pay_comm_ii_amount = $this->pay_comm_ii_charge * $commercial_users;

        foreach ($commercial_schedules as $schedule) {

            $amount = $schedule->net_pay - $this->pay_comm_i_charge - $this->pay_comm_ii_charge;

            $this->reference = $this->getReferenceFor($schedule->id);

            if(! $this->narration){
                $this->narration = $this->createNarration($schedule->department_name);
            }

            $attributes = [
                'payment_reference' => $this->reference,
                'beneficiary_code' => $schedule->account_number,
                'beneficiary_name' => $schedule->beneficiary_name,
                'account_number' => $schedule->account_number,
                'account_type' => 10,
                'cbn_code' => $schedule->bankable->code,
                'is_cash_card' => '0',
                'narration' => $this->narration,
                'amount' => $amount,
                'email' => ' ',
                'currency' => 'NGN',
            ];

            $autopay_schedule = $this->sub_mda->autopaySchedules()->create($attributes);
            $schedule->autopay_schedule_id = $autopay_schedule->id;
            $schedule->save();
        }


        /**
         * ___________________________________________________
         * Microfinance Bank Users
         * ___________________________________________________
         */
        foreach ($microfinance_schedules as $schedule) {

            $amount = $schedule->net_pay - $this->pay_comm_i_charge - $this->pay_comm_ii_charge;

            $this->reference = $this->getReferenceFor($schedule->id);

            if(! $this->narration){
                $this->narration = $this->createNarration($schedule->department_name);
            }

            $attributes = [
                'micro_finance_bank_id' => $schedule->bankable_id,
                'payment_reference' => $this->reference,
                'beneficiary_code' => $schedule->account_number,
                'beneficiary_name' => $schedule->beneficiary_name,
                'account_number' => $schedule->account_number,
                'account_type' => 10,
                'cbn_code' => $schedule->bankable->bankCode(),
                'is_cash_card' => '0',
                'narration' => $this->narration,
                'amount' => $amount,
                'email' => ' ',
                'currency' => 'NGN',
            ];

            $autopay_schedule = $this->sub_mda->microfinanceSchedules()->create($attributes);
            $schedule->autopay_schedule_id = $autopay_schedule->id;
            $schedule->save();
        }


        $mfbs = $microfinance_schedules->groupBy('bankable_id');

        foreach ($mfbs as $mfb)
        {
            $schedule = $mfb->first();

            $mfb_users = $mfb->count();
            $sum_net_pay = $mfb->sum('net_pay');

            $paycomm_i = $this->pay_comm_i_charge * $mfb_users;
            $paycomm_ii = ($this->pay_comm_ii_charge + self::INTERSWITCH_CHARGE) * $mfb_users - self::INTERSWITCH_CHARGE;

            $amount = $sum_net_pay - $paycomm_i - $paycomm_ii;

            $bank = $schedule->bankable;

            if(! $this->narration){
                $this->narration = $this->createNarration($schedule->department_name);
            }

            $attributes = [
                'payment_reference' => $this->getReferenceFor($schedule->id),
                'beneficiary_code' => $bank->account_number,
                'beneficiary_name' => $bank->name,
                'account_number' => $bank->account_number,
                'account_type' => 10,
                'cbn_code' => $bank->bankCode(),
                'is_cash_card' => '0',
                'narration' => $this->narration,
                'amount' => $amount,
                'email' => ' ',
                'currency' => 'NGN',
            ];

            $this->pay_comm_i_amount = $this->pay_comm_i_amount + $paycomm_i;
            $this->pay_comm_ii_amount = $this->pay_comm_ii_amount + $paycomm_ii;

            $this->sub_mda->autopaySchedules()->create($attributes);
        }

            /**
             * ___________________________________________________
             * Paycom I
             * ___________________________________________________
             */

            $paycom_i = [
                'payment_reference' => $this->getReferenceFor($this->reference_id),
                'beneficiary_code' => $this->pay_comm_i->account_number,
                'beneficiary_name' => $this->pay_comm_i->code,
                'account_number' => $this->pay_comm_i->account_number,
                'account_type' => 10,
                'cbn_code' => $this->pay_comm_i->bankable->bankCode(),
                'is_cash_card' => '0',
                'narration' => $this->narration,
                'amount' => $this->pay_comm_i_amount,
                'email' => ' ',
                'currency' => 'NGN',
            ];

            $this->sub_mda->autopaySchedules()->create($paycom_i);


            /**
             * ___________________________________________________
             * Paycom II
             * ___________________________________________________
             */

            $paycom_ii = [
                'payment_reference' => $this->getReferenceFor($this->reference_id),
                'beneficiary_code' => $this->pay_comm_ii->account_number,
                'beneficiary_name' => $this->pay_comm_ii->code,
                'account_number' => $this->pay_comm_ii->account_number,
                'account_type' => 10,
                'cbn_code' => $this->pay_comm_ii->bankable->bankCode(),
                'is_cash_card' => '0',
                'narration' => $this->narration,
                'amount' => $this->pay_comm_ii_amount,
                'email' => ' ',
                'currency' => 'NGN',
            ];

            $this->sub_mda->autopaySchedules()->create($paycom_ii);

        $this->sub_mda->autopay_generated = Carbon::now();
        $this->sub_mda->save();
    }

    private function getReferenceFor($id)
    {
        $month = Str::of($this->month)->limit(3,'');

        $year = Str::of($this->year)->substr(2);

        $reference_id = $this->pad($this->reference_id++, 4);

        $schedule_id = $this->pad($id, 8);

        $random_numbers = $this->pad(random_int(1, 999), 3);

        return $month->append('_')
                     ->append($year)
                     ->append($this->payment)
                     ->append($random_numbers, $schedule_id)
                     ->append($reference_id);
    }

    private function createNarration($department_name)
    {
        $domain = $this->domain->code == 'STATE' ? '_ANSG_' : '_ANLG_';

        $sub_mda_abbr = Str::of($department_name)->explode(' ')
                                                 ->map(fn($word) => Str::limit($word, 1, ''))
                                                 ->join('');

        return Str::of($this->reference)->limit(10,'')->append($domain)->append($sub_mda_abbr);
    }

    protected static function pad($string, $padding)
    {
        return is_int($string) ? str_pad($string, $padding, '0', STR_PAD_LEFT) : $string;
    }

    private function initializePayComms() : void
    {
        $this->domain = $this->sub_mda->auditMdaSchedule->auditPayroll->domain;

        $pay_comms = $this->domain->payComms;

        $this->pay_comm_i = $pay_comms->where('code', 'PayComm I')->first();
        $this->pay_comm_ii = $pay_comms->where('code', 'PayComm II')->first();

        $this->pay_comm_i_charge = $this->pay_comm_i->commission;
        $this->pay_comm_ii_charge = $this->pay_comm_ii->commission;
    }
}
