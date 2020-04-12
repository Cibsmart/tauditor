<template>
  <div>
    <div class="border-l border-gray-100">
      <div class="flex mb-4">
        <div class="w-2/12 mr-10">
          <div class="bg-white w-full pt-12 h-full">
            <ul class="list-reset">
              <li
                class="block p-4 text-grey-darker border-purple hover:bg-gray-200 focus:outline-none mb-4 pl-6 text-sm text-gray-400"
                v-for="tab in tabs"
                :key="tab"
                @click="selected = tab;"
                :class="['tab-btn', { active: selected === tab }]"
              >{{ tab }}</li>
            </ul>
          </div>
        </div>
        <div class="w-9/12">
          <component :is="selected"></component>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Index from "./Tabs/Index";
import EmploymentDetails from "./Tabs/EmploymentDetails";
import NextOfKin from "./Tabs/NextOfKin";
import Qualification from "./Tabs/Qualification";
import Identification from "./Tabs/Identification";
import WorkHistory from "./Tabs/WorkHistory";
import BankAccount from "./Tabs/BankAccount";

export default {
  components: {
    "Basic Information": Index,
    "Employment Details": EmploymentDetails,
    "Next of Kin": NextOfKin,
    Qualification,
    Identification,
    "Work History": WorkHistory,
    "Bank Account": BankAccount
  },
  data() {
    return {
      tabs: [
        "Basic Information",
        "Employment Details",
        "Next of Kin",
        "Qualification",
        "Identification",
        "Work History",
        "Bank Account"
      ],
      selected: "Basic Information"
    };
  }
};
</script>

<style scoped>
.active {
  color: "#374151";
  background: #f4f5f7;
}
</style>