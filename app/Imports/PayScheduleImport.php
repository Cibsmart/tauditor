<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class PayScheduleImport implements OnEachRow
{
    use Importable;

    public function __construct()
    {

    }


    public function onRow(Row $row)
    {

    }
}
