<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Document</title>
</head>

<body>
<div>
    <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead>
                    <tr>
                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                            Beneficiary Name
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                            Report(s) | Current Value | Previous Value
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    @forelse($reports as $report)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ $report->reportable->beneficiary_name }}</div>
                            <div class="text-sm leading-5 text-gray-600">{{ $report->reportable->verification_number }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <table class="min-w-full">
                                <tbody class="bg-white">
                                @foreach($report->reportable->auditReports as $rep)
                                <tr>
                                    <td class="py-1 whitespace-normal border-b border-gray-200 w-2/4">
                                        <div class="text-sm leading-5 font-medium text-gray-600" >{{ $rep->message }}</div>
                                    </td>
                                    <td class="py-1 whitespace-normal border-b border-gray-200 w-1/4">
                                        <div class="text-sm leading-5 font-medium text-gray-600" >{{ $rep->current_value }}</div>
                                    </td>
                                    <td class="py-1 whitespace-normal border-b border-gray-200 w-1/4">
                                        <div class="text-sm leading-5 font-medium text-gray-600" >{{ $rep->previous_value }}</div>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                            No Audit Report
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
