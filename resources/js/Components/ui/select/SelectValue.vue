<script setup>
import { SelectValue } from 'reka-ui'

defineProps({
    placeholder: { type: String, default: undefined },
})
</script>

<template>
    <SelectValue v-bind="$attrs">
        <slot />
    </SelectValue>
</template>
