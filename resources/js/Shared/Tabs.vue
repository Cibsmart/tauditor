<template>
    <div>
        <div class="sm:hidden">
            <select aria-label="Selected tab" class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                <option v-for="(tab, index) in tabs" :key="index">
                    {{ tab.title }}
                </option>
            </select>
        </div>
        <div class="hidden sm:block">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <a v-for="(tab, index) in tabs" role="tab" href="#"
                       @click="activeTab = tab" :key="index"
                       :class="activeTab === tab
                       ? 'border-indigo-700 text-indigo-800 focus:text-indigo-900 focus:border-indigo-800'
                       : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'"
                       class="whitespace-no-wrap ml-8 py-4 px-1 border-b-2 font-medium text-sm leading-5 focus:outline-none">
                        {{ tab.title }}
                    </a>
                </nav>
            </div>
        </div>

        <slot></slot>
    </div>
</template>

<script>

export default {

    props: {

    },

    data() {
        return {
            tabs: [],
            activeTab: null,
        }
    },

    mounted() {
        this.tabs = this.$children

        this.activeTab = this.tabs[0]
    },

    watch: {
        activeTab(activeTab){
            this.tabs.map(tab => tab.show = tab === activeTab)
        }
    },

    methods: {

    }
}
</script>

