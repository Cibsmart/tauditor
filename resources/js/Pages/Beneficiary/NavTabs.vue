<template>
  <div>
    <div class="border-b border-gray-100">
      <ul class="flex items-center h-12 bg-white border-l border-gray-100 pl-6">
        <li
          class="mr-6 text-gray-400 hover:text-blue-800"
          v-for="tab in tabs"
          :key="tab"
          @click="selected = tab;"
          :class="['tab-btn', { active: selected === tab }]"
        >{{ tab }}</li>
      </ul>
    </div>
    <component :is="selected"></component>
    <SalaryStructure/>
  </div>
</template>

<script>
import Tabs from "./Tabs";
import BioData from "./NavTab/Biodata";
import Salary from "./NavTab/Salary";

export default {
  components: {
    "General": Tabs,
    "Bio-Data": BioData,
    "Set Salary Structure": Salary
  },

  data() {
    return {
      tabs: ["General", "Bio-Data", "Set Salary Structure"],
      selected: "General"
    };
  }
};
</script>