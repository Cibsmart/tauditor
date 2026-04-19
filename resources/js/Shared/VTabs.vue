<template>
    <div>
        <div class="sm:hidden">
            <select-input @update:modelValue="selectedTab">
                <option v-for="(tab, index) in tabs" :key="index" :value="index"
                        class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                    {{ tab.title }}
                </option>
            </select-input>
        </div>
        <div class="flex">
            <div class="hidden sm:block">
                <div class="border-gray-200">
                    <nav class="mt-4 ml-4 w-64 flex flex-col border border-gray-200 rounded">
                        <Link
                            v-for="(tab, index) in tabs" role="tab" href="#"
                            @click="activeTab = tab" :key="index"
                            :class="tab.isActive
                           ? 'text-indigo-800 border-r bg-gray-100 focus:text-indigo-900 focus:border-indigo-800'
                           : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:text-gray-700 focus:border-gray-300'"
                            class="whitespace-no-wrap group flex items-center py-4 px-4 border-gray-200 font-extrabold text-sm leading-5 focus:outline-none"
                            preserve-state preserve-scroll>
                            <span class="truncate">
                                {{ tab.title }}
                            </span>
                            <span v-show="tab.completed"
                                  class="ml-auto inline-block transition ease-in-out duration-150">
                              <icon name="check" class="text-green-600"></icon>
                            </span>
                        </Link>
                    </nav>
                </div>
            </div>

            <div class="mx-4 my-4 flex-1">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3'
    import Icon from '@/Shared/Icon'
    import SelectInput from '@/Shared/SelectInput'

    export default {
        components: {
            Link,
            Icon,
            SelectInput,
        },

        provide() {
            return {
                registerTab: this.registerTab,
            }
        },

        data() {
            return {
                tabs: [],
                activeTab: null,
            }
        },

        watch: {
            activeTab(activeTab) {
                this.tabs.forEach(tab => {
                    tab.isActive = tab === activeTab
                })
            },
        },

        methods: {
            registerTab(tab) {
                this.tabs.push(tab)
                if (this.tabs.length === 1 || tab.active) {
                    this.activeTab = tab
                }
            },

            selectedTab(value) {
                this.activeTab = this.tabs[value]
            },
        },
    }
</script>
