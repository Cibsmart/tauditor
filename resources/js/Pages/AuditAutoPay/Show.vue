<template>
    <div>
        <Head title="Autopay MDA Schedules" />
        <h1 class="mb-4 font-bold text-3xl">
            <Link :href="route('audit_autopay.index')" class="text-indigo-500 hover:text-indigo-700">
                Audit Autopay
            </Link>
            <span class="text-indigo-500 font-medium">/</span> Autopay Schedules
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
                        <TableHead class="text-right">Actions</TableHead>
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
                                <span>Item Count: </span>
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
                            <!--  View Sub MDA Details for MDA with Sub MDAs -->
                            <Link v-if="schedule.generated && schedule.uploaded && schedule.has_sub" href="#"
                                  class="px-5 py-3">
                                View Details
                            </Link>

                            <Link v-else-if="schedule.generated && schedule.has_sub"
                                  :href="route('audit_autopay.detail', {audit_mda_schedule: schedule.id})"
                                  class="px-5 py-3">
                                Upload
                            </Link>

                            <form v-else-if="schedule.generated && ! schedule.uploaded && ! schedule.has_sub"
                                  @submit.prevent="upload(schedule.sub_mda_id, schedule.mda_name)"
                                  :key="schedule.id">
                                <div class="px-5 flex items-center justify-end">
                                    <button type="submit"
                                            class="px-4 py-1 h-1/2 bg-gray-600 hover:bg-gray-700 rounded-sm text-xs font-medium text-white focus:outline-none">
                                        Upload
                                    </button>
                                </div>
                            </form>

                            <form v-else-if="schedule.generated && schedule.uploaded && ! schedule.has_sub"
                                  @submit.prevent="reupload(schedule.sub_mda_id, schedule.mda_name)"
                                  :key="schedule.id">
                                <div class="px-5 flex items-center justify-end">
                                    <button type="submit"
                                            class="px-4 py-1 h-1/2 bg-green-600 hover:bg-green-700 rounded-sm text-xs font-medium text-white focus:outline-none">
                                        Re-upload
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
import { Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

export default {
    layout: Layout,

    props: {
        schedules: Object,
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
        return {}
    },

    methods: {
        upload(audit_sub_mda, mda_name) {

            let result = confirm('Confirm Autopay Upload for' + mda_name);

            if (result) {
                let data = new FormData();
                data.append('audit_sub_mda', audit_sub_mda || '')

                this.$inertia.post(this.route('interswitch.process'), data, {
                    preserveScroll: true,
                })
            }
        },

        reupload(audit_sub_mda, mda_name) {

            let result = confirm('Confirm Autopay Re-Upload for' + mda_name);

            if (result) {
                let data = new FormData();
                data.append('audit_sub_mda', audit_sub_mda || '')

                this.$inertia.post(this.route('interswitch.process'), data, {
                    preserveScroll: true,
                })
            }
        },
    }
}
</script>
