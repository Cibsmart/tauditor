<template>
    <div>
        <label v-if="label" :for="id" class="mb-2 block select-none text-gray-800">
            {{ label }}<span v-show="required && label" class="text-red-600 ml-1 font-bold">*</span>
        </label>

        <select v-model="selected" v-bind="$attrs" :id="id" ref="input"
                :class="{ 'pt-px rounded border border-red-500' : errors.length }"
                class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:border-indigo-500 focus:shadow sm:text-sm sm:leading-5 transition ease-in-out duration-150">
            <slot/>
        </select>

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
        }
    },

    watch: {
        selected(selected) {
            this.$emit('update:modelValue', selected)
        },
        modelValue(value) {
            this.selected = value
        },
    },
}
</script>
