<template>
    <div class="flex items-center">
        <span :id="id" role="checkbox" tabindex="0" aria-checked="false" ref="input"
              :class="checked ? 'bg-indigo-600' : 'bg-gray-200'" @click="checked = ! checked"
              class="relative inline-block flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline">
            <span aria-hidden="true"
                  :class="checked ? 'translate-x-5' : 'translate-x-0'"
                  class="translate-x-0 inline-block h-5 w-5 rounded-full bg-white shadow transition ease-in-out duration-200"></span>
        </span>

        <label v-if="label" :for="id" class="ml-4 block select-none text-gray-800">
            {{ label }}
        </label>
    </div>
</template>

<script>
let counter = 0

export default {
    inheritAttrs: false,
    props: {
        label: String,
        id: { type: String, default: () => `check-input-${counter++}` },
    },

    emits: ['update:modelValue'],

    data() {
        return {
            checked: false,
        }
    },

    watch: {
        checked(checked) {
            this.$emit('update:modelValue', checked)
        },
    },
}
</script>
