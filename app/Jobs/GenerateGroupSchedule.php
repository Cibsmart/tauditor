<?php

namespace App\Jobs;

use App\Actions\GenerateGroupAutopayScheduleAction;
use App\Models\AuditPayrollCategory;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateGroupSchedule implements ShouldQueue
{
    use Queueable;

    public int $timeout = 0;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public Domain $domain,
        public AuditPayrollCategory $category,
        public BeneficiaryType $beneficiaryType,
    ) {}

    public function handle(GenerateGroupAutopayScheduleAction $action)
    {
        $action->execute($this->domain, $this->category, $this->beneficiaryType);
    }
}
