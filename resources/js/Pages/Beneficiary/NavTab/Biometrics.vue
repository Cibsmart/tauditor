<template>
  <div class="rounded bg-white">
    <v-tabs>
      <tab title="Photo Capture">
        <photo-capture></photo-capture>
      </tab>

      <tab title="Finger Prints">
        <finger-prints></finger-prints>
      </tab>
    </v-tabs>
  </div>
</template>

<script>
import FingerPrints from '@/Pages/Beneficiary/Tabs/FingerPrints';
import PhotoCapture from '@/Pages/Beneficiary/Tabs/PhotoCapture';
import Tab from '@/Shared/Tab';
import VTabs from '@/Shared/VTabs';

export default {
  props: {
    // data: Object,
  },

  components: {
    Tab,
    VTabs,
    PhotoCapture,
    FingerPrints,
  },

  data() {
    return {};
  },

  methods: {},
};
</script>
