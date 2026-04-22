<template>
  <div class="p-6 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <logo class="block mx-auto w-full max-w-xs fill-current" height="50" />
      <slot />
    </div>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo'

export default {
  components: {
    Logo,
  },

  methods: {},
}
</script>
