<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
    class: { type: String, default: '' },
})
</script>

<template>
    <thead :class="cn('[&_tr]:border-b', props.class)" v-bind="$attrs">
        <slot />
    </thead>
</template>
