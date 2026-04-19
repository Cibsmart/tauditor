<template>
    <Head title="Login" />
    <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
            <h1 class="text-center font-bold text-3xl">Account Login</h1>
            <div class="mx-auto mt-6 w-24 border-b-2"/>

            <text-input v-model="form.email" label="Email" type="email" class="mt-10"
                        :errors="form.errors.email"/>

            <text-input v-model="form.password" label="Password" type="password" class="mt-6"
                        :errors="form.errors.password"/>

            <label class="mt-6 select-none flex items-center" for="remember">
                <input v-model="form.remember" id="remember" type="checkbox" class="mr-1">
                <span class="text-sm">Remember Me</span>
            </label>
        </div>

        <div class="px-10 py-4 bg-gray-200 border-t border-gray-200 flex justify-between items-center">
            <Link href="#" class="hover:underline">Forgot password</Link>
            <button type="submit"
                    class="px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap hover:bg-orange-600 focus:bg-orange-500">
                Login
            </button>
        </div>
    </form>
</template>

<script>
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import AuthLayout from '@/Shared/AuthLayout'
import { Link, Head, useForm } from '@inertiajs/vue3'

export default {
    layout: AuthLayout,

    components: {
        Logo,
        Link,
        Head,
        TextInput,
    },

    props: {
        errors: Object,
        domain: Object,
    },

    setup() {
        const form = useForm({
            email: '',
            password: '',
            remember: false,
        })
        return { form }
    },

    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    remember: data.remember ? 'on' : '',
                    domain_id: this.domain.id
                }))
                .post(this.route('login.attempt'))
        },
    },
}
</script>
