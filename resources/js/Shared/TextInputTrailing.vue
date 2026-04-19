<template>
    <div>
        <label v-if="label" :for="id" class="mb-2 block select-none text-gray-800">
            {{ label }} <span v-show="required && label" class="text-red-600 ml-1 font-bold">*</span>
        </label>

        <div class="mt-1 relative rounded-md shadow-sm">
            <input :id="id" :type="type" v-bind="$attrs" :value="modelValue" ref="input"
                   :class="{ error: errors.length }" @input="$emit('update:modelValue', $event.target.value)"
                   class="form-input block w-full sm:text-sm sm:leading-5 focus:outline-none focus:border-indigo-500 focus:shadow">

            <div class="absolute inset-y-0 right-0 flex items-center">
                <span class="text-gray-500 pr-3">
                    {{ trailing_character }}
                </span>
            </div>
        </div>

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
        trailing_character: String,
        type: { type: String, default: 'text' },
        errors: { type: Array, default: () => [] },
        id: { type: String, default: () => `text-input-${counter++}` },
        required: { type: Boolean, default: false },
    },

    emits: ['update:modelValue'],
}
</script>
