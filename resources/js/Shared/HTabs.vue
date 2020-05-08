<template>
    <div>
        <div class="sm:hidden">
            <select-input @input="selectedTab">
                <option v-for="(tab, index) in tabs" :key="index" :value="index"
                        class="mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5 transition ease-in-out duration-150">
                    {{ tab.title }}
                </option>
            </select-input>
        </div>
        <div class="hidden sm:block">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <inertia-link
                        v-for="(tab, index) in tabs" role="tab" href="#"
                        @click="activeTab = tab" :key="index"
                        :class="tab.isActive
                       ? 'border-indigo-700 text-indigo-800 focus:text-indigo-900 focus:border-indigo-800'
                       : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'"
                        class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 font-extrabold text-sm leading-5 focus:outline-none"
                        preserve-state preserve-scroll>
                        {{ tab.title }}
                    </inertia-link>
                </nav>
            </div>
        </div>

        <slot></slot>
    </div>
</template>

<script>
    import SelectInput from '@/Shared/SelectInput'

    export default {

        props: {

        },

        components: {
            SelectInput,
        },

        data() {
            return {
                tabs: [],
                current: null,
                activeTab: null,
            }
        },

        mounted() {
            this.tabs = this.$children.slice(1);

            this.setInitialActiveTab();
        },

        watch: {
            activeTab(activeTab){
                this.tabs.map(tab => tab.isActive = tab === activeTab);
            },
        },

        methods: {
            setInitialActiveTab(){
                this.activeTab = this.tabs.find(tab => tab.active) || this.tabs[0];
            },

            selectedTab(value){
                this.activeTab = this.tabs[value];
            },
        }
    }
</script>

