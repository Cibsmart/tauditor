<template>
  <div>
    <fieldset>
      <legend v-if="label" :for="id" class="mb-2 block text-sm text-gray-800">
        {{ label }}
        <span v-show="required && label" class="ml-1 font-bold text-red-600"
          >*</span
        >
      </legend>
      <div
        class="mt-1 rounded-md bg-white shadow-sm"
        :class="{
          'rounded border border-red-500 pt-px': errors.length,
        }"
        ref="input"
      >
        <div class="-mt-px flex">
          <div class="flex w-1/4 min-w-0">
            <select
              v-model="day"
              id="day"
              class="relative block w-full form-select rounded-none rounded-l-md bg-transparent transition duration-150 ease-in-out focus:z-10 focus:border-indigo-500 focus:shadow focus:outline-none sm:text-sm sm:leading-5"
            >
              <option value="" selected disabled>
                {{ 'Day' }}
              </option>
              <option
                v-for="(day, index) in days"
                :key="index"
                :value="day"
                v-text="day"
              ></option>
            </select>
          </div>
          <div class="-ml-px w-2/4 min-w-0 flex-1">
            <select
              v-model="month"
              id="month"
              class="relative block w-full form-select rounded-none bg-transparent transition duration-150 ease-in-out focus:z-10 focus:border-indigo-500 focus:shadow focus:outline-none sm:text-sm sm:leading-5"
            >
              <option value="" selected disabled>
                {{ 'Month' }}
              </option>
              <option
                v-for="(month, index) in month_names"
                :key="index"
                :value="month"
                v-text="month"
              ></option>
            </select>
          </div>
          <div class="-ml-px min-w-0 flex-1">
            <select
              v-model="year"
              id="year"
              class="relative block w-full form-select rounded-none rounded-r-md bg-transparent transition duration-150 ease-in-out focus:z-10 focus:border-indigo-500 focus:shadow focus:outline-none sm:text-sm sm:leading-5"
            >
              <option value="" selected disabled>
                {{ 'Year' }}
              </option>
              <option
                v-for="(year, index) in years"
                :key="index"
                :value="year"
                v-text="year"
              ></option>
            </select>
          </div>
        </div>
      </div>
    </fieldset>

    <div v-if="errors.length" class="mt-2 text-sm text-red-800">
      {{ errors[0] }}
    </div>
  </div>
</template>

<script>
let counter = 0;

export default {
  name: 'DatePicker',
  inheritAttrs: false,

  props: {
    modelValue: [String, Number, Boolean],
    label: String,
    errors: { type: Array, default: () => [] },
    id: { type: String, default: () => `date-input-${counter++}` },
    to: { type: [Number, String], default: null },
    from: { type: [Number, String], default: null },
    required: { type: Boolean, default: false },
  },

  emits: ['update:modelValue'],

  data() {
    return {
      day: null,
      month: null,
      year: null,
      date: null,

      days: [],
      years: [],
      month_names: [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
      ],
    };
  },

  mounted() {
    this.initDate();
  },

  watch: {
    day() {
      this.setDate();
    },

    month() {
      this.setDate();
    },

    year() {
      this.setDate();
    },

    date(date) {
      this.$emit('update:modelValue', date);
    },
  },

  methods: {
    setDate() {
      this.date = this.day + ' ' + this.month + ' ' + this.year;
    },

    initDate() {
      let today = new Date();
      let year = today.getFullYear();

      for (let i = 1; i <= 31; i++) {
        this.days.push(i);
      }

      let start = this.from == null ? year - 40 : this.from;
      let end = this.to == null ? year : this.to;

      for (let i = end; i >= start; i--) {
        this.years.push(i);
      }
    },
  },
};
</script>
