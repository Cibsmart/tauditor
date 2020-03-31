<template>
    <div class="mb-2">
      <inertia-link class="py-2 flex items-center group" :href="'/' + uri" >
          <icon :name="icon" class="w-4 h-4 mr-2"
                :class="isActive(uri) ? 'fill-current text-white' : 'fill-current text-indigo-300 hover:fill-current group-hover:text-white'"/>
        <div :class="isActive(uri) ? 'text-white' : 'text-indigo-300 group-hover:text-white' " class="whitespace-no-wrap" :title="label" v-text="label">
        </div>
      </inertia-link>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {

  components: {
    Icon,
  },

  props: {
    uri: String,
    url: String,
    label: String,
    icon : String,
  },

  methods: {
    isActive(...urls) {
      if (urls[0] === '') {
        return this.url === ''
      }

      return urls.filter(url => this.url.startsWith(url)).length
    },
  },
}
</script>
