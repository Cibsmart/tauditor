<script setup>
import { inject } from 'vue'
import { PanelLeft } from 'lucide-vue-next'

const { toggleSidebar } = inject('sidebar')
</script>

<template>
    <button
        type="button"
        @click="toggleSidebar"
        class="inline-flex items-center justify-center rounded-md p-2 text-muted-foreground
               hover:bg-accent hover:text-accent-foreground focus:outline-none
               focus-visible:ring-2 focus-visible:ring-ring transition-colors"
        aria-label="Toggle sidebar"
    >
        <PanelLeft class="h-5 w-5" />
    </button>
</template>
