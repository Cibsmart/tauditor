<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Pay Schedules</h1>
        <div class="mb-6 flex justify-between items-center">
            <!-- Search Filter goes here -->
            <!--            <search-filter v-model="form.search" class="w-full max-w-lg mr-4">-->
            <!--            </search-filter>-->
        </div>

        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Bank Details
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                MDA
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Net Pay
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="schedule in schedules.data" :key="schedule.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ schedule.beneficiary_name }}</div>
                                <div class="text-sm leading-5 text-gray-600">{{ schedule.beneficiary_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ schedule.account_number }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ schedule.bank_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">{{ schedule.mda }}</div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ schedule.sub_mda }}
                                    {{ schedule.sub_sub_mda }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    <span class="line-through">N</span>
                                    {{ schedule.net_pay }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">View</a>
                            </td>
                        </tr>

                        <tr v-if="schedules.data.length === 0">
                            <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">No Pay Schedule</td>
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
    import SearchFilter from '@/Shared/SearchFilter'


    import mapValues from 'lodash/mapValues'
    import pickBY from 'lodash/pickBY'
    import throttle from 'lodash/throttle'

    export default {
        metaInfo: { title: 'Pay Schedules' },
        layout: Layout,

        props: {
            schedules: Object,
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

