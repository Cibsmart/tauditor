<template>
  <div class="container p-8 bg-white">
    
    <div class="mb-6 flex justify-between items-center">
      <h1 class="mb-8 font-bold text-3xl">Deductions</h1>
      <inertia-link href="#" class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap ">
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
          <select-input @input="nameChange" v-model="form.deductiontype">
            <option disabled value="">Select Deduction Type</option>
            <option
              v-for="(deductiontype, index) in deductiontypes.data" 
              :key="deductiontype.deduction_type_id"
              :value="deductiontype.deduction_type_id">
                {{ deductiontype.deduction_type }}
            </option>
          </select-input>
          <div class="text-red-600" v-if="$page.errors.deductiontype">{{ $page.errors.deductiontype[0] }}</div>
        </div>
          <div class="col-end-6 col-span-2">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
              Deduction Name
            </label>
            <div class="text-red-600" v-if="$page.errors.deductionname">{{ $page.errors.deductionname[0] }}</div>
            <div style="position:relative;" >
            <select-input
              v-model="form.deductionname"
              style = "position:absolute; top:0px; left:0px; width:100%;"
            >
              <option v-for="(deductionname, index) in deductionnamesData" 
                  :key="deductionname.deduction_name_id"
                  :value="deductionname.deduction_name_id"
              >
                  {{ deductionname.deduction_name }}
              </option>
          </select-input>
          <input type="text" v-model="form.deductionname"
             style =  "position:absolute;
              top:5px;
              left:5px;
              width:90%;
              padding:1px;"
          >
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
            Value-based Deduction
          </label>
          <text-input  type="text" placeholder="Amount" v-model="form.fixed_value"></text-input>
          <div class="text-red-600" v-if="$page.errors.fixed_value">{{ $page.errors.fixed_value[0] }}</div>
        </div>

        <div class="col-start-1">
          <button 
          class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap"
          type="submit">
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
  metaInfo: { title: 'deductions' },
  layout: Layout,

  props: {
    deductiontypes: Object,
    deductionnames: Object,
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
        deductiontype:'',
        deductionname:'',
        value_type:'',
        percentage_value:'',
        fixed_value:'',
      },
      deductionnamesData: [],
    }
  },

  methods: {
    nameChange(value) {
      this.deductionnamesData = this.deductionnames.data.filter(item => {
        return item.deduction_name_id == value;
      })
    },
    submit() {
      this.$inertia.post('store', this.form)
    },
  },
}
</script>
