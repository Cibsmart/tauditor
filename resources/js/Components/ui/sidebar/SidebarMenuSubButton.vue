<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
    isActive: { type: Boolean, default: false },
    as: { type: [String, Object], default: 'button' },
    class: { type: String, default: '' },
})
</script>

<template>
    <component
        :is="as"
        v-bind="$attrs"
        :class="cn(
            'flex w-full items-center gap-2 rounded-md px-3 py-1.5 text-sm text-sidebar-foreground/80',
            'hover:bg-sidebar-accent hover:text-sidebar-accent-foreground',
            'transition-colors duration-150 cursor-pointer outline-none',
            props.isActive && 'bg-sidebar-accent text-sidebar-accent-foreground font-medium',
            props.class
        )"
    >
        <slot />
    </component>
</template>
