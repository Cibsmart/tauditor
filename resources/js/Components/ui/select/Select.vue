<script setup>
import { SelectRoot } from 'reka-ui'

defineOptions({ inheritAttrs: false })
</script>

<template>
    <SelectRoot v-bind="$attrs">
        <slot />
    </SelectRoot>
</template>
