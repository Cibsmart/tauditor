<template>
  <div class="border-l-2">
    <div class="flex mb-4">
      <div class="w-2/12 mr-10">
        <div class="bg-white w-full pt-12">
    <ul class="list-reset">
      <li>
        <a
          href="#"
          id="tab1"
          class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
        >Basic Information</a>
      </li>
      <li>
        <a
          href="#"
          id="tab1"
          class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
        >Employmennt Details</a>
      </li>
      <li>
        <a
          href="#"
          id="tab1"
          class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
        >Next Of Kin</a>
      </li>
      <li>
        <a
          href="#"
          id="tab1"
          class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
        >Qualification</a>
      </li>
      <li>
        <a
          href="#"
          id="tab1"
          class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
        >Identification</a>
      </li>
      <li>
        <a
          href="#"
          id="tab1"
          class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
        >Work History</a>
      </li>
      <li>
        <a
          href="#"
          id="tab1"
          class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
        >Bank Account</a>
      </li>
    </ul>
  </div>
      </div>
      <div class="w-9/12">
      <div id="tab1" class="active">
        <h1 class="mb-8 font-bold text-3xl">Enroll Beneficiary</h1>
        <div class="mb-6 flex justify-between items-center">
          <!-- Search Filter goes here -->
          <search-filter v-model="search_form.search" class="w-full max-w-lg mr-4"></search-filter>
          <div></div>
          <inertia-link
            href="/beneficiary"
            class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap"
          >
            <span class="hidden md:inline">View Beneficiaries</span>
          </inertia-link>
        </div>

        <div class="flex flex-col">
          <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div
              class="align-middle inline-block min-w-full shadow bg-white overflow-hidden sm:rounded-lg border-b border-white-200 p-6"
            >
              <form class="w-full max-w-2xl m-auto" @submit.prevent="submit">
                <div class="flex flex-wrap -mx-3 mb-6">
                  <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-first-name"
                    >Last Name</label>
                    <input
                      v-model="beneficiary_form.last_name"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-first-name"
                      type="text"
                      placeholder="First Name"
                    />
                  </div>
                  <div class="w-full md:w-1/3 px-3">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >First Name</label>
                    <input
                      v-model="beneficiary_form.first_name"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Last Name"
                    />
                  </div>
                  <div class="w-full md:w-1/3 px-3">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >Middle Name</label>
                    <input
                      v-model="beneficiary_form.middle_name"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Last Name"
                    />
                  </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                  <div class="w-full md:w-1/3 px-3">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-password"
                    >Date of Birth</label>
                    <input
                      v-model="beneficiary_form.date_of_birth"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-date-of-birth"
                      type="date"
                      placeholder="Date of Birth"
                    />
                    <p class="text-gray-600 text-xs italic">Pick a Date from the Calender Input</p>
                  </div>
                  <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >Gender</label>
                    <div class="relative">
                      <select
                        v-model="beneficiary_form.gender_id"
                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        id="grid-state"
                      >
                        <option selected>Select Gender</option>
                        <option
                          v-for="gender in gender"
                          :key="gender.id"
                          :value="gender.id"
                        >{{gender.name}}</option>
                      </select>
                      <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                      >
                        <svg
                          class="fill-current h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                          />
                        </svg>
                      </div>
                    </div>
                  </div>
                  <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >Marital Status</label>
                    <div class="relative">
                      <select
                        v-model="beneficiary_form.marital_status_id"
                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        id="grid-state"
                      >
                        <option selected>Select Status</option>
                        <option
                          v-for="marital_status in marital_status"
                          :key="marital_status.id"
                          :value="marital_status.id"
                        >{{marital_status.name}}</option>
                      </select>
                      <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                      >
                        <svg
                          class="fill-current h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                          />
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                  <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-first-name"
                    >Email</label>
                    <input
                      v-model="beneficiary_form.email"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-first-name"
                      type="email"
                      placeholder="Email Address"
                    />
                  </div>

                  <div class="w-full md:w-1/3 px-3">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >Phone</label>
                    <input
                      v-model="beneficiary_form.phone_number"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Phone"
                    />
                  </div>

                  <div class="w-full md:w-1/3 px-3">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >Verification Number</label>
                    <input
                      v-model="beneficiary_form.verification_number"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Verification Number"
                    />
                  </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                  <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >State of Origin</label>
                    <div class="relative">
                      <select
                        v-model="beneficiary_form.state_id"
                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        id="grid-state"
                      >
                        <option selected>Select State</option>
                        <option
                          v-for="states in states"
                          :key="states.id"
                          :value="states.id"
                        >{{states.name}}</option>
                      </select>
                      <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                      >
                        <svg
                          class="fill-current h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                          />
                        </svg>
                      </div>
                    </div>
                  </div>
                  <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >LGA</label>
                    <div class="relative">
                      <select
                        v-model="beneficiary_form.local_government_id"
                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        id="grid-state"
                      >
                        <option selected>Select LGA</option>
                        <option v-for="lga in lga" :key="lga.id" :value="lga.id">{{lga.name}}</option>
                      </select>
                      <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                      >
                        <svg
                          class="fill-current h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                          />
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                  <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >Domain</label>
                    <div class="relative">
                      <select
                        v-model="beneficiary_form.domain_id"
                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        id="grid-state"
                      >
                        <option selected>Select Domain</option>
                        <option
                          v-for="domain in domains"
                          :key="domain.id"
                          :value="domain.id"
                        >{{domain.name}}</option>
                      </select>
                      <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                      >
                        <svg
                          class="fill-current h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                          />
                        </svg>
                      </div>
                    </div>
                  </div>
                  <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >Beneficiary Type</label>
                    <div class="relative">
                      <select
                        v-model="beneficiary_form.beneficiary_type_id"
                        class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        id="grid-state"
                      >
                        <option selected>Select Type</option>
                        <option
                          v-for="beneficiary_type in beneficiary_types"
                          :key="beneficiary_type.id"
                          :value="beneficiary_type.id"
                        >{{beneficiary_type.name}}</option>
                      </select>
                      <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                      >
                        <svg
                          class="fill-current h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                          />
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                  <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >Address 1</label>
                    <textarea
                      v-model="beneficiary_form.address_line_1"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Address 1"
                    ></textarea>
                  </div>
                  <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-state"
                    >Address 2</label>
                    <textarea
                      v-model="beneficiary_form.address_line_2"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Address 2"
                    ></textarea>
                  </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                  <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-first-name"
                    >Address City</label>
                    <input
                      v-model="beneficiary_form.address_city"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-first-name"
                      type="text"
                      placeholder="Address City"
                    />
                  </div>
                  <div class="w-full md:w-1/3 px-3">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >Address State</label>
                    <input
                      v-model="beneficiary_form.address_state"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Address State"
                    />
                  </div>
                  <div class="w-full md:w-1/3 px-3">
                    <label
                      class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                      for="grid-last-name"
                    >Address Country</label>
                    <input
                      v-model="beneficiary_form.address_country"
                      class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                      id="grid-last-name"
                      type="text"
                      placeholder="Address Country"
                    />
                  </div>
                </div>
                <div class="md:flex md:items-center">
                  <div class="md:w-1/3">
                    <button
                      type="submit"
                      class="hover:bg-blue-500 px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap"
                    >Enroll</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      </div>
    </div>
  </div>
</template>

<script>
import Icon from "@/Shared/Icon";
import Layout from "@/Shared/Layout";
import SearchFilter from "@/Shared/SearchFilter";

import mapValues from "lodash/mapValues";
import pickBY from "lodash/pickBY";
import throttle from "lodash/throttle";

export default {
  metaInfo: {
    title: "Add Beneficiary"
  },
  layout: Layout,

  props: {
    filters: Object,
    lga: Array,
    states: Array,
    domains: Array,
    gender: Array,
    marital_status: Array,
    beneficiary_types: Array
  },

  components: {
    Icon,
    SearchFilter,
  },

  data() {
    return {
      search_form: {
        search: this.filters.search
      },
      beneficiary_form: {
        first_name: null,
        middle_name: null,
        last_name: null,
        date_of_birth: null,
        gender_id: null,
        verification_number: null,
        marital_status_id: null,
        state_id: null,
        local_government_id: null,
        phone_number: null,
        email: null,
        address_line_1: null,
        address_line_2: null,
        address_city: null,
        address_state: null,
        address_country: null,
        domain_id: null,
        beneficiary_type_id: null
      }
    };
  },

  methods: {
    submit() {
      this.$inertia.post("/beneficiaries/store", this.beneficiary_form);
    }
  },

  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBY(this.search_form);
        this.$inertia.replace(
          this.route(
            "beneficiaries.index",
            Object.keys(query).length
              ? query
              : {
                  remember: "forget"
                }
          )
        );
      }, 300),
      deep: true
    }
  }
};
</script>
