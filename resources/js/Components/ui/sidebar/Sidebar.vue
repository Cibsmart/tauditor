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
            'fixed inset-y-0 left-0 z-20 flex h-full flex-col bg-sidebar text-sidebar-foreground border-r border-sidebar-border',
            'w-64 transition-transform duration-200 ease-in-out',
            '-translate-x-full data-[state=open]:translate-x-0',
            'md:relative md:z-auto md:translate-x-0',
            props.class
        )"
    >
        <slot />
    </aside>
</template>
