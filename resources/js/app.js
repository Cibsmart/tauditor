import Vue from 'vue'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import { InertiaApp } from '@inertiajs/inertia-vue'

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.mixin({ methods: { route: window.route } }) //Make route helper available on client side
Vue.config.productionTip = false //Turns off warning during development
Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.use(VueMeta)

const app = document.getElementById('app')

new Vue({
  metaInfo:{
    titleTemplate: (title) => title ? `${title} - HRMEdge` : 'HRMEdge'
  },

  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    },
  }),
}).$mount(app)
