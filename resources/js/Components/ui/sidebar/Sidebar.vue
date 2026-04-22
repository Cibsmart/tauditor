<script setup>
import { inject } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps({ class: { type: String, default: '' } })
const { open } = inject('sidebar')
</script>

<template>
    <aside
        :data-state="open ? 'open' : 'closed'"
        :class="cn(
            'flex flex-col bg-sidebar text-sidebar-foreground border-r border-sidebar-border',
            // Mobile: fixed overlay sliding in/out via transform
            'fixed inset-y-0 left-0 z-20 w-64',
            '-translate-x-full data-[state=open]:translate-x-0 transition-transform duration-200 ease-in-out',
            // Desktop: sticky full-height sidebar, collapse via width
            'md:relative md:z-auto md:sticky md:top-0 md:h-svh',
            'md:translate-x-0 md:transition-[width,border] md:duration-200 md:ease-in-out',
            'md:w-64 md:data-[state=closed]:w-0 md:data-[state=closed]:overflow-hidden md:data-[state=closed]:border-r-0',
            props.class
        )"
    >
        <slot />
    </aside>
</template>
