<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;

class NyscScheduleImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        // TODO: Implement onRow() method.
    }
}
