<template>
    <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
            <h1 class="text-center font-bold text-3xl">Account Registration</h1>
            <div class="mx-auto mt-6 w-24 border-b-2"/>

            <label-input :value="user.first_name" label="First Name" class="mt-10"
                        :errors="$page.errors.first_name" />

            <label-input :value="user.last_name" label="Last Name" class="mt-10"
                         :errors="$page.errors.last_name" />

            <label-input :value="user.email" label="Email" class="mt-10"
                         :errors="$page.errors.email" />

            <text-input v-model="form.password" label="Password" type="password" class="mt-6"
                        :errors="$page.errors.password"/>

            <text-input v-model="form.password_confirmation" label="Confirm Password" type="password"
                        class="mt-6" :errors="$page.errors.password_confirmation"/>

            <label-input :value="domain.name" label="Domain" class="mt-10"
                         :errors="$page.errors.domain" />
        </div>

        <div class="px-10 py-4 bg-gray-200 border-t border-gray-200 flex justify-between items-center">
            <inertia-link :href="route('login', {domain: domain.id})" class="hover:underline"> Login</inertia-link>
            <button type="submit"
                    class="px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap hover:bg-orange-600 focus:bg-orange-500">
                Register
            </button>
        </div>
    </form>
</template>

<script>
    import SelectInput from '@/Shared/SelectInput'
    import AuthLayout from '@/Shared/AuthLayout'
    import TextInput from '@/Shared/TextInput'
    import LabelInput from '@/Shared/LabelInput'
    import Logo from '@/Shared/Logo'

    export default {
        metaInfo: {title: 'Register'},
        layout: AuthLayout,

        components: {
            SelectInput,
            LabelInput,
            TextInput,
            Logo,
        },

        props: {
            errors: Object,
            domain: Object,
            user: Object,
        },

        data() {
            return {
                form: {
                    password: null,
                    password_confirmation: null,
                },
            }
        },

        methods: {
            submit() {
                this.$inertia.post(this.route('register.store'), {
                    first_name: this.user.first_name,
                    last_name: this.user.last_name,
                    email: this.user.email,
                    password: this.form.password,
                    password_confirmation: this.form.password_confirmation,
                    domain_id: this.domain.id,
                })
            }
        },
    }
</script>
