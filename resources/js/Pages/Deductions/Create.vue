<template>
  <div class="container p-8 bg-white">

    <div class="mb-6 flex justify-between items-center">
      <h1 class="mb-8 font-bold text-3xl">Deductions</h1>
      <inertia-link href="#" class="btn btn-big btn-indigo">
        <span class="hidden md:inline">New Deduction</span>
      </inertia-link>
    </div>

    <hr/>

    <form @submit.prevent="submit" class="container w-2/3 md:w-4/5 sm:w-full p-8">
      <div class="grid row-gap-6 grid-cols-5">
        <div class="col-start-1 col-end-3">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
            Deduction Type
          </label>
          <select-input @input="nameChange" v-model="form.deduction_type">
            <option disabled value="">Select Deduction Type</option>
            <option
              v-for="(deduction_type, index) in deduction_types"
              :key="deduction_type.id"
              :value="deduction_type.id">
                {{ deduction_type.name }}
            </option>
          </select-input>
          <div class="text-red-600" v-if="$page.errors.deduction_type">{{ $page.errors.deduction_type[0] }}</div>
        </div>
          <div class="col-end-6 col-span-2">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
              Deduction Name
            </label>
            <div class="text-red-600" v-if="$page.errors.deduction_name">{{ $page.errors.deduction_name[0] }}</div>
            <div style="position:relative;" >
            <select-input
              v-model="form.deduction_name"
              style = "position:absolute; top:0px; left:0px; width:100%;"
            >
              <option v-for="(deduction_name, index) in deduction_names_data"
                  :key="deduction_name.id"
                  :value="deduction_name.id"
              >
                  {{ deduction_name.name }}
              </option>
          </select-input>
          <input type="text" v-model="form.deduction_name"
             style = "position:absolute; top:5px; left:5px; width:90%; padding:1px;" >
          </div>
        </div>

        <div class="col-start-1 col-end-3">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
            Value Type
          </label>
          <select-input v-model="form.value_type">
            <option disabled value="">Select Value Type</option>
            <option value="percentage_value">Percentage Value Type</option>
            <option value="fixed_value">fixed Value Type</option>
            <option value="computed_value">Computed Value Type</option>
          </select-input>
          <div class="text-red-600" v-if="$page.errors.value_type">{{ $page.errors.value_type[0] }}</div>
        </div>

        <div class="col-start-1 col-end-3" v-show="form.value_type === 'percentage_value'">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
            Percentage Deduction
          </label>
          <text-input  type="text" placeholder="Percentage" v-model="form.percentage_value"></text-input>
          <div class="text-red-600" v-if="$page.errors.percentage_value">{{ $page.errors.percentage_value[0] }}</div>
        </div>

        <div class="col-start-1 col-end-3" v-show="form.value_type === 'fixed_value'">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
            Value-Based Deduction
          </label>
          <text-input  type="text" placeholder="Amount" v-model="form.fixed_value"></text-input>
          <div class="text-red-600" v-if="$page.errors.fixed_value">{{ $page.errors.fixed_value[0] }}</div>
        </div>

        <div class="col-start-1">
          <button class="btn btn-big btn-indigo" type="submit">
            Save
          </button>
        </div>
      </div>
    </form>

    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'

import mapValues from 'lodash/mapValues'
import pickBY from 'lodash/pickBY'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Deductions' },
  layout: Layout,

  props: {
    deduction_types: Array,
    deduction_names: Array,
  },

  components: {
    Icon,
    Pagination,
    SearchFilter,
    TextInput,
    SelectInput,
  },

  data(){
    return {
      form: {
        deduction_type: '',
        deduction_name: '',
        value_type: '',
        percentage_value: '',
        fixed_value: '',
      },
      deduction_names_data: [],
    }
  },

  methods: {
    nameChange(value) {
      this.deduction_names_data = this.deduction_names.filter(item => {
        return item.id === value;
      })
    },

    submit() {
      this.$inertia.post(route('deductions.store'), this.form)
    },
  },
}
</script>
