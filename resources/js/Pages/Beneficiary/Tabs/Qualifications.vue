<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <form @submit.prevent="submit">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                Qualification
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="flex w-full flex-col lg:flex-row">
                    <h3>Qualification Create Form Goes Here</h3>
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
import SelectInput from '@/Shared/SelectInput';
import { Button } from '@/Components/ui/button';

export default {
    name: 'Qualifications',

    props: {},

    components: {
        TextInput,
        SelectInput,
        Button,
    },

    data() {
        return {};
    },

    methods: {
        submit() {
            this.$inertia.post(route('beneficiaries.store'), this.form);
        },
    },
};
</script>
