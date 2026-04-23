<template>
  <div
    :class="isActive(uri) ? 'bg-gray-200' : 'bg-white'"
    class="hover:bg-gray-100"
  >
    <Link :href="'/' + uri" class="group flex items-center px-12 py-6">
      <!--        <icon :name="icon" class="w-4 h-4 ml-4 mr-2 text-indigo-800 group-hover:text-orange-800"/>-->
      <div
        :title="label"
        class="whitespace-no-wrap ml-6 font-semibold text-indigo-800 group-hover:text-orange-800"
        v-text="label"
      ></div>
    </Link>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    Link,
  },

  props: {
    uri: String,
    url: String,
    label: String,
    icon: String,
  },

  mounted() {
    // console.log(this.isActive(this.uri))
  },

  methods: {
    isActive(...urls) {
      if (urls[0] === '') {
        return this.url === '';
      }

      return urls.filter((url) => this.url.startsWith(url)).length;
    },
  },
};
</script>
