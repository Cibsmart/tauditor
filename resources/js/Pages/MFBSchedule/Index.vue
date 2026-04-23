<template>
    <div>
        <Head title="Microfinance Bank Schedule" />
        <h1 class="mb-8 text-3xl font-bold">Microfinance Bank Schedules</h1>
        <div class="mb-6 flex items-center justify-between">
            <div></div>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Month</TableHead>
                        <TableHead>Year</TableHead>
                        <TableHead class="text-right">Details</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody v-for="payroll in payrolls.data" :key="payroll.id">
                    <TableRow :key="payroll.id">
                        <TableCell>
                            <Link
                                class=""
                                href="#"
                                preserve-scroll
                                preserve-state
                                @click="show(payroll.id)"
                            >
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    {{ payroll.month }}
                                </div>
                            </Link>
                        </TableCell>
                        <TableCell>
                            <Link
                                class=""
                                href="#"
                                preserve-scroll
                                preserve-state
                                @click="show(payroll.id)"
                            >
                                <div
                                    class="text-sm leading-5 font-medium uppercase"
                                >
                                    {{ payroll.year }}
                                </div>
                            </Link>
                        </TableCell>
                        <TableCell class="w-px text-sm leading-5 font-medium">
                            <Link
                                class="px-6"
                                href="#"
                                preserve-scroll
                                preserve-state
                                @click="show(payroll.id)"
                            >
                                <icon
                                    class="block h-4 w-6 fill-gray-400"
                                    name="cheveron-right"
                                />
                            </Link>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="show_detail[payroll.id]">
                        <TableCell colspan="6">
                            <Table>
                                <TableBody>
                                    <TableRow
                                        v-for="category in payroll.categories"
                                        :key="category.id"
                                    >
                                        <TableCell class="bg-gray-50">
                                            {{ category.payment_title }}
                                            <span
                                                :class="
                                                    category.payment_type_id ===
                                                    'sal'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-pink-100 text-pink-800'
                                                "
                                                class="rounded-full px-2 text-xs leading-5 font-semibold"
                                            >
                                                {{ category.payment_type }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-50">
                                            Number of MDAs:
                                            <span class="font-bold">
                                                {{ category.mda_count }}
                                            </span>
                                        </TableCell>
                                        <TableCell
                                            class="bg-gray-50 text-right"
                                        >
                                            <a
                                                :href="
                                                    route(
                                                        'mfb_schedule.download',
                                                        {
                                                            category:
                                                                category.id,
                                                            mfb: category.mfb_id,
                                                        },
                                                    )
                                                "
                                                class="px-5 py-3"
                                            >
                                                Download Schedules
                                            </a>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </TableCell>
                    </TableRow>
                </TableBody>

                <TableBody>
                    <TableRow v-if="payrolls.data.length === 0">
                        <TableCell
                            class="text-xs font-medium tracking-wider text-gray-700 uppercase"
                            colspan="6"
                        >
                            No Payroll
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <pagination :links="payrolls.links" />
    </div>
</template>

<script>
import Icon from '@/Shared/Icon';
import Layout from '@/Shared/Layout';
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Shared/Pagination';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';

export default {
    layout: Layout,

    props: {
        payrolls: Object,
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

    data() {
        return {
            status: {
                pending: 'bg-yellow-100 text-yellow-800',
                running: 'bg-pink-100 text-pink-800',
                completed: 'bg-green-100 text-green-800',
                incomplete: 'bg-blue-100 text-blue-800',
            },
            show_detail: [],
        };
    },

    methods: {
        show(payroll) {
            this.show_detail[payroll] = !this.show_detail[payroll];
        },
    },
};
</script>
