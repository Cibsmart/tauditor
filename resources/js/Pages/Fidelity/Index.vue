<template>
    <div>
        <Head title="Mandates" />
        <h1 class="mb-8 text-3xl font-bold">Mandates</h1>
        <div class="mb-6 flex items-center justify-between">
            <!-- Search Filter goes here -->
            <search-filter
                v-model="form.search"
                class="mr-4 w-full max-w-lg"
                @reset="reset"
            >
                <label class="block">Mandate Status:</label>
                <select v-model="form.status" class="mt-1 w-full form-select">
                    <option :value="null" />
                    <option v-for="status in statuses" :value="status.id">
                        {{ status.name }}
                    </option>
                </select>
                <label class="mt-5 block">Processed</label>
                <select
                    v-model="form.processed"
                    class="mt-1 w-full form-select"
                >
                    <option :value="null" />
                    <option value="true">Processed</option>
                    <option value="false">Pending</option>
                </select>
            </search-filter>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>SN</TableHead>
                        <TableHead>Name/Staff ID (Mandate Reference)</TableHead>
                        <TableHead>Account Number/BVN</TableHead>
                        <TableHead>Amounts</TableHead>
                        <TableHead>Number of Repayments</TableHead>
                        <TableHead>Disbursement Date</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="(mandate, index) in mandates.data"
                        :key="mandate.id"
                    >
                        <TableCell>
                            <div class="text-sm leading-5">
                                {{ index + 1 }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div
                                class="text-sm leading-5 font-medium uppercase"
                            >
                                {{ mandate.name }}
                            </div>
                            <div
                                class="text-sm leading-5 text-muted-foreground"
                            >
                                {{ mandate.verification_number }} ({{
                                    mandate.reference
                                }})
                                <span
                                    v-if="!mandate.processed"
                                    class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold text-red-800 lowercase"
                                >
                                    pending
                                </span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5">
                                {{ mandate.account_number }}
                            </div>
                            <div
                                class="text-sm leading-5 text-muted-foreground"
                            >
                                {{ mandate.bvn }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5">
                                loan: {{ mandate.loan_amount }}
                            </div>
                            <div class="text-sm leading-5">
                                collection: {{ mandate.collection_amount }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5">
                                repayments: {{ mandate.number_of_repayments }}
                            </div>
                            <div class="text-sm leading-5">
                                repaid: {{ mandate.number_repaid }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5">
                                {{ mandate.date_disbursed }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <span
                                :class="mandate.color"
                                class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold lowercase"
                            >
                                {{ mandate.status }}
                            </span>
                        </TableCell>
                        <TableCell class="text-right">
                            <Link
                                :href="
                                    route('fidelity.show', {
                                        mandate: mandate.id,
                                    })
                                "
                                class="focus:underline focus:outline-none"
                                >view
                            </Link>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="mandates.data.length === 0">
                        <TableCell
                            class="text-xs font-medium tracking-wider uppercase"
                            colspan="6"
                        >
                            No Mandate
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <pagination :links="mandates.links" />
    </div>
</template>

<script>
import Icon from '@/Shared/Icon';
import pickBy from 'lodash/pickBy';
import Layout from '@/Shared/Layout';
import throttle from 'lodash/throttle';
import mapValues from 'lodash/mapValues';
import Pagination from '@/Shared/Pagination';
import { Link, router } from '@inertiajs/vue3';
import SelectInput from '@/Shared/SelectInput';
import SearchFilter from '@/Shared/SearchFilter';
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
        filters: Object,
        mandates: Object,
        statuses: Array,
    },

    components: {
        Icon,
        Link,
        Pagination,
        SelectInput,
        SearchFilter,
        Table,
        TableHeader,
        TableBody,
        TableRow,
        TableHead,
        TableCell,
    },

    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status,
                processed: this.filters.processed,
            },
        };
    },

    watch: {
        form: {
            handler: throttle(function () {
                router.get(this.route('fidelity.index'), pickBy(this.form), {
                    preserveState: true,
                });
            }, 150),
            deep: true,
        },
    },

    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
        },
    },
};
</script>
