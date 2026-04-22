<template>
    <div>
        <Head title="Audit Report" />
        <h1 class="mb-8 font-bold text-3xl">Audit Report for {{  }} Payment</h1>
        <div class="mb-6 flex justify-between items-center">
            <div></div>
            <Button as="a" :href="route('audit_analysis.pdf_report', {audit_payroll_category: audit_payroll_category})" size="lg">
                Download<span class="hidden md:inline">&nbsp; PDF</span>
            </Button>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Beneficiary Name</TableHead>
                        <TableHead>Report(s) | Current Value | Previous Value</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="report in reports.data" :key="report.id">
                        <TableCell>
                            <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{ report.schedule.beneficiary_name }}</div>
                            <div class="text-sm leading-5 text-gray-600">{{ report.schedule.verification_number }}</div>
                        </TableCell>

                        <TableCell>
                            <Table>
                                <TableBody>
                                    <TableRow v-for="audit_report in report.reports" :key="audit_report.id">
                                        <TableCell class="w-2/4 py-1">
                                            <div class="text-sm leading-5 font-medium text-gray-600">{{ audit_report.message }}</div>
                                        </TableCell>
                                        <TableCell class="w-1/4 py-1">
                                            <div class="text-sm leading-5 font-medium text-gray-600">{{ audit_report.current_value }}</div>
                                        </TableCell>
                                        <TableCell class="w-1/4 py-1">
                                            <div class="text-sm leading-5 font-medium text-gray-600">{{ audit_report.previous_value }}</div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="reports.data.length === 0">
                        <TableCell colspan="6" class="text-xs font-medium text-gray-700 uppercase tracking-wider">
                            No Audit Report
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <pagination :links="reports.links" />
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon'
    import Layout from '@/Shared/Layout'
    import Pagination from '@/Shared/Pagination'
    import SearchFilter from '@/Shared/SearchFilter'
    import { Button } from '@/Components/ui/button'
    import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

    import mapValues from 'lodash/mapValues'
    import pickBY from 'lodash/pickBy'
    import throttle from 'lodash/throttle'

    export default {
        layout: Layout,

        props: {
            reports: Object,
            audit_payroll_category: Number,
        },

        components: {
            Icon,
            Pagination,
            Button,
            Table,
            TableHeader,
            TableBody,
            TableRow,
            TableHead,
            TableCell,
        },

    }
</script>
