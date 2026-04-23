<template>
  <div>
    <label v-if="label" :for="id" class="mb-2 block select-none">
      {{ label
      }}<span v-show="required && label" class="ml-1 font-bold text-red-600"
        >*</span
      >
    </label>

    <select
      :id="id"
      ref="input"
      v-model="selected"
      :class="{ 'rounded border border-red-500 pt-px': errors.length }"
      class="mt-1 block w-full form-select py-2 pr-10 pl-3 text-base leading-6 transition duration-150 ease-in-out focus:shadow focus:outline-none sm:text-sm sm:leading-5"
      v-bind="$attrs"
    >
      <slot />
    </select>

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
    modelValue: [String, Number, Boolean],
    label: String,
    errors: { type: Array, default: () => [] },
    id: { type: String, default: () => `select-input-${counter++}` },
    required: { type: Boolean, default: false },
  },

  emits: ['update:modelValue'],

  data() {
    return {
      selected: this.modelValue,
    };
  },

  watch: {
    selected(selected) {
      this.$emit('update:modelValue', selected);
    },
    modelValue(value) {
      this.selected = value;
    },
  },
};
</script>
