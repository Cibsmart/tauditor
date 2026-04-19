<template>
    <div>
        <Head title="Autopay Sub MDA Schedules" />
        <h1 class="mb-4 font-bold text-3xl">
            <Link :href="route('audit_autopay.index')" class="text-indigo-500 hover:text-indigo-700">
                Audit Autopay
            </Link>
            <span class="text-indigo-500 font-medium">/</span> Autopay Sub MDA Schedules
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
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="schedule in schedules.data" :key="schedule.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm leading-5 font-medium text-gray-900 uppercase">
                                            {{ schedule.sub_mda_name }}
                                            <span class="px-2 text-xs leading-5 font-semibold rounded-full"
                                                  :class="schedule.pension ? 'bg-pink-100 text-pink-800' : ''">
                                            {{ schedule.pension ? 'Pension' : '' }}
                                          </span>
                                        </div>
                                        <div class="text-sm leading-5 text-gray-600">{{ schedule.mda_name }}</div>
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
                                    <span>Item Count: </span>
                                    {{ schedule.item_count }}
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

                                <!--                                <Link v-if="schedule.generated && schedule.uploaded" href="#" class="px-5 py-3">-->
                                <!--                                    View Details-->
                                <!--                                </Link>-->

                                <form v-if="schedule.generated && ! schedule.uploaded"
                                      @submit.prevent="upload(schedule.sub_mda_id, schedule.mda_name)"
                                      :key="schedule.id">
                                    <div class="px-5 flex items-center justify-end">
                                        <button type="submit"
                                                class="px-4 py-1 h-1/2 bg-gray-600 hover:bg-gray-700 rounded-sm text-xs font-medium text-white focus:outline-none">
                                            Upload
                                        </button>
                                    </div>
                                </form>

                                <form v-else-if="schedule.generated && schedule.uploaded"
                                      @submit.prevent="reupload(schedule.sub_mda_id, schedule.mda_name)"
                                      :key="schedule.id">
                                    <div class="px-5 flex items-center justify-end">
                                        <button type="submit"
                                                class="px-4 py-1 h-1/2 bg-green-600 hover:bg-green-700 rounded-sm text-xs font-medium text-white focus:outline-none">
                                            Re-upload
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
import Pagination from '@/Shared/Pagination'
import {Link} from '@inertiajs/vue3'

export default {
    layout: Layout,

    props: {
        schedules: Object,
    },

    components: {
        Icon,
        Link,
        Pagination,
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

                //TODO: update to new version
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

                //TODO: update to new version
                this.$inertia.post(this.route('interswitch.process'), data, {
                    preserveScroll: true,
                })
            }
        },
    }
}
</script>
