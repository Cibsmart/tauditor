<script setup>
import { SidebarProvider, SidebarTrigger, SidebarInset } from '@/Components/ui/sidebar'
import AppSidebar from '@/Components/AppSidebar.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
</script>

<template>
    <SidebarProvider>
        <AppSidebar />

        <SidebarInset>
            <!-- Top header bar -->
            <header class="flex h-14 shrink-0 items-center gap-2 border-b border-border px-4">
                <SidebarTrigger />
                <div class="h-4 w-px bg-border mx-1" />
                <div class="flex-1" />
                <ThemeSwitcher />
            </header>

            <!-- Page content -->
            <div class="flex flex-1 flex-col p-6 overflow-y-auto" scroll-region>
                <FlashMessages />
                <slot />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
