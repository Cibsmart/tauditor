<script setup>
import { DropdownMenuTrigger } from 'reka-ui'
</script>

<template>
    <DropdownMenuTrigger v-bind="$attrs">
        <slot />
    </DropdownMenuTrigger>
</template>
