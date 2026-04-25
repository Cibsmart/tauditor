<?php

namespace App\Jobs;

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Models\AuditPayrollCategory;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateGroupSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Domain $domain,
        public AuditPayrollCategory $category,
        public BeneficiaryType $beneficiaryType,
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GenerateGroupAutopayScheduleAction $action)
    {
        $action->execute($this->domain, $this->category, $this->beneficiaryType);
    }
}
