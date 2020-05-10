<template>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <form @submit.prevent="submit">
        <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
            Basic Information
        </div>

        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col lg:flex-row w-full">
                <text-input v-model="form.last_name"
                            placeholder="Last Name" class="pr-6 pb-8 w-full lg:w-1/3"
                            label="Last Name" :errors="$page.errors.last_name" required>
                </text-input>

                <text-input v-model="form.first_name"
                            placeholder="First Name" class="pr-6 pb-8 w-full lg:w-1/3"
                            label="First Name" :errors="$page.errors.first_name" required>
                </text-input>

                <text-input v-model="form.middle_name"
                            placeholder="Middle Name" class="pr-6 pb-8 w-full lg:w-1/3"
                            label="Middle Name" :errors="$page.errors.middle_name" required>
                </text-input>
            </div>

            <div class="flex flex-col lg:flex-row w-full">
                <date-picker v-model="form.date_of_birth" class="pr-6 pb-8 w-full lg:w-1/3" from="1980"
                            label="Date Of Birth" :errors="$page.errors.date_of_birth" required>
                </date-picker>

                <select-input v-model="form.gender_id" class="pr-6 pb-8 w-full lg:w-1/3" label="Gender"
                              :errors="$page.errors.gender_id" required>
                    <option v-for="gender in prop.genders" :key="gender.id"
                            :value="gender.id" v-text="gender.name">
                    </option>
                </select-input>

                <select-input v-model="form.marital_status_id" class="pr-6 pb-8 w-full lg:w-1/3" label="Marital Status"
                              :errors="$page.errors.marital_status_id" required>
                    <option v-for="marital_status in prop.marital_statues" :key="marital_status.id"
                            :value="marital_status.id" v-text="marital_status.name">
                    </option>
                </select-input>
            </div>

            <div class="flex flex-col lg:flex-row w-full">
                <text-input v-model="form.phone_number"
                            placeholder="Phone Number" class="pr-6 pb-8 w-full lg:w-1/3"
                            label="Phone Number" :errors="$page.errors.phone_number" required>
                </text-input>

                <text-input v-model="form.email"
                            placeholder="Email" class="pr-6 pb-8 w-full lg:w-2/3"
                            label="Email" :errors="$page.errors.email">
                </text-input>
            </div>

            <div class="flex flex-col lg:flex-row w-full">
                <select-input v-model="form.state_id" class="pr-6 pb-8 w-full lg:w-1/2" label="State of Origin"
                              :errors="$page.errors.state_id" required>
                    <option v-for="state in prop.states" :key="state.id"
                            :value="state.id" v-text="state.name">
                    </option>
                </select-input>

                <select-input v-model="form.local_government_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Local Government of Origin"
                              :errors="$page.errors.local_government_id" required>
                    <option v-for="local_government in prop.local_governments" :key="local_government.id"
                            :value="local_government.id" v-text="local_government.name">
                    </option>
                </select-input>
            </div>

            <div class="flex flex-col lg:flex-row w-full">
                <text-input v-model="form.address_line_1"
                            placeholder="Address Line 1" class="pr-6 pb-8 w-full lg:w-1/2"
                            label="Address Line 1" :errors="$page.errors.address_line_1" required>
                </text-input>

                <text-input v-model="form.address_line_2"
                            placeholder="Address Line 2" class="pr-6 pb-8 w-full lg:w-1/2"
                            label="Address Line 2" :errors="$page.errors.address_line_2">
                </text-input>
            </div>

            <div class="flex flex-col lg:flex-row w-full">
                <text-input v-model="form.city"
                            placeholder="City" class="pr-6 pb-8 w-full lg:w-1/3"
                            label="City" :errors="$page.errors.city" required>
                </text-input>

                <text-input v-model="form.state"
                            placeholder="State" class="pr-6 pb-8 w-full lg:w-1/3"
                            label="State" :errors="$page.errors.state" required>
                </text-input>

                <text-input v-model="form.country"
                            placeholder="Country" class="pr-6 pb-8 w-full lg:w-1/3"
                            label="Country" :errors="$page.errors.country" required>
                </text-input>
            </div>

            <div class="flex flex-col lg:flex-row w-full">
                <check-input v-model="form.pensioner" class="pr-6 pb-8 w-full lg:w-1/3" label="Pensioner" ></check-input>

                <select-input v-model="form.beneficiary_type_id" class="pr-6 pb-8 w-full lg:w-2/3" label="Beneficiary Type"
                              :errors="$page.errors.beneficiary_type_id" required>
                    <option v-for="beneficiary_type in prop.beneficiary_types" :key="beneficiary_type.id"
                            :value="beneficiary_type.id" v-text="beneficiary_type.name">
                    </option>
                </select-input>
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
            <span class="flex justify-end">
                <button type="submit" class="inline-flex btn btn-big btn-indigo">
                    Save & Continue
                </button>
            </span>
        </div>
        </form>
    </div>
</template>

<script>
import TextInput from "@/Shared/TextInput";
import CheckInput from "@/Shared/CheckInput";
import DatePicker from "@/Shared/DatePicker";
import SelectInput from "@/Shared/SelectInput";

export default {
    name: "BasicInformation",

    props: {
        states: Array,
        genders: Array,
        marital_statues: Array,
        beneficiary_types: Array,
        local_governments: Array,
    },

    components: {
        TextInput,
        CheckInput,
        DatePicker,
        SelectInput,
    },

    data() {
        return {
            prop: {
                states: Array,
                genders: Array,
                marital_statues: Array,
                beneficiary_types: Array,
                local_governments: Array,
            },

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
                pensioner: null,
                beneficiary_type_id: null
            }
        }
    },

    methods: {
        submit() {
            this.$inertia.post(route('beneficiaries.store'), this.beneficiary_form);
        }
    },
}
</script>

