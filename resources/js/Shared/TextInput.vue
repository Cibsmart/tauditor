<template>
    <div>
      <label v-if="label" :for="id" class="mb-2 block select-none text-gray-800">
        {{ label }} <span v-show="required && label" class="text-red-600 ml-1 font-bold">*</span>
      </label>

      <input :id="id" :type="type" v-bind="$attrs" :value="value" ref="input"
            :class="{ error: errors.length }" @input="$emit('input', $event.target.value)"
            class="form-input block w-full sm:text-sm sm:leading-5 focus:outline-none focus:border-indigo-500 focus:shadow">

      <div v-if="errors.length" class="text-red-800 mt-2 text-sm">
        {{ errors[0] }}
      </div>
    </div>
</template>

<script>

export default{
  inheritAttrs: false,
  props: {
      value: String,
      label: String,
      type: { type: String, default: 'text' },
      errors: { type: Array, default: () => [] },
      id: { type: String, default() {
          return `text-input-${this._uid}` },
      },
      required: { type: Boolean, default: false},
  },

  methods: {

  },
}
</script>
