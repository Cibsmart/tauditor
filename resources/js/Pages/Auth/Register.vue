<template>
    <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
      <div class="px-10 py-12">
        <h1 class="text-center font-bold text-3xl">Account Sign Up</h1>
        <div class="mx-auto mt-6 w-24 border-b-2" />
        
        <text-input v-model="form.first_name" label="First Name" type="first_name" class="mt-10"
                    :errors="$page.errors.first_name" />

        <text-input v-model="form.last_name" label="Last Name" type="last_name" class="mt-6"
                    :errors="$page.errors.last_name" />

        <text-input v-model="form.email" label="Email" type="email" class="mt-6"
                    :errors="$page.errors.email" />

        <text-input v-model="form.password" label="Password" type="password"  class="mt-6"
                    :errors="$page.errors.password" />

        <text-input v-model="form.password_confirmation" label="Confirm Password" type="password"             class="mt-6" :errors="$page.errors.password_confirmation" />

        <select-input v-model="form.domain_id" label="Domain" :errors="$page.errors.domain_id" class="mt-6">
          <option :value="null" />
          <option v-for="domain in domains" :key="domain.id" :value="domain.id" v-text="domain.slug" />
        </select-input>
      </div>

      <div class="px-10 py-4 bg-gray-200 border-t border-gray-200 flex justify-between items-center">
        <inertia-link :href="route('login')" class="hover:underline"> Login </inertia-link>
        <button type="submit" 
            class="px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap hover:bg-orange-600 focus:bg-orange-500">
          Sign Up
        </button>
      </div>
    </form>
</template>

<script>
import SelectInput from '@/Shared/SelectInput'
import AuthLayout from '@/Shared/AuthLayout'
import TextInput from '@/Shared/TextInput'
import Logo from '@/Shared/Logo'

export default{
  metaInfo: { title: 'Register' },
  layout: AuthLayout,

  components: {
    SelectInput,
    TextInput,
    Logo,
  },
  
  props: {
    errors: Object,
    domains: Array,
  },

  data() {
    return {
      form: {
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        domain_id: ''
      },
    }
  },

  methods: {
    submit() {
      this.$inertia.post(this.route('register.store'), {
        first_name: this.form.first_name,
        last_name: this.form.last_name,
        email: this.form.email,
        password: this.form.password,
        password_confirmation: this.form.password_confirmation,
        domain_id: this.form.domain_id,
      })
    }
  },
}
</script>