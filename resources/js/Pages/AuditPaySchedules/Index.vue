<template>
    <div>
        <h1 class="mb-4 font-bold text-3xl">
            <inertia-link :href="route('audit_payroll.index')" class="text-indigo-500 hover:text-indigo-700">
                Audit Payroll
            </inertia-link>
            <span class="text-indigo-500 font-medium">/</span>
            <inertia-link :href="route('audit_mda_schedules.index', {audit_payroll_category})" class="text-indigo-500 hover:text-indigo-700">
                MDA Schedules
            </inertia-link>
            <span class="text-indigo-500 font-medium">/</span>
            <inertia-link :href="route('audit_sub_mda_schedules.index', {audit_mda_schedule})" class="text-indigo-500 hover:text-indigo-700">
                Sub MDA Schedules
            </inertia-link>
            <span class="text-indigo-500 font-medium">/</span> Pay Schedules
        </h1>

        <div class="mb-6 flex justify-between items-center">
            <!-- Search Filter goes here -->
            <!--            <search-filter v-model="form.search" class="w-full max-w-lg mr-4">-->
            <!--            </search-filter>-->
            <div></div>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Beneficiary Name/Verification Number
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Cadre/Designation
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Bank Details
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Net Pay
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Payment Status
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="schedule in schedules.data" :key="schedule.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{ schedule.beneficiary_name }}</div>
                                        <div class="text-sm leading-5 text-gray-600">{{ schedule.verification_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ schedule.cadre }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ schedule.designation }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ schedule.bank_name }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ schedule.account_number }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    <span class="line-through">N</span>
                                    {{ schedule.net_pay }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="schedule.paid
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'">
                                {{ schedule.paid ? 'Paid' : 'Pending' }}
                              </span>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">

                                <inertia-link href="#" class="px-5 py-3">
                                    View Details
                                </inertia-link>
                            </td>
                        </tr>

                        <tr v-if="schedules.data.length === 0">
                            <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                No Pay Schedule
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <pagination :links="schedules.links" />
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon'
    import Layout from '@/Shared/Layout'
    import Pagination from '@/Shared/Pagination'

    export default {
        metaInfo: { title: 'Pay Schedules' },
        layout: Layout,

        props: {
            schedules: Object,
            audit_payroll_category: Number,
            audit_mda_schedule: Number,
        },

        components: {
            Icon,
            Pagination,
        },
    }
</script>
