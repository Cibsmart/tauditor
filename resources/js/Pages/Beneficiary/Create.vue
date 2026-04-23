<template>
  <Head title="Add Beneficiary" />
  <h-tabs>
    <tab active title="General">
      <general :data="general"></general>
    </tab>
    <tab title="Biometrics">
      <biometrics></biometrics>
    </tab>
    <tab title="Basic Pay">
      <salary></salary>
    </tab>
  </h-tabs>
</template>

<script>
import Biometrics from '@/Pages/Beneficiary/NavTab/Biometrics';
import General from '@/Pages/Beneficiary/NavTab/General';
import Salary from '@/Pages/Beneficiary/NavTab/Salary';
import HTabs from '@/Shared/HTabs';
import Layout from '@/Shared/Layout';
import Tab from '@/Shared/Tab';

export default {
  layout: Layout,

  props: {
    beneficiary_view_model: Object,
  },

  components: {
    Tab,
    HTabs,
    Salary,
    General,
    Biometrics,
  },

  data() {
    return {
      beneficiary: this.beneficiary_view_model.beneficiary,
      general: {
        basic: this.beneficiary_view_model,
      },
    };
  },
};
</script>
