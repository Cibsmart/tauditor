<template>
    <div>
        <Head title="Audit MDA Schedules" />
        <h1 class="mb-4 font-bold text-3xl">
            <Link :href="route('audit_payroll.index')" class="text-indigo-500 hover:text-indigo-700">
                Audit Payroll
            </Link>
            <span class="text-indigo-500 font-medium">/</span> MDA Schedules
        </h1>

        <div class="mb-6 flex justify-between items-center">
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>MDA</TableHead>
                        <TableHead>Month</TableHead>
                        <TableHead>Total Amount</TableHead>
                        <TableHead>Uploaded</TableHead>
                        <TableHead class="text-right"></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="schedule in schedules.data" :key="schedule.id">
                        <TableCell>
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                        {{ schedule.mda_name }}
                                        <span class="px-2 text-xs leading-5 font-semibold rounded-full"
                                              :class="schedule.pension ? 'bg-pink-100 text-pink-800' : ''">
                                            {{ schedule.pension ? 'Pension' : '' }}
                                        </span>
                                    </div>
                                    <div class="text-sm leading-5 text-gray-600">{{ schedule.domain }}</div>
                                </div>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">
                                {{ schedule.month }}
                            </div>
                            <div class="text-sm leading-5 text-gray-600">
                                {{ schedule.year }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">
                                <span class="line-through">N</span>
                                {{ schedule.total_amount }}
                            </div>
                            <div class="text-sm leading-5 text-gray-600">
                                <span>Head Count: </span>
                                {{ schedule.head_count }}
                            </div>
                        </TableCell>

                        <TableCell>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                  :class="schedule.uploaded
                                  ? 'bg-green-100 text-green-800'
                                  : 'bg-red-100 text-red-800'">
                                {{ schedule.uploaded ? 'UPLOADED' : 'NOT-UPLOADED' }}
                            </span>
                        </TableCell>

                        <TableCell class="text-right">
                            <form v-show="schedule.uploaded && ! schedule.has_sub && ! schedule.archived"
                                  @submit.prevent="reupload(schedule.sub_mda_id, schedule.mda_name)"
                                  class="inline"
                                  :key="schedule.id + schedule.sub_mda_id">
                                <button type="submit"
                                        class="px-5 py-3 h-1/2 bg-transparent font-medium focus:outline-none">
                                    Re-upload
                                </button>
                            </form>

                            <!--  View Sub MDA Details for MDA with Sub MDAs -->
                            <Link v-if="schedule.uploaded && schedule.has_sub"
                                  :href="route('audit_sub_mda_schedules.index', {audit_mda_schedule: schedule.id})"
                                  class="px-5 py-3">
                                View Details
                            </Link>

                            <Link v-else-if="schedule.uploaded"
                                  :href="route('audit_pay_schedules.index', {audit_sub_mda_schedule: schedule.sub_mda_id})"
                                  class="px-5 py-3">
                                View Details
                            </Link>

                            <Link v-else-if="schedule.has_sub"
                                  :href="route('audit_sub_mda_schedules.index', {audit_mda_schedule: schedule.id})"
                                  class="px-5 py-3">
                                Upload
                            </Link>

                            <form v-else @submit.prevent="upload(schedule.sub_mda_id)" :key="schedule.id">
                                <div class="flex items-center">
                                    <file-input v-model="form.schedule_file[schedule.sub_mda_id]"
                                                :errors="form.errors.schedule_file" class="pr-6 w-full" type="file"
                                                accept="file/*"/>
                                    <button type="submit"
                                            class="px-4 py-1 h-1/2 bg-gray-600 hover:bg-gray-700 rounded-sm text-xs font-medium text-white focus:outline-none">
                                        Upload
                                    </button>
                                </div>
                            </form>
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
import FileInput from "@/Shared/FileInput";
import Pagination from '@/Shared/Pagination'
import { Link, useForm } from '@inertiajs/vue3'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

export default {
    layout: Layout,

    props: {
        schedules: Object,
    },

    components: {
        Icon,
        Link,
        FileInput,
        Pagination,
        Table,
        TableHeader,
        TableBody,
        TableRow,
        TableHead,
        TableCell,
    },

    setup() {
        const form = useForm({
            schedule_file: [],
        })
        return { form }
    },

    methods: {
        upload(audit_sub_mda) {
            this.form
                .transform((data) => ({
                    audit_sub_mda: audit_sub_mda ? audit_sub_mda : '',
                    schedule_file: data.schedule_file[audit_sub_mda]
                }))
                .post('/audit_pay_schedules/store', {
                    preserveScroll: true
                })
        },

        reupload(audit_sub_mda, mda_name) {

            let result = confirm('Confirm Re-Upload for' + mda_name);

            if (result) {
                this.form
                    .post(`/audit_pay_schedules/${audit_sub_mda}/destroy`, {
                        preserveScroll: true,
                    })
            }
        }
    }
}
</script>
