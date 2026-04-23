<template>
  <div>
    <label v-if="label" :for="id" class="mb-2 select-none">
      {{ label }}:
      <span v-show="required && label" class="ml-1 font-bold text-red-600"
        >*</span
      >
    </label>

    <label
      :id="id"
      class="form-input w-full focus:shadow focus:outline-none sm:text-sm sm:leading-5"
    >
      {{ modelValue }}
      <slot />
    </label>

    <div v-if="errors.length" class="mt-2 text-sm text-red-800">
      {{ errors[0] }}
    </div>
  </div>
</template>

<script>
let counter = 0;

export default {
  inheritAttrs: false,
  props: {
    modelValue: [String, Number],
    label: String,
    errors: { type: Array, default: () => [] },
    id: { type: String, default: () => `label-input-${counter++}` },
    required: { type: Boolean, default: false },
  },
};
</script>
