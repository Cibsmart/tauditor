<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Employees</h1>
    <div class="mb-6 flex justify-between items-center">
      <!-- Search Filter goes here -->
<!--       <search-filter v-model="form.search" class="w-full max-w-lg mr-4">
      </search-filter> -->
      <div></div>
      <inertia-link href="#" class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap ">
        <span class="hidden md:inline">New Employee</span>
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
                  MDA & Department
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
              <tr>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">Bernard Lane</div>
                      <div class="text-sm leading-5 text-gray-500">WR/PER/00056454</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-900">Local Government Education Authority</div>
                  <div class="text-sm leading-5 text-gray-500">Nnewi South LGEA</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-900">H/M MASTER</div>
                  <div class="text-sm leading-5 text-gray-500">G/L 13</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-900">0048304757</div>
                  <div class="text-sm leading-5 text-gray-500">Access Bank PLC</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Active
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- <div class="bg-white rounded shadow-none overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <thead>
          <tr class="text-left font-bold">
            <th class="px-6 pt-6 pb-4">Employee Name</th>
            <th class="px-6 pt-6 pb-4">Date of Birth</th>
            <th class="px-6 pt-6 pb-4" colspan="2">Gender</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(employee, index) in employees.data" :key="employee.id" class="hover:bg-gray-100
          focus-within:bg-gray-100">
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" href="#">
                {{ employee.name}}
              </inertia-link>
            </td>
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" href="#">
                {{ employee.date_of_birth }}
              </inertia-link>
            </td>
            <td class="border-t">
              <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" href="#">
                {{ employee.gender }}
              </inertia-link>
            </td>
            <td class="border-t w-px">
              <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" href="#">
                <icon name="cheveron-right" class="block w-6 h-6 text-gray-500 fill-current" />
              </inertia-link>
            </td>
          </tr>

          <tr v-if="employees.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No Schedule Uploaded Yet</td>
          </tr>
        </tbody>
      </table>
    </div> -->
    <pagination :links="employees.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'

export default {
  metaInfo: { title: 'Employees' }, 
  layout: Layout,

  props: {
    employees: Object,
  },

  components: {
    Icon,
    Pagination,
  },
}
</script>