<template>
    <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
            <h1 class="text-center font-bold text-3xl">Account Login</h1>
            <div class="mx-auto mt-6 w-24 border-b-2"/>

            <text-input v-model="form.email" label="Email" type="email" class="mt-10"
                        :errors="$page.errors.email"/>

            <text-input v-model="form.password" label="Password" type="password" class="mt-6"
                        :errors="$page.errors.password"/>

            <label class="mt-6 select-none flex items-center" for="remember">
                <input v-model="form.remember" id="remember" type="checkbox" class="mr-1">
                <span class="text-sm">Remember Me</span>
            </label>
        </div>

        <div class="px-10 py-4 bg-gray-200 border-t border-gray-200 flex justify-between items-center">
            <inertia-link href="#" class="hover:underline">Forget password</inertia-link>
            <button type="submit"
                    class="px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap hover:bg-orange-600 focus:bg-orange-500">
                Login
            </button>
        </div>
    </form>
</template>

<script>
    import AuthLayout from '@/Shared/AuthLayout'
    import TextInput from '@/Shared/TextInput'
    import Logo from '@/Shared/Logo'

    export default {
        metaInfo: {title: 'Login'},
        layout: AuthLayout,

        components: {
            TextInput,
            Logo,
        },

        props: {
            errors: Object,
            domain: Object,
        },

        data() {
            return {
                form: {
                    email: '',
                    password: '',
                    remember: null,
                },
            }
        },

        methods: {
            submit() {
                this.$inertia.post(this.route('login.attempt'), {
                    email: this.form.email,
                    password: this.form.password,
                    domain_id: this.domain.id,
                    remember: this.form.remember,
                })
            }
        },
    }
</script>
