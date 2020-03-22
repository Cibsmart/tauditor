<template>
  <div class="flex items-center">
    <div class="flex w-full bg-white rounded">
      <input type="text" name="search" placeholder="Search..." :value="value" autocomplete="off" @input="$emit('input', $event.target.value)" class="relative w-full px-6 py-3 appearance-none border rounded outline-none focus:border-indigo-600">
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: String,
  },
}
</script>
