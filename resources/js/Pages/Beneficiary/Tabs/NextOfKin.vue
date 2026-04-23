<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <form @submit.prevent="submit">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                Next Of Kin
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="flex w-full flex-col lg:flex-row">
                    <h3>Next Of Kin Create Form Goes Here</h3>
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
import { Button } from '@/Components/ui/button';
import SelectInput from '@/Shared/SelectInput';
import TextInput from '@/Shared/TextInput';

export default {
    name: 'NextOfKin',

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
