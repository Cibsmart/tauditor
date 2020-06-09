<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <title>Analysis Report</title>
</head>

<body>
    <div class="container-xl h-100">
        <div class="text-center">
            <h3>HRMEdge Analysis Report (Beneficiaries)</h3>
        </div>

        <div>
            <h4>Payment Title: {{ $category->payment_title }}</h4>
        </div>
        <hr style="" />
        <table class="table table-striped table-condensed">
            <thead>
            <tr class="">
                <th class="">
                    SN
                </th>
                <th class="">
                    Beneficiary
                </th>
                <th class="">
                    Report
                </th>
                <th class="">
                    Current Value
                </th>
                <th class="">
                    Previous Value
                </th>
            </tr>
            </thead>
            <tbody class="">
            @forelse($reports as $report)
            <tr>
                <td style="width:50px" rowspan="{{ $report->reportable->auditReports->count() }}">{{ $loop->index + 1 }}</td>
                <td class="" style="width:250px" rowspan="{{ $report->reportable->auditReports->count() }}">
                    <div class="" >{{ $report->reportable->beneficiary_name }}</div>
                    <div class="">{{ $report->reportable->verification_number }}</div>
                </td>

            @foreach($report->reportable->auditReports as $rep)
                @if($loop->index == 0)
                <td class="" style="width:250px">
                    <div class="" >{{ $rep->message }}</div>
                </td>
                <td class="">
                    <div class="" >
                        @if(collect($rep->current_value)->count() < 2)
                            {{ $rep->current_value }}
                        @else
                            { @foreach(collect($rep->current_value) as $key => $value)
                                {{ $key . ':' . $value . ' ' }}
                            @endforeach }
                        @endif
                    </div>
                </td>
                <td class="">
                    <div class="" >
                        @if(collect($rep->previous_value)->count() < 2)
                            {{ $rep->previous_value }}
                        @else
                            { @foreach(collect($rep->previous_value) as $key => $value)
                                {{ $key . ':' . $value . ' ' }}
                            @endforeach }
                        @endif
                    </div>
                </td>
            </tr>
                @else
                <tr>
                    <td class="">
                        <div class="" style="width:250px">{{ $rep->message }}</div>
                    </td>
                    <td class="">
                        <div class="" >
                            @if(collect($rep->current_value)->count() < 2)
                                {{ $rep->current_value }}
                            @else
                            { @foreach(collect($rep->current_value) as $key => $value)
                                {{ $key . ':' . $value . ' ' }}
                            @endforeach }
                            @endif
                        </div>
                    </td>
                    <td class="">
                        <div class="" >
                            @if(collect($rep->previous_value)->count() < 2)
                                {{ $rep->previous_value }}
                            @else
                                { @foreach(collect($rep->previous_value) as $key => $value)
                                    {{ $key . ':' . $value . ' ' }}
                                @endforeach }
                            @endif
                        </div>
                    </td>
                </tr>
                @endif
            @endforeach
            @empty
            <tr>
                <td colspan="6" class="">
                    No Audit Report
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_text(30, ($pdf->get_height() - 26.89), "Date Printed: " . date('d M Y H:i:s'), null, 8);
            $pdf->page_text(($pdf->get_width() - 84), ($pdf->get_height() - 26.89), "Page {PAGE_NUM} of {PAGE_COUNT}", null, 8);
        }
</script>
</body>
</html>
