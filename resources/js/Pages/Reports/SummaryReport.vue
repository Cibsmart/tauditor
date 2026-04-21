<template>
    <div>
        <Head title="Payment Summary Report" />
        <h1 class="mb-8 font-bold text-3xl">Payment Summary Report</h1>

        <div>
            <select-input v-model="form.payroll" class="pb-8 w-full"
                          label="Payroll Month" :errors="$page.props.errors.payroll"
                          @update:modelValue="payrollChanged">
                <option disabled value="" class="text-gray-100">Select Payroll Month</option>
                <option v-for="payroll in payrolls" :key="payroll.id" :value="payroll.id">
                    {{ payroll.month_name + ' ' + payroll.year }}
                </option>
            </select-input>
        </div>

        <div v-show="categories.data">
        <div class="mb-6 flex justify-between items-center">
            <div></div>
            <Button as="a" :href="route('reports.summary_print', {payroll: form.payroll})" size="lg">
                Download<span class="hidden md:inline">&nbsp; PDF</span>
            </Button>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Payment Category Title
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Total Net Pay
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Head Count
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="category in categories.data" :key="category.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{ category.payment_title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                    <span class="line-through">N</span>
                                    {{ category.total_net_pay }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ category.head_count }}</div>
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 font-bold uppercase">{{ 'Total' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 font-bold uppercase">
                                    <span class="line-through">N</span>
                                    {{ payroll.total_net_pay }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 font-bold uppercase" >{{ payroll.head_count }}</div>
                            </td>
                        </tr>

                        <tr v-if="categories.data && categories.data.length === 0">
                            <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                No Payment Summary
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon';
    import Layout from '@/Shared/Layout';
    import Pagination from '@/Shared/Pagination';
    import SelectInput from "@/Shared/SelectInput";
    import { Button } from '@/Components/ui/button';

    import { useForm, router } from '@inertiajs/vue3'

    export default {
        layout: Layout,

        props: {
            payroll: Object,
            payrolls: Array,
            categories: Object,
        },

        components: {
            Icon,
            Pagination,
            SelectInput,
            Button,
        },

        setup(props) {
            const form = useForm({
                payroll: props.payroll.id,
            })
            return { form }
        },

        methods: {
            payrollChanged() {
                router.reload({
                    method: 'post', data: this.form,
                    preserveState: true, preserveScroll: true,
                    only: ['categories', 'payroll']
                })
            },
        },
    }
</script>

