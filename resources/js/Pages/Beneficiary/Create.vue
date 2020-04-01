<template>
<div>
    <h1 class="mb-8 font-bold text-3xl">Create Beneficiary</h1>
    <div class="mb-6 flex justify-between items-center">
        <!-- Search Filter goes here -->
        <search-filter v-model="search_form.search" class="w-full max-w-lg mr-4">
        </search-filter>
        <div></div>
        <inertia-link href="#" class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap ">
            <span class="hidden md:inline">View Beneficiaries </span>
        </inertia-link>
    </div>

    <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow bg-white overflow-hidden sm:rounded-lg border-b border-white-200 p-6">
                <form class="w-full max-w-lg m-auto" @submit.prevent="submit">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
                                First Name
                            </label>
                            <input v-model="beneficiary_form.first_name" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-first-name" type="text" placeholder="First Name">
                           
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-last-name">
                                Last Name
                            </label>
                            <input v-model="beneficiary_form.last_name"  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-last-name" type="text" placeholder="Last Name">
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                                Date of Birth
                            </label>
                            <input v-model="beneficiary_form.date_of_birth"  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-date-of-birth" type="date" placeholder="Date of Birth">
                            <p class="text-gray-600 text-xs italic">Pick a Date from the Calender Input</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                       
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                                Domain
                            </label>
                            <div class="relative">
                                <select v-model="beneficiary_form.domain_id" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                    <option value ="1">New Mexico</option>
                                    <option value ="2">Missouri</option>
                                    <option value ="3">Texas</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                                </div>
                            </div>
                        </div>
                          <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                                Beneficiary Type
                            </label>
                            <div class="relative">
                                <select v-model="beneficiary_form.beneficiary_type_id" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                    <option value ="1">New Mexico</option>
                                    <option value ="2">Missouri</option>
                                    <option value ="3">Texas</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                                </div>
                            </div>
                        </div>
                       
                        
                    </div>
                    <div class="md:flex md:items-center">           
                        <div class="md:w-1/3">
                            
                            <button type="submit" class="hover:bg-blue-500 px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap">
                              Add Beneficiary
                            </button>
                            
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import SearchFilter from '@/Shared/SearchFilter'

import mapValues from 'lodash/mapValues'
import pickBY from 'lodash/pickBY'
import throttle from 'lodash/throttle'

export default {
    metaInfo: {
        title: 'Add Beneficiary'
    },
    layout: Layout,

    props: {
        filters: Object,
    },

    components: {
        Icon,
        SearchFilter,
    },

    data() {
        return {
            search_form: {
              search: this.filters.search,
            },
            beneficiary_form: {
              first_name: null,
              last_name: null,
              date_of_birth: null,
              domain_id: null,
              beneficiary_type_id: null,
          },
        }
    },

    methods: {
      submit() {
        this.$inertia.post('/beneficiaries/store', this.beneficiary_form)
      },
    },

    watch: {
        form: {
            handler: throttle(function () {
                let query = pickBY(this.search_form)
                this.$inertia.replace(this.route('beneficiaries.index', Object.keys(query).length ? query : {
                    remember: 'forget'
                }))
            }, 300),
            deep: true,
        },
    },
}
</script>
