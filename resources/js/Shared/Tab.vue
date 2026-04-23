<template>
  <div role="tabpanel" v-show="isActive">
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    active: {
      type: Boolean,
      default: false,
    },
    completed: {
      type: Boolean,
      default: false,
    },
  },

  inject: ['registerTab'],

  data() {
    return {
      isActive: this.active,
    };
  },

  mounted() {
    this.registerTab(this);
  },
};
</script>
