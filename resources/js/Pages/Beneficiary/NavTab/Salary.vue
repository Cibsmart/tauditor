<template>
  <div class="rounded bg-white">
    <v-tabs>
      <tab title="Salary">
        <salary></salary>
      </tab>
    </v-tabs>
  </div>
</template>

<script>
import Salary from '@/Pages/Beneficiary/Tabs/Salary';
import Tab from '@/Shared/Tab';
import VTabs from '@/Shared/VTabs';

export default {
  props: {
    // data: Object,
  },

  components: {
    Tab,
    VTabs,
    Salary,
  },

  data() {
    return {};
  },

  methods: {},
};
</script>
