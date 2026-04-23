<template>
    <div>
        <label v-if="label" :for="id" class="mb-2 block select-none">
            {{ label }}
            <span v-show="required && label" class="ml-1 font-bold text-red-600"
                >*</span
            >
        </label>

        <input
            :id="id"
            ref="input"
            :class="{ error: errors.length }"
            :type="type"
            :value="modelValue"
            class="form-input block w-full rounded focus:shadow focus:outline-none sm:text-sm sm:leading-5"
            v-bind="$attrs"
            @input="$emit('update:modelValue', $event.target.value)"
        />

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
        modelValue: String,
        label: String,
        type: { type: String, default: 'text' },
        errors: { type: Array, default: () => [] },
        id: { type: String, default: () => `text-input-${counter++}` },
        required: { type: Boolean, default: false },
    },

    emits: ['update:modelValue'],
};
</script>
