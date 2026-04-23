<template>
  <div class="mt-6 -mb-1 flex flex-wrap">
    <template v-for="(link, key) in links" :key="key">
      <div
        v-if="link.url === null"
        class="mr-1 mb-1 rounded border px-4 py-3 text-sm"
      >
        {{ link.label }}
      </div>
      <Link
        v-else
        class="mr-1 mb-1 rounded border border-gray-400 px-4 py-3 text-sm hover:bg-white focus:border-indigo-600 focus:text-indigo-600"
        :class="{ 'bg-white': link.active }"
        :href="link.url"
        preserve-scroll
        preserve-state
      >
        {{ link.label }}
      </Link>
    </template>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  props: {
    links: Array,
  },
  components: {
    Link,
  },
};
</script>
