<template>
  <div class="flex min-h-screen items-center justify-center p-6">
    <div class="w-full max-w-md">
      <logo class="mx-auto block w-full max-w-xs fill-current" height="50" />
      <slot />
    </div>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo';

export default {
  components: {
    Logo,
  },

  methods: {},
};
</script>
