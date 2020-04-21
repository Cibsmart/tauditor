<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Audit Report for {{  }} Payment</h1>
        <div class="mb-6 flex justify-between items-center">
            <!-- Search Filter goes here -->
            <!--            <search-filter v-model="form.search" class="w-full max-w-lg mr-4">-->
            <!--            </search-filter>-->
            <div></div>
            <!--            <inertia-link :href="route('audit_payroll.store')" method="post" class="btn btn-big btn-indigo">-->
            <!--                <span class="hidden md:inline">New Audit Payroll</span>-->
            <!--            </inertia-link>-->
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
                                Report
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Current Value
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Previous Value
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
<!--                        <tr v-for="report in reports.data" :key="report.id">-->
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ 'IFEBUDE BARNABAS' }}</div>
                                <div class="text-sm leading-5 text-gray-600">{{ '100304050' }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ 'NEW BENEFICIARY' }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-600" >{{ 'CURRENT VALUE' }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-600" >{{ 'PREVIOUS VALUE' }}</div>
                            </td>
                        </tr>

<!--                        <tr v-if="reports.data.length === 0">-->
<!--                            <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">-->
<!--                                No Audit Report-->
<!--                            </td>-->
<!--                        </tr>-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<!--        <pagination :links="reports.links" />-->
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon'
    import Layout from '@/Shared/Layout'
    import Pagination from '@/Shared/Pagination'
    import SearchFilter from '@/Shared/SearchFilter'


    import mapValues from 'lodash/mapValues'
    import pickBY from 'lodash/pickBY'
    import throttle from 'lodash/throttle'

    export default {
        metaInfo: { title: 'Audit Report' },
        layout: Layout,

        props: {
            // reports: Object,
        },

        components: {
            Icon,
            Pagination,
        },

    }
</script>

