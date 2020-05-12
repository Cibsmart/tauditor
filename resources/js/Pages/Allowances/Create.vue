<template>
<div>
    <h1 class="mb-8 font-bold text-3xl">Enroll allowance</h1>
    <div class="mb-6 flex justify-between items-center">
        <!-- Search Filter goes here -->
        <!-- <search-filter v-model="search_form.search" class="w-full max-w-lg mr-4">
        </search-filter>
        <div></div>
        <inertia-link href="/allowance" class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap ">
            <span class="hidden md:inline">View Beneficiaries </span>
        </inertia-link> -->
    </div>

    <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow bg-white overflow-hidden sm:rounded-lg border-b border-white-200 p-6">
                <form class="w-full max-w-2xl m-auto" @submit.prevent="submit">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/1 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                                Allowance Name
                            </label>
                            <div class="relative">
                                <select v-model="allowance_form.allowance_name_id" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                   <option selected >Select Type </option>
                                    <option v-for="allowance_name in allowance_names" :key="allowance_name.id" :value="allowance_name.id">{{allowance_name.name}}</option>

                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                                </div>
                            </div>
                        </div>
                            <div class="text-red-600" v-if="$page.errors.allowance_name_id">{{ $page.errors.allowance_name_id[0] }}</div>
                    </div>


                    <div class="flex flex-wrap -mx-3 mb-6">
                       <div class="w-full md:w-1/1 px-3 mb-6">
                          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                               Select Value Type
                            </label>
                       </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                                Fixed Type
                            </label>
                            <input type="radio" v-model="allowance_form.allowance_type" id="fixed_value" v-on:click="toggle_value_type(0)"  name="allowance_type" value="fixed_value">

                            <div class="text-red-600" v-if="$page.errors.allowance_type">{{ $page.errors.allowance_type[0] }}</div>
                        </div>
                          <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                                Percentage Type
                            </label>
                            <input type="radio"  v-model="allowance_form.allowance_type" id="percentage_value" v-on:click="toggle_value_type(1)" name="allowance_type" value="percentage_type">

                            <div class="text-red-600" v-if="$page.errors.allowance_type">{{ $page.errors.allowance_type[0] }}</div>

                        </div>
                    </div>
                      <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/1 px-3 mb-6 md:mb-0">
                            <!-- <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                               Select Value Type
                            </label> -->
                            <!-- <div class="relative" v-if="selected_value_type == 0">
                                <select v-model="allowance_form.percentage_value_id" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                    <option selected >Select Fixed Value </option>
                                    <option v-for="fixed_value in fixed_values" :key="fixed_value.id" :value="fixed_value.id">{{fixed_value.amount}}</option>

                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                                </div>
                            </div>
                            <div class="relative" v-else>
                                <select v-model="allowance_form.percentage_value_id" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                    <option selected >Select Percentage Value </option>
                                    <option v-for="percentage_value in percentage_values" :key="percentage_value.id" :value="percentage_value.id">{{percentage_value.percentage}}</option>

                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                                </div>
                            </div> -->
                            <!-- <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                               Select Value Type
                            </label> -->
                            <div class="relative" v-if="selected_value_type == 0">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-Value">
                                Enter Fixed Value
                                </label>
                                <input v-model="allowance_form.amount"  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-fixed_value" type="text" placeholder="Enter Fixed Value">
                                <div class="text-red-600" v-if="$page.errors.amount">{{ $page.errors.amount[0] }}</div>
                            </div>
                            <div class="relative" v-else>
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-Value">
                                Enter Percentage Value
                                </label>
                                <input v-model="allowance_form.percentage"  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-percentage_value" type="text" placeholder="Enter Percentage Value">

                                <div class="text-red-600" v-if="$page.errors.percentage">{{ $page.errors.percentage[0] }}</div>
                            </div>
                        </div>

                    </div>

                    <div class="md:flex md:items-center">
                        <div class="md:w-1/3">

                            <button type="submit" class="hover:bg-blue-500 px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap">
                              Enroll
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
import Layout from '../../Shared/Layout'
import SearchFilter from '@/Shared/SearchFilter'

export default {
    metaInfo: {
        title: 'Add allowance'
    },
    layout: Layout,

    props: {
        //filters: Object,
        allowance_names:Array,
        percentage_values:Array,
        fixed_values:Array,

    },

    components: {
        Icon,
        SearchFilter,
    },

    data() {
        return {
            // search_form: {
            //   search: this.filters.search,
            // },
            allowance_form: {

                allowance_name_id: null,
                amount:null,
                percentage:null,
                allowance_type:null
            },
            selected_value_type: 0

        }
    },

    methods: {
      submit() {
        this.$inertia.post('store', this.allowance_form)
      },
      toggle_value_type(type){
          this.selected_value_type =  type
      }
    },

    // watch: {
    //     form: {
    //         handler: throttle(function () {
    //             let query = pickBY(this.search_form)
    //             this.$inertia.replace(this.route('allowances.index', Object.keys(query).length ? query : {
    //                 remember: 'forget'
    //             }))
    //         }, 300),
    //         deep: true,
    //     },
    // },
}
</script>
