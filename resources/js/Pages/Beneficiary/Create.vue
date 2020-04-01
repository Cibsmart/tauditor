<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Beneficiaries</h1>
    <div class="mb-6 flex justify-between items-center">
      <!-- Search Filter goes here -->
      <search-filter v-model="form.search" class="w-full max-w-lg mr-4">
      </search-filter>
      <div></div>
      <inertia-link href="#" class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap ">
        <span class="hidden md:inline">Create Beneficiary</span>
      </inertia-link>
    </div>

   
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

import mapValues from 'lodash/mapValues'
import pickBY from 'lodash/pickBY'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Beneficiaries' },
  layout: Layout,

  props: {
    beneficiaries: Object,
    filters: Object,
  },

  components: {
    Icon,
    Pagination,
    SearchFilter,
  },

  data(){
    return {
      form: {
        search: this.filters.search,
      },
    }
  },

  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBY(this.form)
          this.$inertia.replace(this.route('beneficiaries.index', Object.keys(query).length ? query : { remember: 'forget'}))
      }, 300),
      deep: true,
    },
  },
}
</script>
