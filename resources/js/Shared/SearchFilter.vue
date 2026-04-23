<template>
  <div class="flex items-center">
    <div class="flex w-full rounded bg-white">
      <dropdown
        :auto-close="false"
        class="rounded-l border-r px-4 hover:bg-gray-100 focus:z-10 focus:border-white focus:ring md:px-6"
        placement="bottom-start"
      >
        <div class="flex items-baseline">
          <span class="hidden text-gray-700 md:inline">Filter</span>
          <svg
            class="h-2 w-2 fill-gray-700 md:ml-2"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 961.243 599.998"
          >
            <path
              d="M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z"
            />
          </svg>
        </div>
        <template v-slot:dropdown>
          <div
            class="mt-2 w-screen rounded bg-white px-4 py-6 shadow-xl"
            :style="{ maxWidth: `${maxWidth}px` }"
          >
            <slot />
          </div>
        </template>
      </dropdown>
      <input
        type="text"
        name="search"
        placeholder="Search..."
        :value="modelValue"
        autocomplete="off"
        @input="$emit('update:modelValue', $event.target.value)"
        class="relative w-full appearance-none rounded border px-6 py-3 outline-none focus:border-indigo-600"
      />
    </div>
    <button
      class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500"
      type="button"
      @click="$emit('reset')"
    >
      Reset
    </button>
  </div>
</template>

<script>
import Dropdown from '@/Shared/NewDropdown';

export default {
  components: { Dropdown },
  props: {
    modelValue: String,
    maxWidth: {
      type: Number,
      default: 300,
    },
  },

  emits: ['update:modelValue', 'reset'],
};
</script>
