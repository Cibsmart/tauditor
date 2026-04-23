<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <form @submit.prevent="submit">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                Basic Information
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="flex w-full flex-col lg:flex-row">
                    <text-input
                        v-model="form.last_name"
                        placeholder="Last Name"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="Last Name"
                        :errors="$page.props.errors.last_name"
                        required
                    >
                    </text-input>

                    <text-input
                        v-model="form.first_name"
                        placeholder="First Name"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="First Name"
                        :errors="$page.props.errors.first_name"
                        required
                    >
                    </text-input>

                    <text-input
                        v-model="form.middle_name"
                        placeholder="Middle Name"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="Middle Name"
                        :errors="$page.props.errors.middle_name"
                    >
                    </text-input>
                </div>

                <div class="flex w-full flex-col lg:flex-row">
                    <date-picker
                        v-model="form.date_of_birth"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        from="1980"
                        label="Date Of Birth"
                        :errors="$page.props.errors.date_of_birth"
                        required
                    >
                    </date-picker>

                    <select-input
                        v-model="form.gender_id"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="Gender"
                        :errors="$page.props.errors.gender_id"
                        required
                    >
                        <option
                            v-for="gender in data.genders"
                            :key="gender.id"
                            :value="gender.id"
                            v-text="gender.name"
                        ></option>
                    </select-input>

                    <select-input
                        v-model="form.marital_status_id"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="Marital Status"
                        :errors="$page.props.errors.marital_status_id"
                        required
                    >
                        <option
                            v-for="marital_status in data.marital_statues"
                            :key="marital_status.id"
                            :value="marital_status.id"
                            v-text="marital_status.name"
                        ></option>
                    </select-input>
                </div>

                <div class="flex w-full flex-col lg:flex-row">
                    <text-input
                        v-model="form.phone_number"
                        placeholder="Phone Number"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="Phone Number"
                        :errors="$page.props.errors.phone_number"
                        required
                    >
                    </text-input>

                    <text-input
                        v-model="form.email"
                        placeholder="Email"
                        class="w-full pr-6 pb-8 lg:w-2/3"
                        label="Email"
                        :errors="$page.props.errors.email"
                    >
                    </text-input>
                </div>

                <div class="flex w-full flex-col lg:flex-row">
                    <select-input
                        v-model="form.state_id"
                        class="w-full pr-6 pb-8 lg:w-1/2"
                        label="State of Origin"
                        :errors="$page.props.errors.state_id"
                        @update:modelValue="stateChanged"
                        required
                    >
                        <option
                            v-for="state in data.states"
                            :key="state.id"
                            :value="state.id"
                            v-text="state.name"
                        ></option>
                    </select-input>

                    <select-input
                        v-model="form.local_government_id"
                        class="w-full pr-6 pb-8 lg:w-1/2"
                        label="Local Government of Origin"
                        :errors="$page.props.errors.local_government_id"
                        required
                    >
                        <option
                            v-for="local_government in local_governments"
                            :key="local_government.id"
                            :value="local_government.id"
                            v-text="local_government.name"
                        ></option>
                    </select-input>
                </div>

                <div class="flex w-full flex-col lg:flex-row">
                    <text-input
                        v-model="form.address_line_1"
                        placeholder="Address Line 1"
                        class="w-full pr-6 pb-8 lg:w-1/2"
                        label="Address Line 1"
                        :errors="$page.props.errors.address_line_1"
                        required
                    >
                    </text-input>

                    <text-input
                        v-model="form.address_line_2"
                        placeholder="Address Line 2"
                        class="w-full pr-6 pb-8 lg:w-1/2"
                        label="Address Line 2"
                        :errors="$page.props.errors.address_line_2"
                    >
                    </text-input>
                </div>

                <div class="flex w-full flex-col lg:flex-row">
                    <text-input
                        v-model="form.address_city"
                        placeholder="City"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="City"
                        :errors="$page.props.errors.address_city"
                        required
                    >
                    </text-input>

                    <text-input
                        v-model="form.address_state"
                        placeholder="State"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="State"
                        :errors="$page.props.errors.address_state"
                        required
                    >
                    </text-input>

                    <text-input
                        v-model="form.address_country"
                        placeholder="Country"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="Country"
                        :errors="$page.props.errors.address_country"
                        required
                    >
                    </text-input>
                </div>

                <div class="flex w-full flex-col lg:flex-row">
                    <check-input
                        v-model="form.pensioner"
                        class="w-full pr-6 pb-8 lg:w-1/3"
                        label="Pensioner"
                        @update:modelValue="checkedPensioner"
                    ></check-input>

                    <select-input
                        v-model="form.beneficiary_type_id"
                        class="w-full pr-6 pb-8 lg:w-2/3"
                        label="Beneficiary Type"
                        :errors="$page.props.errors.beneficiary_type_id"
                        required
                    >
                        <option
                            v-for="(
                                beneficiary_type, index
                            ) in beneficiary_types"
                            :key="index"
                            :value="beneficiary_type.id"
                            v-text="beneficiary_type.name"
                        ></option>
                    </select-input>
                </div>
            </div>

            <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
                <span class="flex justify-end">
                    <Button type="submit" size="lg"> Save & Continue </Button>
                </span>
            </div>
        </form>
    </div>
</template>

<script>
import TextInput from '@/Shared/TextInput';
import CheckInput from '@/Shared/CheckInput';
import DatePicker from '@/Shared/DatePicker';
import SelectInput from '@/Shared/SelectInput';
import { Button } from '@/Components/ui/button';

export default {
    name: 'BasicInformation',

    props: {
        data: Object,
    },

    components: {
        TextInput,
        CheckInput,
        DatePicker,
        SelectInput,
        Button,
    },

    data() {
        return {
            local_governments: null,
            beneficiary_types: null,

            form: {
                last_name: null,
                first_name: null,
                middle_name: null,
                date_of_birth: null,
                gender_id: null,
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
                pensioner: false,
                beneficiary_type_id: null,
            },
        };
    },

    mounted() {
        this.checkedPensioner();
    },

    methods: {
        submit() {
            this.$inertia.post(route('beneficiaries.store'), this.form);
        },

        stateChanged(value) {
            this.local_governments = this.data.local_governments.filter(
                (lga) => lga.state_id === value,
            );
        },

        checkedPensioner() {
            this.beneficiary_types = this.data.beneficiary_types.filter(
                (type) => type.pensioners === this.form.pensioner,
            );
        },
    },
};
</script>
