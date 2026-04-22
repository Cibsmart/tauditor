<template>
    <div>
        <Head title="Pay Schedules" />
        <h1 class="mb-4 font-bold text-3xl">
            <Link :href="route('audit_payroll.index')" class="text-indigo-500 hover:text-indigo-700">
                Audit Payroll
            </Link>
            <span class="text-indigo-500 font-medium">/</span>
            <Link :href="route('audit_mda_schedules.index', {audit_payroll_category})"
                  class="text-indigo-500 hover:text-indigo-700">
                MDA Schedules
            </Link>
            <span class="text-indigo-500 font-medium">/</span>
            <Link :href="route('audit_sub_mda_schedules.index', {audit_mda_schedule})"
                  class="text-indigo-500 hover:text-indigo-700">
                Sub MDA Schedules
            </Link>
            <span class="text-indigo-500 font-medium">/</span> Pay Schedules
        </h1>

        <div class="mb-6 flex justify-between items-center">
            <div></div>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Beneficiary Name/Verification Number</TableHead>
                        <TableHead>Cadre/Designation</TableHead>
                        <TableHead>Bank Details</TableHead>
                        <TableHead>Net Pay</TableHead>
                        <TableHead>Payment Status</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="schedule in schedules.data" :key="schedule.id">
                        <TableCell>
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                        {{ schedule.beneficiary_name }}
                                    </div>
                                    <div class="text-sm leading-5 text-gray-600">{{
                                            schedule.verification_number
                                        }}
                                    </div>
                                </div>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">
                                {{ schedule.cadre }}
                            </div>
                            <div class="text-sm leading-5 text-gray-600">
                                {{ schedule.designation }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">
                                {{ schedule.bank_name }}
                            </div>
                            <div class="text-sm leading-5 text-gray-600">
                                {{ schedule.account_number }}
                            </div>
                        </TableCell>

                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">
                                <span class="line-through">N</span>
                                {{ schedule.net_pay }}
                            </div>
                        </TableCell>

                        <TableCell>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                  :class="schedule.paid
                                  ? 'bg-green-100 text-green-800'
                                  : 'bg-red-100 text-red-800'">
                                {{ schedule.paid ? 'Paid' : 'Pending' }}
                            </span>
                        </TableCell>

                        <TableCell class="text-right">
                            <Link href="#" class="px-5 py-3">
                                View Details
                            </Link>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="schedules.data.length === 0">
                        <TableCell colspan="6" class="text-xs font-medium text-gray-700 uppercase tracking-wider">
                            No Pay Schedule
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <pagination :links="schedules.links"/>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import {Link} from '@inertiajs/vue3'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

export default {
    layout: Layout,

    props: {
        schedules: Object,
        audit_payroll_category: Number,
        audit_mda_schedule: Number,
    },

    components: {
        Icon,
        Link,
        Pagination,
        Table,
        TableHeader,
        TableBody,
        TableRow,
        TableHead,
        TableCell,
    },
}
</script>
