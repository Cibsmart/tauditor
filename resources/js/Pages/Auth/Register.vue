<template>
  <Head title="Register" />
  <form
    class="mt-8 overflow-hidden rounded-lg shadow-lg"
    @submit.prevent="submit"
  >
    <div class="px-10 py-12">
      <h1 class="text-center text-3xl font-bold">Account Registration</h1>
      <div class="mx-auto mt-6 w-24 border-b-2" />

      <label-input
        :model-value="user.first_name"
        class="mt-10"
        label="First Name"
      />

      <label-input
        :model-value="user.last_name"
        class="mt-10"
        label="Last Name"
      />

      <label-input
        :model-value="user.email"
        class="mt-10"
        label="Email"
      />

      <text-input
        v-model="form.password"
        :errors="form.errors.password"
        class="mt-6"
        label="Password"
        type="password"
      />

      <text-input
        v-model="form.password_confirmation"
        :errors="form.errors.password_confirmation"
        class="mt-6"
        label="Confirm Password"
        type="password"
      />

      <label-input
        :model-value="domain.name"
        class="mt-10"
        label="Domain"
      />
    </div>

    <div class="flex items-center justify-between border-t px-10 py-4">
      <Link
        :href="route('login', { domain: domain.id })"
        class="hover:underline"
      >
        Login
      </Link
      >
      <Button>Register</Button>
    </div>
  </form>
</template>

<script>
import { Link, Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import AuthLayout from '@/Shared/AuthLayout';
import LabelInput from '@/Shared/LabelInput';
import TextInput from '@/Shared/TextInput';

export default {
  layout: AuthLayout,

  components: {
    Button,
    Link,
    Head,
    TextInput,
    LabelInput,
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
    });

    return { form };
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
        .post(this.route('register.store'));
    },
  },
};
</script>
