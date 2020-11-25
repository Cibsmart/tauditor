<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Audit Payrolls</h1>
        <div class="mb-6 flex justify-between items-center">
            <div></div>
            <inertia-link :href="route('audit_payroll.store')" method="post" class="btn btn-big btn-indigo" preserve-state>
                Add <span class="hidden md:inline"> &nbsp; Payroll</span>
            </inertia-link>
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
                                &nbsp;
                            </th>

                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Details
                            </th>
                        </tr>
                        </thead>
                        <tbody v-for="payroll in payrolls.data" :key="payroll.id" class="bg-white">
                        <tr :key="payroll.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="whitespace-no-wrap border-b border-gray-200">
                                <inertia-link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                    <div class="px-6 pt-4 text-sm leading-5 font-medium text-gray-900 uppercase" >{{ payroll.month }}</div>
                                    <div class="px-6 pb-4 text-sm leading-5 text-gray-600">{{ payroll.year }}</div>
                                </inertia-link>
                            </td>
                            <td class="whitespace-no-wrap border-b border-gray-200">
                                <inertia-link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                    <div class="px-6 pt-4 text-sm leading-5 text-gray-900">
                                        {{ payroll.created_by }}
                                    </div>
                                    <div class="px-6 pb-4 text-sm leading-5 text-gray-600">
                                        {{ payroll.date_created }}
                                    </div>
                                </inertia-link>
                            </td>

                            <td class="w-px whitespace-no-wrap border-b border-gray-200">
                                <inertia-link v-if="payroll.is_current && payroll.can_add_leave"
                                              :href="route('audit_payroll.leave', {audit_payroll: payroll.id})" class="px-5 py-3" preserve-state>
                                    Add Annual Leave Allowance
                                </inertia-link>
                            </td>

                            <td class="w-px whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                <inertia-link href="#" @click="show(payroll.id)" class="px-6" preserve-state preserve-scroll>
                                    <icon name="cheveron-right" class="block w-6 h-4 fill-gray-400" />
                                </inertia-link>
                            </td>
                        </tr>

                        <tr v-if="show_detail[payroll.id]">
                            <td colspan="6" class="whitespace-no-wrap text-left border-b border-gray-200 text-sm text-indigo-800 leading-5 font-medium">
                                <table class="min-w-full">
                                    <tr v-for="category in payroll.categories" :key="category.id">
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            {{ category.payment_title }}
                                            <span class="px-2 text-xs leading-5 font-semibold rounded-full"
                                                  :class="category.payment_type_id === 'sal' ? 'bg-green-100 text-green-800' : 'bg-pink-100 text-pink-800'">
                                             {{ category.payment_type }}
                                           </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            Total Amount: <span class="line-through">N</span>
                                            <span class="font-bold">
                                                {{ category.total_amount }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            Head Count:
                                            <span class="font-bold">
                                                {{ category.head_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            <inertia-link :href="route('audit_mda_schedules.index', {audit_payroll_category: category.id})"
                                                          class="px-5 py-3" preserve-state preserve-scroll>
                                                View Mdas
                                            </inertia-link>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </tbody>

                        <tbody>
                        <tr v-if="payrolls.data.length === 0">
                            <td colspan="3" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
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

    export default {
        metaInfo: { title: 'Audit Payroll' },
        layout: Layout,

        props: {
            payrolls: Object,
        },

        components: {
            Icon,
            Pagination,
        },

        data(){
            return {
                show_detail: [],
            }
        },

        methods: {
          show(payroll){
              this.show_detail[payroll] = !this.show_detail[payroll]
          }
        },
    }
</script>
