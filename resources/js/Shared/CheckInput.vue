<template>
    <div class="flex items-center">
        <span
            :id="id"
            role="checkbox"
            tabindex="0"
            aria-checked="false"
            ref="input"
            :class="checked ? 'bg-indigo-600' : 'bg-gray-200'"
            @click="checked = !checked"
            class="focus:shadow-outline relative inline-block h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
        >
            <span
                aria-hidden="true"
                :class="checked ? 'translate-x-5' : 'translate-x-0'"
                class="inline-block h-5 w-5 translate-x-0 rounded-full bg-white shadow transition duration-200 ease-in-out"
            ></span>
        </span>

        <label
            v-if="label"
            :for="id"
            class="ml-4 block text-gray-800 select-none"
        >
            {{ label }}
        </label>
    </div>
</template>

<script>
let counter = 0;

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
        };
    },

    watch: {
        checked(checked) {
            this.$emit('update:modelValue', checked);
        },
    },
};
</script>
