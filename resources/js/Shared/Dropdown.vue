<template>
  <div class="relative inline-block text-left" @click="show = true">
    <div>
            <span class="rounded-md shadow-sm">
                <button
                  class="inline-flex w-full justify-center px-4 py-2 text-sm leading-5 font-medium text-gray-700 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none active:bg-gray-50 active:text-gray-800"
                  type="button"
                  @click="show = !show"
                >
                    <slot></slot>
                </button>
            </span>
    </div>

    <div
      v-if="show"
      class="absolute right-0 mt-2 w-56 origin-top-right rounded-md shadow-lg"
    >
      <div class="rounded-md bg-white shadow-xs">
        <div class="py-1">
          <slot name="dropdown" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      show: false,
    };
  },

  mounted() {
    document.addEventListener('keydown', (e) => {
      if (e.code === 'Escape') {
        this.show = false;
      }
    });
  },
};
</script>
