<template>
  <Head title="Login" />
  <form
    class="mt-8 overflow-hidden rounded-lg shadow-lg"
    @submit.prevent="submit"
  >
    <div class="px-10 py-12">
      <h1 class="text-center text-3xl font-bold">Account Login</h1>
      <div class="mx-auto mt-6 w-24 border-b-2" />

      <text-input
        v-model="form.email"
        :errors="form.errors.email"
        class="mt-10"
        label="Email"
        type="email"
      />

      <text-input
        v-model="form.password"
        :errors="form.errors.password"
        class="mt-6"
        label="Password"
        type="password"
      />

      <label class="mt-6 flex items-center select-none" for="remember">
        <input
          id="remember"
          v-model="form.remember"
          class="mr-1"
          type="checkbox"
        />
        <span class="text-sm">Remember Me</span>
      </label>
    </div>

    <div class="flex items-center justify-between border-t px-10 py-4">
      <Link class="hover:underline" href="#">Forgot password</Link>
      <Button>Login</Button>
    </div>
  </form>
</template>

<script>
import { Link, Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import AuthLayout from '@/Shared/AuthLayout';
import TextInput from '@/Shared/TextInput';

export default {
  layout: AuthLayout,

  components: {
    Button,
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
    });

    return { form };
  },

  methods: {
    submit() {
      this.form
        .transform((data) => ({
          ...data,
          remember: data.remember ? 'on' : '',
          domain_id: this.domain.id,
        }))
        .post(this.route('login.attempt'));
    },
  },
};
</script>
