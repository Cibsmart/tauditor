<template>
    <div>
        <Head title="Create User" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link
                class="text-indigo-400 hover:text-indigo-600"
                :href="route('manage_users.index')"
                >Users</Link
            >
            <span class="font-medium text-indigo-400">/</span> Create
        </h1>
        <div class="max-w-3xl overflow-hidden rounded bg-white shadow">
            <form @submit.prevent="form.post(route('manage_users.store'))">
                <div class="-mr-6 -mb-8 flex flex-wrap p-8">
                    <text-input
                        v-model="form.first_name"
                        :errors="form.errors.first_name"
                        class="w-full pr-6 pb-8"
                        label="First name"
                        required
                    />
                    <text-input
                        v-model="form.last_name"
                        :errors="form.errors.last_name"
                        class="w-full pr-6 pb-8"
                        label="Last name"
                        required
                    />
                    <text-input
                        v-model="form.email"
                        :errors="form.errors.email"
                        class="w-full pr-6 pb-8"
                        label="Email"
                        required
                    />
                    <select-input
                        v-model="form.role"
                        :errors="form.errors.role"
                        class="w-full pr-6 pb-8"
                        label="Role"
                        required
                    >
                        <option disabled value="" class="text-gray-100">
                            Select Role
                        </option>
                        <option
                            v-for="role in roles"
                            :key="role.id"
                            :value="role.id"
                        >
                            {{ role.name }}
                        </option>
                    </select-input>
                    <select-input
                        v-model="form.mfb"
                        :errors="form.errors.microfinance_bank"
                        class="w-full pr-6 pb-8"
                        label="Microfinance Bank"
                        v-if="form.role === mfb_role_id"
                    >
                        <option disabled value="" class="text-gray-100">
                            Select Microfinance Bank
                        </option>
                        <option
                            v-for="mfb in mfbs"
                            :key="mfb.id"
                            :value="mfb.id"
                        >
                            {{ mfb.name }}
                        </option>
                    </select-input>
                    <label-input
                        :model-value="domain"
                        :error="form.errors.domain"
                        class="w-full pr-6 pb-8"
                        label="Domain"
                    />
                </div>
                <div
                    class="flex items-center justify-end border-t border-gray-200 bg-gray-100 px-8 py-4"
                >
                    <loading-button :loading="sending" type="submit"
                        >Create User</loading-button
                    >
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout';
import FileInput from '@/Shared/FileInput';
import TextInput from '@/Shared/TextInput';
import { Link, useForm } from '@inertiajs/vue3';
import LabelInput from '@/Shared/LabelInput';
import SelectInput from '@/Shared/SelectInput';
import LoadingButton from '@/Shared/LoadingButton';

export default {
    layout: Layout,

    components: {
        Link,
        TextInput,
        FileInput,
        LabelInput,
        SelectInput,
        LoadingButton,
    },

    props: {
        errors: Object,
        roles: Array,
        domain: String,
        mfbs: Array,
    },

    remember: 'form',

    setup() {
        const form = useForm({
            first_name: null,
            last_name: null,
            email: null,
            role: null,
            mfb: null,
        });
        return { form };
    },

    data() {
        return {
            sending: false,
            mfb_role_id: null,
        };
    },

    mounted() {
        this.mfb_role_id = this.roles.find((role) => role.name === 'MFB').id;
    },
};
</script>
