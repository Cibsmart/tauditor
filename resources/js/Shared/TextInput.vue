<template>
  <div>
    <label v-if="label" :for="id" class="mb-2 block select-none ">
      {{ label }} <span v-show="required && label" class="text-red-600 ml-1 font-bold">*</span>
    </label>

    <input :id="id" ref="input" :class="{ error: errors.length }" :type="type" :value="modelValue"
           class="form-input block w-full rounded sm:text-sm sm:leading-5 focus:outline-none  focus:shadow" v-bind="$attrs"
           @input="$emit('update:modelValue', $event.target.value)">

    <div v-if="errors.length" class="text-red-800 mt-2 text-sm">
      {{ errors[0] }}
    </div>
  </div>
</template>

<script>
let counter = 0

export default {
  inheritAttrs: false,
  props: {
    modelValue: String,
    label: String,
    type: { type: String, default: 'text' },
    errors: { type: Array, default: () => [] },
    id: { type: String, default: () => `text-input-${counter++}` },
    required: { type: Boolean, default: false },
  },

  emits: ['update:modelValue'],
}
</script>
