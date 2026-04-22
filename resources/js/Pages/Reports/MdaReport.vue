<template>
    <div>
        <Head title="MDA Payment Analysis Report" />
        <h1 class="mb-8 font-bold text-3xl">MDA/Zone Summary Report</h1>

        <div>
            <select-input v-model="form.payroll" class="pb-8 w-full"
                          label="Payroll Month" :errors="$page.props.errors.payroll"
                          @update:modelValue="payrollChanged">
                <option disabled value="" class="text-gray-100">Select Payroll Month</option>
                <option v-for="payroll in payrolls" :key="payroll.id" :value="payroll.id">
                    {{ payroll.month_name + ' ' + payroll.year }}
                </option>
            </select-input>

            <select-input v-model="form.category" class="pb-8 w-full"
                          label="Payment Category" :errors="$page.props.errors.category"
                          @update:modelValue="categoryChanged">
                <option disabled value="" class="text-gray-100">Select Category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.payment_title }}
                </option>
            </select-input>
        </div>

        <div v-show="reports.data">
            <div class="mb-6 flex justify-between items-center">
                <div></div>
                <Button as="a" :href="route('reports.mda_print', {category: form.category})" size="lg">
                    Download<span class="hidden md:inline">&nbsp; PDF</span>
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>MDA/Zone</TableHead>
                            <TableHead>Month</TableHead>
                            <TableHead>Head Count</TableHead>
                            <TableHead>Basic Pay (<span class="line-through">N</span>)</TableHead>
                            <TableHead>Gross Pay (<span class="line-through">N</span>)</TableHead>
                            <TableHead>Deduction (<span class="line-through">N</span>)</TableHead>
                            <TableHead>Net Pay (<span class="line-through">N</span>)</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="report in reports.data" :key="report.mda_id">
                            <TableCell>
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{ report.mda_name }}</div>
                            </TableCell>
                            <TableCell>
                                <div class="text-xs leading-5 font-medium text-gray-700">
                                    <table>
                                        <tr>
                                            <td>{{ report.month }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_month }}</td>
                                        </tr>
                                        <tr>
                                            {{ 'DIFF' }}
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                    <table>
                                        <tr>
                                            <td>{{ report.head_count }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_head_count }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" :class="status[getStatus(report.diff_head_count)]">
                                                {{ report.diff_head_count }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                    <table>
                                        <tr>
                                            <td>{{ report.basic_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_basic_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" :class="status[getStatus(report.diff_basic_pay)]">
                                                {{ report.diff_basic_pay }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                    <table>
                                        <tr>
                                            <td>{{ report.gross_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_gross_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" :class="status[getStatus(report.diff_gross_pay)]">
                                                {{ report.diff_gross_pay }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                    <table>
                                        <tr>
                                            <td>{{ report.deduction }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_deduction }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" :class="status[getStatus(report.diff_deduction)]">
                                                {{ report.diff_deduction }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                    <table>
                                        <tr>
                                            <td>{{ report.net_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_net_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold" :class="status[getStatus(report.diff_net_pay)]">
                                                {{ report.diff_net_pay }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="reports.data && reports.data.length === 0">
                            <TableCell colspan="5" class="text-xs font-medium text-gray-700 uppercase tracking-wider">
                                No Analysis Report
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <pagination :links="reports.links" />
        </div>
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon';
    import Layout from '@/Shared/Layout';
    import Pagination from '@/Shared/Pagination';
    import SelectInput from "@/Shared/SelectInput";
    import { Button } from '@/Components/ui/button';
    import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

    import { router } from '@inertiajs/vue3'

    export default {
        layout: Layout,

        props: {
            payrolls: Array,
            reports: Object,
            category: Object,
        },

        components: {
            Icon,
            Pagination,
            SelectInput,
            Button,
            Table,
            TableHeader,
            TableBody,
            TableRow,
            TableHead,
            TableCell,
        },

        data() {
            return {
                categories: '',
                form: {
                    payroll: '',
                    category: this.category.id,
                },
                status: {
                    negative: 'text-red-700',
                    positive: 'text-green-600',
                    zero: 'text-gray-400',
                },
            }
        },

        methods: {

            getStatus(value){
                if(value[0] === '-'){
                    return 'negative';
                }

                if(value[0] === '0'){
                    return 'zero';
                }

                return 'positive';
            },

            payrollChanged() {
                this.categories = this.payrolls.find(payroll => payroll.id === this.form.payroll).categories;
            },

            categoryChanged() {
                router.reload({
                    method: 'post', data: this.form,
                    preserveState: true, preserveScroll: true,
                    only: ['reports', 'category']
                })
            },
        },
    }
</script>
