<template>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <form @submit.prevent="submit">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                Deduction Details
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col lg:flex-row w-full">
                    <h3>Deduction Details Create Form Goes Here</h3>
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
    import SelectInput from "@/Shared/SelectInput";

    export default {
        name: "Deductions",

        props: {
        },

        components: {
            TextInput,
            SelectInput,
        },

        data() {
            return {
            }
        },

        methods: {
            submit() {
                this.$inertia.post(route('beneficiaries.store'), this.form);
            },
        }
    }
</script>

