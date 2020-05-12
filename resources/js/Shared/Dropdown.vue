<template>
    <div class="relative inline-block text-left" @click.="">
        <div>
            <span class="rounded-md shadow-sm">
              <button type="button" @click="show = ! show"
                      class="inline-flex justify-center w-full px-4 py-2 text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none active:bg-gray-50 active:text-gray-800 transition ease-in-out duration-150">
                    <slot></slot>
              </button>
            </span>
        </div>

        <div v-if="show"  class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg">
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
            }
        },

        mounted() {
            document.addEventListener('keydown', (e) => {
                if (e.code === 'Escape') {
                    this.show = false
                }
            })
        },
    }
</script>
