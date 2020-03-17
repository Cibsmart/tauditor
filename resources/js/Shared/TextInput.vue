<template>
    <div>
      <label v-if="label" :for="id" class="mb-2 block select-none text-gray-800">
        {{ label }}:
      </label>
      
      <input :id="id" :type="type" v-bind="$attrs" :value="value" ref="input"
            :class="{ error: errors.length }" @input="$emit('input', $event.target.value)"
            class="p-2 leading-normal block w-full border text-gray-800 bg-white rounded text-left appearance-none relative focus:outline-none focus:border-indigo-500 focus:shadow">
      
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
    }
  },

  methods: {

  },
}
</script>