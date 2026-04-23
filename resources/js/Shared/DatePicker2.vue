<template>
    <div class="mb-5 w-64">
        <label for="datepicker" class="mb-1 block font-bold text-gray-700"
            >Select Date</label
        >
        <div class="relative">
            <input type="hidden" name="date" ref="date" />
            <input
                type="text"
                readonly
                v-model="datepickerValue"
                @click="showDatepicker = !showDatepicker"
                @keydown.escape="showDatepicker = false"
                placeholder="Select date"
                id="datepicker"
                class="focus:shadow-outline w-full rounded-lg py-3 pr-10 pl-4 leading-none font-medium text-gray-600 shadow-sm focus:outline-none"
            />

            <div class="absolute top-0 right-0 px-3 py-2">
                <svg
                    class="h-6 w-6 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>
            </div>

            <div
                class="absolute top-0 left-0 mt-12 rounded-lg bg-white p-4 shadow"
                style="width: 17rem"
                @click.away="showDatepicker = false"
            >
                <!-- x-show.transition="showDatepicker"  -->

                <div class="mb-2 flex items-center justify-between">
                    <div>
                        <span
                            v-text="month_names[month]"
                            class="text-lg font-bold text-gray-800"
                        ></span>
                        <span
                            v-text="year"
                            class="ml-1 text-lg font-normal text-gray-600"
                        ></span>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="inline-flex cursor-pointer rounded-full p-1 transition duration-100 ease-in-out hover:bg-gray-200"
                            :class="{
                                'cursor-not-allowed opacity-25': month === 0,
                            }"
                            :disabled="month === 0"
                            @click="getNoOfDays('sub')"
                        >
                            <svg
                                class="inline-flex h-6 w-6 text-gray-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="inline-flex cursor-pointer rounded-full p-1 transition duration-100 ease-in-out hover:bg-gray-200"
                            :class="{
                                'cursor-not-allowed opacity-25': month === 11,
                            }"
                            :disabled="month === 11"
                            @click="getNoOfDays('add')"
                        >
                            <svg
                                class="inline-flex h-6 w-6 text-gray-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="-mx-1 mb-3 flex flex-wrap">
                    <template v-for="(day, index) in days">
                        <div style="width: 14.26%" class="px-1" :key="index">
                            <div
                                v-text="day"
                                class="text-center text-xs font-medium text-gray-800"
                            ></div>
                        </div>
                    </template>
                </div>

                <div class="-mx-1 flex flex-wrap">
                    <template v-for="blankday in blank_days">
                        <div
                            style="width: 14.28%"
                            class="border border-transparent p-1 text-center text-sm"
                        ></div>
                    </template>
                    <template v-for="(date, dateIndex) in no_of_days">
                        <div
                            style="width: 14.28%"
                            class="mb-1 px-1"
                            :key="dateIndex"
                        >
                            <div
                                @click="getDateValue(date)"
                                v-text="date"
                                class="cursor-pointer rounded-full text-center text-sm leading-loose leading-none transition duration-100 ease-in-out"
                                :class="{
                                    'bg-blue-500 text-white':
                                        isToday(date) === true,
                                    'text-gray-700 hover:bg-blue-200':
                                        isToday(date) === false,
                                }"
                            ></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DatePicker',

    data() {
        return {
            showDatepicker: false,
            datepickerValue: '',

            month: '',
            year: '',
            no_of_days: [],
            blank_days: [],
            days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
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
        this.getNoOfDays();
    },

    methods: {
        initDate() {
            let today = new Date();
            this.month = today.getMonth();
            this.year = today.getFullYear();
            this.datepickerValue = new Date(
                this.year,
                this.month,
                today.getDate(),
            ).toDateString();
        },

        isToday(date) {
            const today = new Date();
            const d = new Date(this.year, this.month, date);

            return today.toDateString() === d.toDateString();
        },

        getDateValue(date) {
            let selectedDate = new Date(this.year, this.month, date);
            this.datepickerValue = selectedDate.toDateString();

            this.$refs.date.value =
                selectedDate.getFullYear() +
                '-' +
                ('0' + selectedDate.getMonth()).slice(-2) +
                '-' +
                ('0' + selectedDate.getDate()).slice(-2);

            this.showDatepicker = false;
        },

        getNoOfDays(act = '') {
            if (act === 'add') {
this.month++;
}

            if (act === 'sub') {
this.month--;
}

            let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();

            // find where to start calendar day of week
            let dayOfWeek = new Date(this.year, this.month).getDay();
            let blank_days_array = [];

            for (let i = 1; i <= dayOfWeek; i++) {
                blank_days_array.push(i);
            }

            let daysArray = [];

            for (let i = 1; i <= daysInMonth; i++) {
                daysArray.push(i);
            }

            this.blank_days = blank_days_array;
            this.no_of_days = daysArray;
        },
    },
};
</script>
