<script setup>
import { DropdownMenuSeparator } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps({ class: { type: String, default: '' } })
</script>

<template>
    <DropdownMenuSeparator
        v-bind="$attrs"
        :class="cn('-mx-1 my-1 h-px bg-muted', props.class)"
    />
</template>
