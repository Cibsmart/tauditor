<template>
    <div>
        <Head title="MDA Payment Analysis Report" />
        <h1 class="mb-8 text-3xl font-bold">MDA/Zone Summary Report</h1>

        <div class="space-y-4">
            <div class="w-full space-y-1.5">
                <label class="text-sm leading-none font-medium"
                    >Payroll Month</label
                >
                <Select
                    v-model="form.payroll"
                    @update:modelValue="payrollChanged"
                >
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select Payroll Month" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="payroll in payrolls"
                            :key="payroll.id"
                            :value="payroll.id"
                        >
                            {{ payroll.month_name + ' ' + payroll.year }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p
                    v-if="$page.props.errors.payroll"
                    class="text-sm text-destructive"
                >
                    {{ $page.props.errors.payroll }}
                </p>
            </div>

            <div class="w-full space-y-1.5">
                <label class="text-sm leading-none font-medium"
                    >Payment Category</label
                >
                <Select
                    v-model="form.category"
                    @update:modelValue="categoryChanged"
                >
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select Category" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.payment_title }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p
                    v-if="$page.props.errors.category"
                    class="text-sm text-destructive"
                >
                    {{ $page.props.errors.category }}
                </p>
            </div>
        </div>

        <div v-show="reports.data">
            <div class="mt-2 mb-6 flex items-center justify-between">
                <div></div>
                <Button
                    :href="
                        route('reports.mda_print', { category: form.category })
                    "
                    as="a"
                    size="lg"
                >
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
                            <TableHead
                                >Basic Pay (<span class="line-through">N</span
                                >)</TableHead
                            >
                            <TableHead
                                >Gross Pay (<span class="line-through">N</span
                                >)</TableHead
                            >
                            <TableHead
                                >Deduction (<span class="line-through">N</span
                                >)</TableHead
                            >
                            <TableHead
                                >Net Pay (<span class="line-through">N</span
                                >)</TableHead
                            >
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="report in reports.data"
                            :key="report.mda_id"
                        >
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    {{ report.mda_name }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="text-xs leading-5 font-medium">
                                    <table>
                                        <tr>
                                            <td>{{ report.month }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_month }}</td>
                                        </tr>
                                        <tr>
                                            {{
                                                'DIFF'
                                            }}
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    <table>
                                        <tr>
                                            <td>{{ report.head_count }}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ report.prev_head_count }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                :class="
                                                    status[
                                                        getStatus(
                                                            report.diff_head_count,
                                                        )
                                                    ]
                                                "
                                                class="font-bold"
                                            >
                                                {{ report.diff_head_count }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    <table>
                                        <tr>
                                            <td>{{ report.basic_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_basic_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                :class="
                                                    status[
                                                        getStatus(
                                                            report.diff_basic_pay,
                                                        )
                                                    ]
                                                "
                                                class="font-bold"
                                            >
                                                {{ report.diff_basic_pay }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    <table>
                                        <tr>
                                            <td>{{ report.gross_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_gross_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                :class="
                                                    status[
                                                        getStatus(
                                                            report.diff_gross_pay,
                                                        )
                                                    ]
                                                "
                                                class="font-bold"
                                            >
                                                {{ report.diff_gross_pay }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    <table>
                                        <tr>
                                            <td>{{ report.deduction }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_deduction }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                :class="
                                                    status[
                                                        getStatus(
                                                            report.diff_deduction,
                                                        )
                                                    ]
                                                "
                                                class="font-bold"
                                            >
                                                {{ report.diff_deduction }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    <table>
                                        <tr>
                                            <td>{{ report.net_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ report.prev_net_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                :class="
                                                    status[
                                                        getStatus(
                                                            report.diff_net_pay,
                                                        )
                                                    ]
                                                "
                                                class="font-bold"
                                            >
                                                {{ report.diff_net_pay }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-if="reports.data && reports.data.length === 0"
                        >
                            <TableCell
                                class="text-xs font-medium tracking-wider uppercase"
                                colspan="5"
                            >
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
import { router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import Icon from '@/Shared/Icon';
import Layout from '@/Shared/Layout';
import Pagination from '@/Shared/Pagination';


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
        Button,
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
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
        };
    },

    methods: {
        getStatus(value) {
            if (value[0] === '-') {
                return 'negative';
            }

            if (value[0] === '0') {
                return 'zero';
            }

            return 'positive';
        },

        payrollChanged() {
            this.categories = this.payrolls.find(
                (payroll) => payroll.id === this.form.payroll,
            ).categories;
        },

        categoryChanged() {
            router.reload({
                method: 'post',
                data: this.form,
                preserveState: true,
                preserveScroll: true,
                only: ['reports', 'category'],
            });
        },
    },
};
</script>
