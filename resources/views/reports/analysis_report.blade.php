@extends('layouts.report_layout')

@section('title', 'Beneficiaries Analysis Report')

@section('heading', 'HRMEdge Analysis Report (Beneficiaries)')

@section('sub_heading')
{{ 'Payment Title: ' . $category->payment_title }}
@endsection

@section('body')
<table class="table table-striped table-condensed" style="font-family: sans-serif; font-size: 12px">
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
            @php
                $current = collect($rep->current_value);
                $previous = collect($rep->previous_value);
            @endphp
        <td class="" style="width:250px">
            <div class="" >{{ $rep->message }}</div>
        </td>
        <td class="">
            <div class="" >
                @if(count($current) > 0)
                    { @foreach($current as $key => $value)
                        {{ $key . ':' . $value . ' ' }}
                    @endforeach }
                @endif
            </div>
        </td>
        <td class="">
            <div class="" >
                @if(count($previous) > 0)
                    { @foreach($previous as $key => $value)
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
                    @if(count($current) > 0)
                        { @foreach($current as $key => $value)
                            {{ $key . ':' . $value . ' ' }}
                        @endforeach }
                    @endif
                </div>
            </td>
            <td class="">
                <div class="" >
                    @if(count($previous) > 0)
                        { @foreach($previous as $key => $value)
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
@endsection
