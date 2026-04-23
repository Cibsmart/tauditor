<template>
    <div>
        <label
            v-if="label"
            :for="id"
            class="mb-2 block text-gray-800 select-none"
        >
            {{ label }}
            <span v-show="required && label" class="ml-1 font-bold text-red-600"
                >*</span
            >
        </label>

        <div class="relative mt-1 rounded-md shadow-sm">
            <input
                :id="id"
                :type="type"
                v-bind="$attrs"
                :value="modelValue"
                ref="input"
                :class="{ error: errors.length }"
                @input="$emit('update:modelValue', $event.target.value)"
                class="form-input block w-full focus:border-indigo-500 focus:shadow focus:outline-none sm:text-sm sm:leading-5"
            />

            <div class="absolute inset-y-0 right-0 flex items-center">
                <span class="pr-3 text-gray-500">
                    {{ trailing_character }}
                </span>
            </div>
        </div>

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
        trailing_character: String,
        type: { type: String, default: 'text' },
        errors: { type: Array, default: () => [] },
        id: { type: String, default: () => `text-input-${counter++}` },
        required: { type: Boolean, default: false },
    },

    emits: ['update:modelValue'],
};
</script>
