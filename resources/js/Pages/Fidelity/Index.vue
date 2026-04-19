<template>
    <div>
        <Head title="Mandates" />
        <h1 class="mb-8 font-bold text-3xl">Mandates</h1>
        <div class="mb-6 flex justify-between items-center">
            <!-- Search Filter goes here -->
            <search-filter v-model="form.search" class="w-full max-w-lg mr-4" @reset="reset">
                <label class="block text-gray-700">Mandate Status:</label>
                <select v-model="form.status" class="mt-1 w-full form-select">
                    <option :value="null" />
                    <option v-for="status in statuses" :value="status.id">{{ status.name }}</option>
                </select>
                <label class="block text-gray-700 mt-5">Processed</label>
                <select v-model="form.processed" class="mt-1 w-full form-select">
                    <option :value="null" />
                    <option value="true">Processed</option>
                    <option value="false">Pending</option>
                </select>
            </search-filter>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                SN
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Name/Staff ID (Mandate Reference)
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Account Number/BVN
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Amounts
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Number of Repayments
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Disbursement Date
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="(mandate, index) in mandates.data" :key="mandate.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ mandate.name }}</div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ mandate.verification_number }} ({{ mandate.reference }})
                                    <span v-if="! mandate.processed" class="px-2 inline-flex text-xs lowercase leading-5 font-semibold bg-red-100 text-red-800 rounded-full">
                                        pending
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ mandate.account_number }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ mandate.bvn }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    loan: {{ mandate.loan_amount }}
                                </div>
                                <div class="text-sm leading-5 text-gray-900">
                                    collection: {{ mandate.collection_amount }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    repayments: {{ mandate.number_of_repayments }}
                                </div>
                                <div class="text-sm leading-5 text-gray-900">
                                    repaid: {{ mandate.number_repaid }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ mandate.date_disbursed }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                              <span class="px-2 inline-flex text-xs lowercase leading-5 font-semibold rounded-full"
                                    :class="mandate.color">
                                  {{ mandate.status }}
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                <Link :href="route('fidelity.show', {mandate: mandate.id })" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">view</Link>
                            </td>
                        </tr>

                        <tr v-if="mandates.data.length === 0">
                            <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">No Mandate</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <pagination :links="mandates.links" />
    </div>
</template>

<script>
import Icon from '@/Shared/Icon';
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout';
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import { router, Link } from '@inertiajs/vue3'
import SelectInput from "@/Shared/SelectInput"
import SearchFilter from '@/Shared/SearchFilter'


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
    },

    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status,
                processed: this.filters.processed,
            },
        }
    },

    watch: {
      form: {
          handler: throttle( function() {
              router.get(this.route('fidelity.index'), pickBy(this.form), { preserveState: true })
          }, 150),
          deep: true,
      }
    },

    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
}
</script>
