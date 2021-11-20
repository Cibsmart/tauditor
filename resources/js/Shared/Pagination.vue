<template>
    <div class="mt-6 -mb-1 flex flex-wrap">
        <template v-for="(link, key) in links">
            <div v-if="link.url === null" :key="key" class="mr-1 mb-1 px-4 py-3 text-sm border rounded">
                {{ link.label }}
            </div>
            <Link v-else :key="key"
                          class="mr-1 mb-1 px-4 py-3 text-sm border border-gray-400 rounded hover:bg-white focus:border-indigo-600 focus:text-indigo-600"
                          :class="{ 'bg-white': link.active }" :href="link.url" preserve-scroll preserve-state
            >
                {{ link.label }}
            </Link>
        </template>
    </div>
</template>

<script>
import {Link} from '@inertiajs/inertia-vue'

export default {
    props: {
        links: Array,
    },
    components: {
        Link
    }
}
</script>
