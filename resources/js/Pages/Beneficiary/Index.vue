<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Beneficiaries</h1>
    <div class="mb-6 flex justify-between items-center">
      <!-- Search Filter goes here -->
      <search-filter v-model="form.search" class="w-full max-w-lg mr-4">
      </search-filter>
      <div></div>
      <inertia-link href="#" class="btn btn-big btn-indigo">
        <span class="hidden md:inline">New Beneficiary</span>
      </inertia-link>
    </div>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                  Name
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                  MDA
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                  Designation
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                  Bank Details
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="(beneficiary, index) in beneficiaries.data" :key="beneficiary.id">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 font-medium text-gray-900 uppercase" >{{ beneficiary.name }}</div>
                  <div class="text-sm leading-5 text-gray-600">{{ beneficiary.verification_number }}</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-900">{{ beneficiary.mda }}</div>
                  <div class="text-sm leading-5 text-gray-600">
                      {{ beneficiary.sub_mda }}
                      {{ beneficiary.sub_sub_mda }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-900">{{ beneficiary.designation }}</div>
                  <div class="text-sm leading-5 text-gray-600">
                      {{ beneficiary.grade_level }}
                      {{ beneficiary.step }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-900">
                    {{ beneficiary.account_number }}
                  </div>
                  <div class="text-sm leading-5 text-gray-600">
                    {{ beneficiary.bank_name }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="beneficiary.active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'">
                    {{ beneficiary.active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                </td>
              </tr>

              <tr v-if="beneficiaries.data.length === 0">
                <td colspan="6" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">No Beneficiary</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <pagination :links="beneficiaries.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Beneficiaries' },
  layout: Layout,

  props: {
    beneficiaries: Object,
    filters: Object,
  },

  components: {
    Icon,
    Pagination,
    SearchFilter,
  },

  data(){
    return {
      form: {
        search: this.filters.search,
      },
    }
  },

  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
          this.$inertia.replace(this.route('beneficiaries.index', Object.keys(query).length ? query : { remember: 'forget'}))
      }, 300),
      deep: true,
    },
  },
}
</script>
