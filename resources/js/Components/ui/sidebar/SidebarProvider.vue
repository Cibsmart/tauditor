<script setup>
import { ref, provide } from 'vue'

const props = defineProps({ defaultOpen: { type: Boolean, default: true } })

const open = ref(props.defaultOpen)
const toggleSidebar = () => (open.value = !open.value)

provide('sidebar', { open, toggleSidebar })
</script>

<template>
    <div class="flex min-h-svh w-full">
        <slot />
        <Transition
            enter-from-class="opacity-0"
            enter-active-class="transition-opacity duration-200"
            leave-to-class="opacity-0"
            leave-active-class="transition-opacity duration-200"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-10 bg-black/50 md:hidden"
                @click="toggleSidebar"
            />
        </Transition>
    </div>
</template>
