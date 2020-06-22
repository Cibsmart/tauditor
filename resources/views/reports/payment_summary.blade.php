@extends('layouts.report_layout')

@section('title', 'Payment Summary Report')

@section('heading', 'HRMEdge Payment Summary Report')

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
                Payment Category
            </th>
            <th class="">
                Total Net Pay
            </th>
            <th class="">
                Head Count
            </th>
        </tr>
        </thead>
        <tbody class="">
        @forelse($categories as $category)
            <tr>
                <td style="width:50px">{{ $loop->index + 1 }}</td>

                <td class="" style="">
                    <div class="" >{{ $category->payment_title }}</div>
                </td>

                <td class="" style="">
                    <div class="" >
                        <span class="line-through">N</span>
                        {{ number_format($category->total_net_pay, 2) }}
                    </div>
                </td>

                <td class="" style="">
                    <div class="" >{{ number_format($category->head_count) }}</div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="">
                    No Payment Summary
                </td>
            </tr>
        @endforelse
        <tr>
            <td class="" style="font-weight: bolder" colspan="2">
                <div class="" >{{ 'Total' }}</div>
            </td>

            <td class="" style="font-weight: bolder">
                <div class="" >
                    <span class="line-through">N</span>
                    {{ number_format($payroll->totalNetPay(), 2) }}
                </div>
            </td>

            <td class="" style="font-weight: bolder">
                <div class="" >{{ number_format($payroll->headCount()) }}</div>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
