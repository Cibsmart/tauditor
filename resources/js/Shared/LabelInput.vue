<template>
    <div>
        <label v-if="label" :for="id" class="mb-2 block select-none text-gray-800">
            {{ label }} <span v-show="required && label" class="text-red-600 ml-1 font-bold">*</span>
        </label>

        <label :id="id"
               class="form-input block w-full sm:text-sm sm:leading-5 focus:outline-none focus:border-indigo-500 focus:shadow">
            {{ modelValue }}
        </label>

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
        errors: { type: Array, default: () => [] },
        id: { type: String, default: () => `label-input-${counter++}` },
        required: { type: Boolean, default: false },
    },
}
</script>
