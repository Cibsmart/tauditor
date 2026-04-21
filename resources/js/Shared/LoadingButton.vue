<template>
    <Button v-bind="$attrs" :disabled="loading">
        <div v-if="loading" class="btn-spinner mr-2" />
        <slot />
    </Button>
</template>

<script setup>
import { Button } from '@/Components/ui/button'

defineProps({ loading: Boolean })
</script>
