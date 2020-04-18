<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Auto Pay Payrolls</h1>
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
                                Month
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Created By
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Autopay Status
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="payroll in payrolls.data" :key="payroll.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ payroll.month }}</div>
                                        <div class="text-sm leading-5 text-gray-600">{{ payroll.year }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ payroll.created_by }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ payroll.date_created }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="payroll.autopay_generated
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'">
                                {{ payroll.autopay_generated ? 'Generated' : 'Pending' }}
                              </span>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">

                                <inertia-link href="#" class="px-5 py-3">
                                    Download
                                </inertia-link>

                                <span> | </span>

                                <inertia-link :href="route('audit_mda_schedules.index', {audit_payroll: payroll.id})" class="px-5 py-3">
                                    View Mdas
                                </inertia-link>

                                <span> | </span>

                                <inertia-link :href="route('audit_autopay.generate', { audit_payroll: payroll.id })" method="post" class="px-5 py-3">
                                    Generate
                                </inertia-link>
                            </td>
                        </tr>

                        <tr v-if="payrolls.data.length === 0">
                            <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                No Payroll
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <pagination :links="payrolls.links" />
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
        metaInfo: { title: 'Payroll' },
        layout: Layout,

        props: {
            payrolls: Object,
            // filters: Object,
        },

        components: {
            Icon,
            Pagination,
            // SearchFilter,
        },

        data(){
            return {
                // form: {
                //     search: this.filters.search,
                // },
            }
        },
    }
</script>
