<template>
    <div>
        <Head title="Autopay Sub MDA Schedules" />
        <h1 class="mb-4 text-3xl font-bold">
            <Link :href="route('audit_autopay.index')" class="">
                Audit Autopay
            </Link>
            <span class="font-medium">/</span> Autopay Sub MDA Schedules
        </h1>

        <div class="mb-6 flex items-center justify-between"></div>

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
                                        <span
                                            :class="
                                                schedule.pension
                                                    ? 'bg-pink-100 text-pink-800'
                                                    : ''
                                            "
                                            class="rounded-full px-2 text-xs leading-5 font-semibold"
                                        >
                                            {{
                                                schedule.pension
                                                    ? 'Pension'
                                                    : ''
                                            }}
                                        </span>
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
                                <span>Item Count: </span>
                                {{ schedule.item_count }}
                            </div>
                        </TableCell>

                        <TableCell>
                            <span
                                :class="
                                    schedule.uploaded
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                                class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold"
                            >
                                {{
                                    schedule.uploaded
                                        ? 'UPLOADED'
                                        : 'NOT-UPLOADED'
                                }}
                            </span>
                        </TableCell>

                        <TableCell class="text-right">
                            <form
                                v-if="schedule.generated && !schedule.uploaded"
                                :key="schedule.id"
                                @submit.prevent="
                                    upload(
                                        schedule.sub_mda_id,
                                        schedule.mda_name,
                                    )
                                "
                            >
                                <div class="flex items-center justify-end px-5">
                                    <Button
                                        size="sm"
                                        type="submit"
                                        variant="outline"
                                    >
                                        Upload
                                    </Button>
                                </div>
                            </form>

                            <form
                                v-else-if="
                                    schedule.generated && schedule.uploaded
                                "
                                :key="schedule.id"
                                @submit.prevent="
                                    reupload(
                                        schedule.sub_mda_id,
                                        schedule.mda_name,
                                    )
                                "
                            >
                                <div class="flex items-center justify-end px-5">
                                    <Button
                                        size="sm"
                                        type="submit"
                                        variant="outline"
                                    >
                                        Re-upload
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
import Pagination from '@/Shared/Pagination';
import { Link } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { Button } from '@/Components/ui/button';

export default {
    layout: Layout,

    props: {
        schedules: Object,
    },

    components: {
        Button,
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
        return {};
    },

    methods: {
        upload(audit_sub_mda, mda_name) {
            let result = confirm('Confirm Autopay Upload for' + mda_name);

            if (result) {
                let data = new FormData();
                data.append('audit_sub_mda', audit_sub_mda || '');

                //TODO: update to new version
                this.$inertia.post(this.route('interswitch.process'), data, {
                    preserveScroll: true,
                });
            }
        },

        reupload(audit_sub_mda, mda_name) {
            let result = confirm('Confirm Autopay Re-Upload for' + mda_name);

            if (result) {
                let data = new FormData();
                data.append('audit_sub_mda', audit_sub_mda || '');

                //TODO: update to new version
                this.$inertia.post(this.route('interswitch.process'), data, {
                    preserveScroll: true,
                });
            }
        },
    },
};
</script>
