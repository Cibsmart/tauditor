<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('manage_users.index')">Users</inertia-link>
            <span class="text-indigo-400 font-medium">/</span> Create
        </h1>
        <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.first_name" :errors="errors.first_name" class="pr-6 pb-8 w-full " label="First name" required />
                    <text-input v-model="form.last_name" :errors="errors.last_name" class="pr-6 pb-8 w-full " label="Last name" required />
                    <text-input v-model="form.email" :errors="errors.email" class="pr-6 pb-8 w-full " label="Email" required />
                    <select-input v-model="form.role" :errors="errors.role"
                                  class="pr-6 pb-8 w-full " label="Role" required>
                        <option disabled value="" class="text-gray-100">Select Role</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select-input>
                    <select-input v-model="form.mfb" :errors="errors.microfinance_bank"
                                  class="pr-6 pb-8 w-full " label="Microfinance Bank"
                                  v-if="form.role === mfb_role_id">
                        <option disabled value="" class="text-gray-100">Select Microfinance Bank</option>
                        <option v-for="mfb in mfbs" :key="mfb.id" :value="mfb.id">
                            {{ mfb.name }}
                        </option>
                    </select-input>
                    <label-input :value="domain" :error="errors.domain" class="pr-6 pb-8 w-full" label="Domain"  />
                </div>
                <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                    <loading-button :loading="sending" class="btn-indigo" type="submit">Create User</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Layout from '@/Shared/Layout'
    import LoadingButton from '@/Shared/LoadingButton'
    import SelectInput from '@/Shared/SelectInput'
    import TextInput from '@/Shared/TextInput'
    import LabelInput from '@/Shared/LabelInput'
    import FileInput from '@/Shared/FileInput'

    export default {
        metaInfo: { title: 'Create User' },
        layout: Layout,

        components: {
            LoadingButton,
            LabelInput,
            SelectInput,
            TextInput,
            FileInput,
        },

        props: {
            errors: Object,
            roles: Array,
            domain: String,
            mfbs: Array,
        },

        remember: 'form',

        data() {
            return {
                sending: false,
                mfb_role_id: null,
                form: {
                    first_name: null,
                    last_name: null,
                    email: null,
                    role: null,
                    mfb: null,
                },
            }
        },

        mounted() {
            this.mfb_role_id = this.roles.find(role => role.name === 'MFB').id
        },

        methods: {
            submit() {
                this.sending = true

                var data = new FormData()

                data.append('first_name', this.form.first_name || '')
                data.append('last_name', this.form.last_name || '')
                data.append('email', this.form.email || '')
                data.append('role', this.form.role || '')

                if (this.form.role === this.mfb_role_id) {
                    data.append('microfinance_bank', this.form.mfb || '')
                }

                this.$inertia.post(this.route('manage_users.store'), data)
                    .then(() => this.sending = false)
            },
        },
    }
</script>
