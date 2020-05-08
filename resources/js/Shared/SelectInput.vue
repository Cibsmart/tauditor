<template>
    <div>
      <label v-if="label" :for="id" class="mb-2 block select-none text-gray-800">
        {{ label }}:
      </label>

      <select v-model="selected" v-bind="$attrs" :id="id" ref="input"
              :class="{ error: errors.length }"
              class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:border-indigo-500 focus:shadow sm:text-sm sm:leading-5 transition ease-in-out duration-150">
        <slot />
      </select>

      <div v-if="errors.length" class="text-red-800 mt-2 text-sm">
        {{ errors[0] }}
      </div>
    </div>
</template>

<script>

export default{

  inheritAttrs: false,

  components: {},

  props: {
    value: [String, Number, Boolean],
    label: String,
    errors: { type: Array, default: () => []},
    id: {
      type: String,
      default() {
        return `select-input-${this._uid}`
      }
    },
  },

  data() {
    return {
      selected: this.value,
    }
  },

  watch: {
    selected(selected) {
      this.$emit('input', selected)
    }
  },
}

</script>
