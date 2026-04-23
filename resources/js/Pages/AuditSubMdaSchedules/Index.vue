<template>
    <div>
        <Head title="Audit Sub MDA Schedules" />
        <h1 class="mb-4 text-3xl font-bold">
            <Link :href="route('audit_payroll.index')"> Audit Payroll </Link>
            <span class="font-medium">/</span>
            <Link
                :href="
                    route('audit_mda_schedules.index', {
                        audit_payroll_category,
                    })
                "
            >
                MDA Schedules
            </Link>
            <span class="font-medium">/</span> Sub MDA Schedules
        </h1>

        <div class="mb-6 flex items-center justify-between">
            <div></div>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>MDA/Department/Zone</TableHead>
                        <TableHead>Month</TableHead>
                        <TableHead>Total Net Amount</TableHead>
                        <TableHead>Uploaded</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="schedule in schedules.data"
                        :key="schedule.id"
                    >
                        <TableCell>
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm leading-5 font-medium uppercase"
                                    >
                                        {{ schedule.sub_mda_name }}
                                    </div>
                                    <div
                                        class="text-sm leading-5 text-muted-foreground"
                                    >
                                        {{ schedule.mda_name }}
                                    </div>
                                </div>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5">
                                {{ schedule.month }}
                            </div>
                            <div
                                class="text-sm leading-5 text-muted-foreground"
                            >
                                {{ schedule.year }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5">
                                <span class="line-through">N</span>
                                {{ schedule.total_amount }}
                            </div>
                            <div
                                class="text-sm leading-5 text-muted-foreground"
                            >
                                <span>Head Count: </span>
                                {{ schedule.head_count }}
                            </div>
                        </TableCell>

                        <TableCell>
                            <span
                                :class="
                                    schedule.uploaded
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                                class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold uppercase"
                            >
                                {{ schedule.uploaded ? 'uploaded' : 'pending' }}
                            </span>
                        </TableCell>

                        <TableCell class="text-right">
                            <form
                                v-show="schedule.uploaded && !schedule.archived"
                                :key="schedule.sub_mda_name"
                                class="inline"
                                @submit.prevent="
                                    reupload(schedule.id, schedule.sub_mda_name)
                                "
                            >
                                <Button size="sm" type="submit" variant="ghost">
                                    Re-upload
                                </Button>
                            </form>

                            <Link
                                v-if="schedule.uploaded"
                                :href="
                                    route('audit_pay_schedules.index', {
                                        audit_sub_mda_schedule: schedule.id,
                                    })
                                "
                                class="px-5 py-3"
                            >
                                View Details
                            </Link>

                            <form
                                v-else
                                :key="schedule.id"
                                @submit.prevent="upload(schedule.id)"
                            >
                                <div class="flex items-center">
                                    <file-input
                                        v-model="
                                            form.schedule_file[schedule.id]
                                        "
                                        :errors="form.errors.schedule_file"
                                        accept="file/*"
                                        class="w-full pr-6"
                                        type="file"
                                    />
                                    <Button size="sm" type="submit">
                                        Upload
                                    </Button>
                                </div>
                            </form>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="schedules.data.length === 0">
                        <TableCell
                            class="text-xs font-medium tracking-wider uppercase"
                            colspan="6"
                        >
                            No Pay Schedule
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <pagination :links="schedules.links" />
    </div>
</template>

<script>
import Icon from '@/Shared/Icon';
import Layout from '@/Shared/Layout';
import FileInput from '@/Shared/FileInput';
import Pagination from '@/Shared/Pagination';
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
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
        schedules: Object,
        audit_payroll_category: Number,
    },

    components: {
        Icon,
        Link,
        FileInput,
        Pagination,
        Button,
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
        });
        return { form };
    },

    methods: {
        upload(audit_sub_mda) {
            this.form
                .transform((data) => ({
                    audit_sub_mda: audit_sub_mda ? audit_sub_mda : '',
                    schedule_file: data.schedule_file[audit_sub_mda],
                }))
                .post('/audit_pay_schedules/store', {
                    preserveScroll: true,
                });
        },

        reupload(audit_sub_mda, mda_name) {
            let result = confirm('Confirm Re-Upload for' + mda_name);

            if (result) {
                this.form.post(
                    `/audit_pay_schedules/${audit_sub_mda}/destroy`,
                    {
                        preserveScroll: true,
                    },
                );
            }
        },
    },
};
</script>
