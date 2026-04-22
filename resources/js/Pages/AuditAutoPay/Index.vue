<template>
    <div>
        <Head title="Audit Autopay" />
        <h1 class="mb-8 font-bold text-3xl">Auto Pay Payrolls</h1>
        <div class="mb-6 flex justify-between items-center">
            <div></div>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Month</TableHead>
                        <TableHead>Created By</TableHead>
                        <TableHead class="text-right">Details</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody v-for="payroll in payrolls.data" :key="payroll.id">
                    <TableRow :key="payroll.id">
                        <TableCell>
                            <Link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{ payroll.month }}</div>
                                <div class="text-sm leading-5 text-gray-600">{{ payroll.year }}</div>
                            </Link>
                        </TableCell>
                        <TableCell>
                            <Link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ payroll.created_by }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ payroll.date_created }}
                                </div>
                            </Link>
                        </TableCell>
                        <TableCell class="w-px text-sm leading-5 font-medium">
                            <Link href="#" @click="show(payroll.id)" class="px-6" preserve-state preserve-scroll>
                                <icon name="cheveron-right" class="block w-6 h-4 fill-gray-400"/>
                            </Link>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="show_detail[payroll.id]">
                        <TableCell colspan="6">
                            <Table>
                                <TableBody>
                                    <TableRow v-for="category in payroll.categories" :key="category.id">
                                        <TableCell class="bg-gray-200">
                                            {{ category.payment_title }}
                                            <span class="px-2 text-xs leading-5 font-semibold rounded-full"
                                                  :class="category.payment_type_id === 'sal' ? 'bg-green-100 text-green-800' : 'bg-pink-100 text-pink-800'">
                                                {{ category.payment_type }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            MDA Count:
                                            <span class="font-bold">
                                                {{ category.mda_count }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            Uploaded:
                                            <span class="font-bold">
                                                {{ category.uploaded_count }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            Generated:
                                            <span class="font-bold">
                                                {{ category.autopay_count }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            <span class="px-2 text-xs leading-5 font-semibold rounded-full uppercase"
                                                  :class="status[category.autopay_status]">
                                                {{ category.autopay_status }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="text-right bg-gray-200">
                                            <Button v-show="category.can_generate" :as="Link"
                                                          :href="route('audit_autopay.generate', { audit_payroll_category: category.id })"
                                                          method="post" size="sm" preserve-state preserve-scroll>
                                                Generate
                                            </Button>

                                            <Link v-show="category.refreshable"
                                                          :href="route('audit_autopay.index')"
                                                          class="px-5 py-3" preserve-state preserve-scroll>
                                                Refresh
                                            </Link>

                                            <a v-show="category.viewable"
                                               :href="route('audit_autopay.download', { audit_payroll_category: category.id })"
                                               class="px-5 py-3">
                                                Download Autopay
                                            </a>

                                            <span v-show="category.viewable"> | </span>

                                            <a v-show="category.viewable"
                                                :href="route('audit_autopay.downloadMfb', { audit_payroll_category: category.id })"
                                               class="px-5 py-3">
                                                Download MFB
                                            </a>

                                            <span v-show="category.viewable"> | </span>

                                            <Link v-show="category.viewable"
                                                :href="route('audit_autopay.show', {audit_payroll_category: category.id})"
                                                class="px-5 py-3" preserve-state preserve-scroll>
                                                View MDAs
                                            </Link>
                                        </TableCell>
                                    </TableRow>

                                    <TableRow v-for="category in payroll.other_categories" :key="category.id">
                                        <TableCell class="bg-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">
                                                {{ category.payment_title }}
                                                <span class="px-2 text-xs leading-5 font-semibold rounded-full" :class="category.color">
                                                    {{ category.payment_type }}
                                                </span>
                                            </div>
                                            <div class="text-sm leading-5 ">
                                                <span v-if="!category.tenece && !category.fidelity" class="italic text-green-900">No Charge Applied</span>
                                                <span v-if="category.tenece && category.fidelity" class="italic text-pink-900">All Charges Applied</span>
                                                <span v-if="category.tenece && !category.fidelity" class="italic text-blue-900">Fidelity Charge not Applied</span>
                                            </div>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            Line Items:
                                            <span class="font-bold">
                                                {{ category.line_items }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="category.uploaded
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800'">
                                                {{ category.uploaded ? 'UPLOADED' : 'NOT-UPLOADED' }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="category.autopay_generated
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800'">
                                                {{ category.autopay_generated ? 'GENERATED' : 'NOT-GENERATED' }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            <span class="px-2 text-xs leading-5 font-semibold rounded-full uppercase"
                                                  :class="status[category.autopay_status]">
                                                {{ category.autopay_status }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="text-right bg-gray-200">
                                            <Button v-show="category.can_generate" :as="Link"
                                                  :href="route('other_audit_autopay.generate', { other_audit_payroll_category: category.id })"
                                                  method="post" size="sm" preserve-state preserve-scroll>
                                                Generate
                                            </Button>

                                            <Link v-show="category.refreshable"
                                                  :href="route('audit_autopay.index')"
                                                  class="px-5 py-3" preserve-state preserve-scroll>
                                                Refresh
                                            </Link>

                                            <a v-show="category.viewable"
                                               :href="route('other_audit_autopay.download', { other_audit_payroll_category: category.id })"
                                               class="px-5 py-3">
                                                Download Autopay
                                            </a>

                                            <span v-show="category.viewable"> | </span>

                                            <a v-show="category.viewable"
                                               :href="route('other_audit_autopay.downloadMfb', { other_audit_payroll_category: category.id })"
                                               class="px-5 py-3">
                                                Download MFB
                                            </a>

                                            <span v-show="category.viewable"> | </span>

                                            <Link v-show="category.viewable"
                                                  href="#"
                                                  class="px-5 py-3" preserve-state preserve-scroll>
                                                View Schedule
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </TableCell>
                    </TableRow>
                </TableBody>

                <TableBody>
                    <TableRow v-if="payrolls.data.length === 0">
                        <TableCell colspan="6" class="text-xs font-medium text-gray-700 uppercase tracking-wider">
                            No Payroll
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <pagination :links="payrolls.links"/>
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon'
    import Layout from '@/Shared/Layout'
    import Pagination from '@/Shared/Pagination'
    import { Link } from '@inertiajs/vue3'
    import { Button } from '@/Components/ui/button'
    import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

    export default {
        layout: Layout,

        props: {
            payrolls: Object,
        },

        components: {
            Icon,
            Link,
            Pagination,
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
                status: {
                    pending: 'bg-yellow-100 text-yellow-800',
                    running: 'bg-pink-100 text-pink-800',
                    completed: 'bg-green-100 text-green-800',
                    incomplete: 'bg-blue-100 text-blue-800',
                },
                show_detail: [],
            }
        },

        methods: {
            show(payroll) {
                this.show_detail[payroll] = !this.show_detail[payroll]
            }
        },
    }
</script>
