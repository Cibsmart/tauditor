<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use App\AuditSubMdaSchedules;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class PayScheduleImport implements OnEachRow
{
    use Importable;


    public AuditSubMdaSchedules $mda_schedules;
    public string $file_path;

    public function __construct(AuditSubMdaSchedules $mda_schedules, string $file_path)
    {
        $this->mda_schedules = $mda_schedules;
        $this->file_path = $file_path;
    }


    public function onRow(Row $row)
    {

    }
}
