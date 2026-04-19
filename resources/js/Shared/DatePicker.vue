<template>
    <div>
        <fieldset>
            <legend v-if="label" :for="id" class="mb-2 block text-sm text-gray-800">
                {{ label }} <span v-show="required && label" class="text-red-600 ml-1 font-bold">*</span>
            </legend>
            <div class="mt-1 bg-white rounded-md shadow-sm" :class="{ 'pt-px rounded border border-red-500' : errors.length }" ref="input">
                <div class="-mt-px flex">
                    <div class="w-1/4 flex min-w-0">
                        <select v-model="day" id="day"
                                class="form-select relative block w-full rounded-none rounded-l-md bg-transparent focus:outline-none focus:border-indigo-500 focus:shadow focus:z-10 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                            <option value="" selected disabled>{{ 'Day' }}</option>
                            <option v-for="(day, index) in days" :key="index" :value="day" v-text="day"></option>
                        </select>
                    </div>
                    <div class="-ml-px w-2/4 flex-1 min-w-0">
                        <select v-model="month" id="month"
                                class="form-select relative block w-full rounded-none bg-transparent focus:outline-none focus:border-indigo-500 focus:shadow focus:z-10 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                            <option value="" selected disabled>{{ 'Month' }}</option>
                            <option v-for="(month, index) in month_names" :key="index" :value="month" v-text="month"></option>
                        </select>
                    </div>
                    <div class="-ml-px flex-1 min-w-0">
                        <select v-model="year" id="year"
                                class="form-select relative block w-full rounded-none rounded-r-md bg-transparent focus:outline-none focus:border-indigo-500 focus:shadow focus:z-10 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                            <option value="" selected disabled>{{ 'Year' }}</option>
                            <option v-for="(year, index) in years" :key="index" :value="year" v-text="year"></option>
                        </select>
                    </div>
                </div>
            </div>
        </fieldset>

        <div v-if="errors.length" class="text-red-800 mt-2 text-sm">
            {{ errors[0] }}
        </div>
    </div>
</template>

<script>
let counter = 0

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
            month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        }
    },

    mounted() {
        this.initDate()
    },

    watch: {
        day() {
            this.setDate()
        },

        month() {
            this.setDate()
        },

        year() {
            this.setDate()
        },

        date(date) {
            this.$emit('update:modelValue', date)
        },
    },

    methods: {
        setDate() {
            this.date = this.day + ' ' + this.month + ' ' + this.year
        },

        initDate() {
            let today = new Date()
            let year = today.getFullYear()

            for (let i = 1; i <= 31; i++) {
                this.days.push(i)
            }

            let start = this.from == null ? year - 40 : this.from
            let end = this.to == null ? year : this.to

            for (let i = end; i >= start; i--) {
                this.years.push(i)
            }
        },
    },
}
</script>
