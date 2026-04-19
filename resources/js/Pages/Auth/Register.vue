<template>
    <Head title="Register" />
    <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
            <h1 class="text-center font-bold text-3xl">Account Registration</h1>
            <div class="mx-auto mt-6 w-24 border-b-2"/>

            <label-input :model-value="user.first_name" label="First Name" class="mt-10"/>

            <label-input :model-value="user.last_name" label="Last Name" class="mt-10"/>

            <label-input :model-value="user.email" label="Email" class="mt-10"/>

            <text-input v-model="form.password" label="Password" type="password" class="mt-6"
                        :errors="form.errors.password"/>

            <text-input v-model="form.password_confirmation" label="Confirm Password" type="password"
                        class="mt-6" :errors="form.errors.password_confirmation"/>

            <label-input :model-value="domain.name" label="Domain" class="mt-10"/>
        </div>

        <div class="px-10 py-4 bg-gray-200 border-t border-gray-200 flex justify-between items-center">
            <Link :href="route('login', {domain: domain.id})" class="hover:underline"> Login</Link>
            <button type="submit"
                    class="px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap hover:bg-orange-600 focus:bg-orange-500">
                Register
            </button>
        </div>
    </form>
</template>

<script>
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import AuthLayout from '@/Shared/AuthLayout'
import LabelInput from '@/Shared/LabelInput'
import SelectInput from '@/Shared/SelectInput'
import { Link, Head, useForm } from '@inertiajs/vue3'

export default {
    layout: AuthLayout,

    components: {
        Logo,
        Link,
        Head,
        TextInput,
        LabelInput,
        SelectInput,
    },

    props: {
        errors: Object,
        domain: Object,
        user: Object,
    },

    setup() {
        const form = useForm({
            password: null,
            password_confirmation: null,
        })
        return { form }
    },

    methods: {
        submit() {
            this.form
                .transform((data) => ({
                    ...data,
                    email: this.user.email,
                    domain_id: this.domain.id,
                    last_name: this.user.last_name,
                    first_name: this.user.first_name,
                }))
                .post(this.route('register.store'))
        },
    },
}
</script>
