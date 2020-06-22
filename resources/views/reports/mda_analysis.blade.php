@extends('layouts.report_layout')

@section('title', 'MDA/Zone Payment Analysis Report')

@section('heading', 'HRMEdge MDA/Zone Payment Analysis Report')

@section('sub_heading')
    {{ $filename }}
@endsection

@section('body')
    <table class="table table-striped table-condensed" style="font-family: sans-serif; font-size: 12px">
        <thead>
        <tr class="">
            <th class="">
                SN
            </th>
            <th class="">
                MDA/Zone
            </th>
            <th class="">
                Month
            </th>
            <th class="">
                Head Count
            </th>
            <th class="">
                Basic Pay
            </th>
            <th class="">
                Gross Pay
            </th>
            <th class="">
                Deduction
            </th>
            <th class="">
                Net Pay
            </th>
        </tr>
        </thead>
        <tbody class="">
        @forelse($reports as $report)
            <tr>
                <td style="width:50px">{{ $loop->index + 1 }}</td>

                <td class="" style="">
                    <div class="" >{{ $report->mda_name }}</div>
                </td>

                <td class="" style="">
                        <table class="table-condensed" style="font-family: sans-serif; font-size: 12px">
                            <tr>
                                <td>
                                    {{ $month }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ $prev_month }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ 'DIFF' }}
                                </td>
                            </tr>
                        </table>
                </td>

                <td class="" style="">
                        <table class="table-condensed" style="font-family: sans-serif; font-size: 12px">
                            <tr>
                                <td>
                                    {{ number_format($report->head_count) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->prev_head_count) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->head_count - $report->prev_head_count) }}
                                </td>
                            </tr>
                        </table>
                </td>

                <td class="" style="">
                        <table class="table-condensed" style="font-family: sans-serif; font-size: 12px">
                            <tr>
                                <td>
                                    {{ number_format($report->basic_pay, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->prev_basic_pay, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->basic_pay - $report->prev_basic_pay, 2) }}
                                </td>
                            </tr>
                        </table>
                </td>

                <td class="" style="">
                        <table class="table-condensed" style="font-family: sans-serif; font-size: 12px">
                            <tr>
                                <td>
                                    {{ number_format($report->gross_pay, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->prev_gross_pay, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->gross_pay - $report->prev_gross_pay, 2) }}
                                </td>
                            </tr>
                        </table>
                </td>

                <td class="" style="">
                        <table class="table-condensed" style="font-family: sans-serif; font-size: 12px">
                            <tr>
                                <td>
                                    {{ number_format($report->total_deduction, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->prev_total_deduction, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->total_deduction - $report->prev_total_deduction, 2) }}
                                </td>
                            </tr>
                        </table>
                </td>

                <td class="" style="">
                        <table class="table-condensed" style="font-family: sans-serif; font-size: 12px">
                            <tr>
                                <td>
                                    {{ number_format($report->net_pay, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->prev_net_pay, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ number_format($report->net_pay - $report->prev_net_pay, 2) }}
                                </td>
                            </tr>
                        </table>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="">
                    No Analysis Report
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection
