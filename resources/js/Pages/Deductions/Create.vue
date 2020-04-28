<template>
  <div>
      <h1 class="mb-4 font-bold text-3xl">
          <inertia-link :href="route('deductions.index')" class="text-indigo-500 hover:text-indigo-700">
              Deductions
          </inertia-link>
          <span class="text-indigo-500 font-medium">/</span> New
      </h1>

      <hr/>

      <div class="mt-8 bg-white rounded shadow-none overflow-hidden max-w-3xl">
          <form @submit.prevent="submit">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <select-input v-model="form.deduction_type" class="pr-6 pb-8 w-full" label="Deduction Type" :errors="$page.errors.deduction_type" @input="nameChange" >
                <option disabled value="" class="text-gray-100">Select Type</option>
                <option v-for="deduction_type in deduction_types" :key="deduction_type.id" :value="deduction_type.id">
                    {{ deduction_type.name }}
                </option>
              </select-input>

              <select-input v-model="form.deduction_name" class="pr-6 pb-8 w-full" label="Deduction Name" :errors="$page.errors.deduction_name">
                  <option value="">Select Name</option>
                  <option v-for="deduction_name in deduction_names_data" :key="deduction_name.id" :value="deduction_name.id">
                      {{ deduction_name.name }}
                  </option>
                  <option value=-1>New Deduction</option>
              </select-input>

              <text-input v-show="form.deduction_name === '-1'" v-model="form.new_deduction"
                          placeholder="New Deduction Name" class="pr-6 pb-8 w-full"
                          label="New Deduction" :errors="$page.errors.new_deduction"></text-input>

              <select-input v-model="form.value_type" class="pr-6 pb-8 w-1/2" label="Value Type" :errors="$page.errors.value_type">
                  <option v-for="value_type in value_types"
                          :key="value_type.id"
                          :value="value_type.id"
                          v-text="value_type.name"
                  >
                  </option>
              </select-input>

              <text-input v-if="form.value_type === 'computed'" class="pr-6 pb-8 w-1/2" label="Computer" value="COMPUTED" disabled></text-input>

              <text-input v-else-if="form.value_type === 'blank'" class="pr-6 pb-8 w-1/2" label="No Value" value="BLANK" disabled></text-input>

              <text-input-trailing v-else-if="form.value_type === 'percentage'" v-model="form.value" type="number" placeholder="20" class="pr-6 pb-8 w-1/2" label="Percentage"
                                  :errors="$page.errors.value" trailing_character="%">
              </text-input-trailing>

              <text-input-leading v-else v-model="form.value" type="number" placeholder="500" class="pr-6 pb-8 w-1/2" label="Amount"
                                  :errors="$page.errors.value" leading_character="N">
              </text-input-leading>

            </div>

            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center">
              <button type="submit" class="btn btn-big btn-indigo">
                Save Deduction
              </button>
            </div>
          </form>
      </div>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import Pagination from '@/Shared/Pagination'
import SelectInput from '@/Shared/SelectInput'
import SearchFilter from '@/Shared/SearchFilter'

import mapValues from 'lodash/mapValues'
import pickBY from 'lodash/pickBY'
import throttle from 'lodash/throttle'
import TextInputLeading from "../../Shared/TextInputLeading";
import TextInputTrailing from "../../Shared/TextInputTrailing";

export default {
    metaInfo: { title: 'Deductions' },
    layout: Layout,

    props: {
        value_types: Array,
        deduction_types: Array,
        deduction_names: Array,
    },

    components: {
      Icon,
      Pagination,
      SearchFilter,
      TextInput,
      SelectInput,
      TextInputLeading,
      TextInputTrailing,
    },

    data(){
        return {
            form: {
              deduction_type: '',
              deduction_name: '',
              value_type: '',
              value: '',
              new_deduction: '',
            },
            selected: 'fixed',
            deduction_names_data: [],
        }
    },

    mounted() {
    },

    methods: {
        nameChange(value) {
            this.deduction_names_data = this.deduction_names.filter(deduction => deduction.deduction_type_id === value)
        },

        submit() {
          this.$inertia.post(route('deductions.store'), this.form)
        },
    },
}
</script>
