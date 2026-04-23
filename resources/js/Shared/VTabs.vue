<template>
  <div>
    <div class="sm:hidden">
      <select-input @update:modelValue="selectedTab">
        <option
          v-for="(tab, index) in tabs"
          :key="index"
          :value="index"
          class="focus:shadow-outline-blue mt-1 block w-full form-select border-gray-300 py-2 pr-10 pl-3 text-base leading-6 transition duration-150 ease-in-out focus:border-blue-300 focus:outline-none sm:text-sm sm:leading-5"
        >
          {{ tab.title }}
        </option>
      </select-input>
    </div>
    <div class="flex">
      <div class="hidden sm:block">
        <div class="border-gray-200">
          <nav
            class="mt-4 ml-4 flex w-64 flex-col rounded border border-gray-200"
          >
            <Link
              v-for="(tab, index) in tabs"
              role="tab"
              href="#"
              @click="activeTab = tab"
              :key="index"
              :class="
                tab.isActive
                  ? 'border-r bg-gray-100 text-indigo-800 focus:border-indigo-800 focus:text-indigo-900'
                  : 'border-transparent text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:border-gray-300 focus:text-gray-700'
              "
              class="whitespace-no-wrap group flex items-center border-gray-200 px-4 py-4 text-sm leading-5 font-extrabold focus:outline-none"
              preserve-state
              preserve-scroll
            >
              <span class="truncate">
                {{ tab.title }}
              </span>
              <span
                v-show="tab.completed"
                class="ml-auto inline-block transition duration-150 ease-in-out"
              >
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
import { Link } from '@inertiajs/vue3';
import Icon from '@/Shared/Icon';
import SelectInput from '@/Shared/SelectInput';

export default {
  components: {
    Link,
    Icon,
    SelectInput,
  },

  provide() {
    return {
      registerTab: this.registerTab,
    };
  },

  data() {
    return {
      tabs: [],
      activeTab: null,
    };
  },

  watch: {
    activeTab(activeTab) {
      this.tabs.forEach((tab) => {
        tab.isActive = tab === activeTab;
      });
    },
  },

  methods: {
    registerTab(tab) {
      this.tabs.push(tab);

      if (this.tabs.length === 1 || tab.active) {
        this.activeTab = tab;
      }
    },

    selectedTab(value) {
      this.activeTab = this.tabs[value];
    },
  },
};
</script>
