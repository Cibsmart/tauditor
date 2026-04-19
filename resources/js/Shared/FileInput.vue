<template>
    <div>
        <label v-if="label" class="mb-2 block select-none text-gray-800">{{ label }}:</label>
        <div class="p-0 leading-normal block w-full border text-gray-800 bg-white rounded text-left appearance-none relative focus:outline-none focus:border-indigo-500 focus:shadow" :class="{ error: errors.length && modelValue }">
            <input ref="file" type="file" :accept="accept" class="hidden" @change="change">
            <div v-if="!modelValue" class="p-2">
                <button type="button" class="px-4 py-1 bg-gray-600 hover:bg-gray-700 rounded-sm text-xs font-medium text-white focus:outline-none" @click="browse">
                    Browse
                </button>
            </div>
            <div v-else class="flex items-center justify-between p-2">
                <div class="flex-1 pr-1">
                    {{ modelValue.name }} <span class="text-gray-600 text-xs">
            ({{ filesize(modelValue.size) }})
          </span>
                </div>
                <button type="=button" @click="remove" class="px-4 py-1 bg-gray-600 rounded-sm text-xs font-medium text-white hover:bg-gray-700 focus:outline-none">
                    Remove
                </button>
            </div>
        </div>
        <div v-if="errors.length && modelValue" class="text-red-800 mt-2 text-sm">{{ errors[0]}} </div>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: File,
        label: String,
        accept: String,
        errors: {
            type: Array,
            default: () => [],
        },
    },

    emits: ['update:modelValue'],

    watch: {
        modelValue(value) {
            if (!value) {
                this.$refs.file.value = ''
            }
        },
    },

    methods: {
        filesize(size) {
            var i = Math.floor(Math.log(size) / Math.log(1024))
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
        },
        browse() {
            this.$refs.file.click()
        },
        change(e) {
            this.$emit('update:modelValue', e.target.files[0])
        },
        remove() {
            this.$emit('update:modelValue', null)
        },
    },
}
</script>
