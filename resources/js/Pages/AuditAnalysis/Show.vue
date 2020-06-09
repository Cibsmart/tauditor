<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Audit Report for {{  }} Payment</h1>
        <div class="mb-6 flex justify-between items-center">
            <!-- Search Filter goes here -->
            <!--            <search-filter v-model="form.search" class="w-full max-w-lg mr-4">-->
            <!--            </search-filter>-->
            <div></div>
            <a :href="route('audit_analysis.pdf_report', {audit_payroll_category: audit_payroll_category})"
               class="btn btn-big btn-indigo">
                Download<span class="hidden md:inline">&nbsp; PDF</span>
            </a>
        </div>

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
                        <tr v-for="report in reports.data" :key="report.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ report.schedule.beneficiary_name }}</div>
                                <div class="text-sm leading-5 text-gray-600">{{ report.schedule.verification_number }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <table class="min-w-full">
                                    <tbody class="bg-white">
                                        <tr v-for="audit_report in report.reports" :key="audit_report.id">
                                            <td class="py-1 whitespace-normal border-b border-gray-200 w-2/4">
                                                <div class="text-sm leading-5 font-medium text-gray-600" >{{ audit_report.message }}</div>
                                            </td>
                                            <td class="py-1 whitespace-normal border-b border-gray-200 w-1/4">
                                                <div class="text-sm leading-5 font-medium text-gray-600" >{{ audit_report.current_value }}</div>
                                            </td>
                                            <td class="py-1 whitespace-normal border-b border-gray-200 w-1/4">
                                                <div class="text-sm leading-5 font-medium text-gray-600" >{{ audit_report.previous_value }}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        <tr v-if="reports.data.length === 0">
                            <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                No Audit Report
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <pagination :links="reports.links" />
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon'
    import Layout from '@/Shared/Layout'
    import Pagination from '@/Shared/Pagination'
    import SearchFilter from '@/Shared/SearchFilter'


    import mapValues from 'lodash/mapValues'
    import pickBY from 'lodash/pickBy'
    import throttle from 'lodash/throttle'

    export default {
        metaInfo: { title: 'Audit Report' },
        layout: Layout,

        props: {
            reports: Object,
            audit_payroll_category: Number,
        },

        components: {
            Icon,
            Pagination,
        },

    }
</script>

