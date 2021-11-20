<template>
    <div>
        <h1 class="mb-4 font-bold text-3xl">
            <Link :href="route('audit_payroll.index')" class="text-indigo-500 hover:text-indigo-700">
                Audit Payroll
            </Link>
            <span class="text-indigo-500 font-medium">/</span> MDA Schedules
        </h1>

        <div class="mb-6 flex justify-between items-center">
            <!-- Search Filter goes here -->
            <!--            <search-filter v-model="form.search" class="w-full max-w-lg mr-4">-->
            <!--            </search-filter>-->
        </div>

        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div
                    class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                MDA
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Month
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Total Amount
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Uploaded
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">

                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="schedule in schedules.data" :key="schedule.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
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
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ schedule.month }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ schedule.year }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    <span class="line-through">N</span>
                                    {{ schedule.total_amount }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    <span>Head Count: </span>
                                    {{ schedule.head_count }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="schedule.uploaded
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'">
                                {{ schedule.uploaded ? 'UPLOADED' : 'NOT-UPLOADED' }}
                              </span>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">

                                <form v-show="schedule.uploaded && ! schedule.has_sub && ! schedule.archived"
                                      @submit.prevent="reupload(schedule.sub_mda_id, schedule.mda_name)"
                                      class="inline"
                                      :key="schedule.id + schedule.sub_mda_id">
                                    <button type="submit"
                                            class="px-5 py-3 h-1/2 bg-transparent font-medium focus:outline-none">
                                        Re-upload
                                    </button>
                                </form>

                                <!--                                View Sub MDA Details for MDA with Sub MDAs-->
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
                            </td>
                        </tr>

                        <tr v-if="schedules.data.length === 0">
                            <td colspan="6"
                                class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                No Pay Schedule
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <pagination :links="schedules.links"/>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import FileInput from "@/Shared/FileInput";
import Pagination from '@/Shared/Pagination'
import {Link} from '@inertiajs/inertia-vue'

export default {
    metaInfo: {title: 'Audit MDA Schedules'},
    layout: Layout,

    props: {
        schedules: Object,
    },

    components: {
        Icon,
        Link,
        FileInput,
        Pagination,
    },

    data() {
        return {

            form: this.$inertia.form({
                schedule_file: [],
            })
        }
    },

    methods: {
        upload(audit_sub_mda) {
            this.form
                .transform((data) => ({
                    audit_sub_mda: audit_sub_mda ? audit_sub_mda : '',
                    schedule_file: data.schedule_file[audit_sub_mda]
                }))
                .post(this.route('audit_pay_schedules.store'), {
                    preserveScroll: true
                })
        },

        reupload(audit_sub_mda, mda_name) {

            let result = confirm('Confirm Re-Upload for' + mda_name);

            if (result) {
                this.form
                    .post(this.route('audit_pay_schedules.destroy', {audit_sub_mda_schedule: audit_sub_mda}), {
                        preserveScroll: true,
                    })
            }
        }
    }
}
</script>
