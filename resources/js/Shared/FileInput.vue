<template>
    <div>
        <label v-if="label" class="mb-2 block select-none">{{ label }}:</label>
        <div
            :class="{ error: errors.length && modelValue }"
            class="relative block w-full appearance-none rounded border p-0 text-left leading-normal focus:shadow focus:outline-none"
        >
            <input
                ref="file"
                :accept="accept"
                class="hidden"
                type="file"
                @change="change"
            />
            <div v-if="!modelValue" class="p-2">
                <Button
                    class="rounded-sm px-2 py-1 text-xs font-medium focus:outline-none"
                    size="sm"
                    type="button"
                    @click="browse"
                >
                    Browse
                </Button>
            </div>
            <div v-else class="flex items-center justify-between p-2">
                <div class="flex-1 pr-1">
                    {{ modelValue.name }}
                    <span class="text-xs">
                        ({{ filesize(modelValue.size) }})
                    </span>
                </div>
                <Button
                    class="rounded-sm px-2 py-1 text-xs font-medium focus:outline-none"
                    size="sm"
                    type="button"
                    variant="destructive"
                    @click="remove"
                >
                    Remove
                </Button>
            </div>
        </div>
        <div
            v-if="errors.length && modelValue"
            class="mt-2 text-sm text-red-800"
        >
            {{ errors[0] }}
        </div>
    </div>
</template>

<script>
import { Button } from '@/Components/ui/button';

export default {
    components: { Button },
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
                this.$refs.file.value = '';
            }
        },
    },

    methods: {
        filesize(size) {
            var i = Math.floor(Math.log(size) / Math.log(1024));

            return (
                (size / Math.pow(1024, i)).toFixed(2) * 1 +
                ' ' +
                ['B', 'kB', 'MB', 'GB', 'TB'][i]
            );
        },
        browse() {
            this.$refs.file.click();
        },
        change(e) {
            this.$emit('update:modelValue', e.target.files[0]);
        },
        remove() {
            this.$emit('update:modelValue', null);
        },
    },
};
</script>
