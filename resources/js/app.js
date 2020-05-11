import Vue from 'vue'
import Vuex from 'vuex'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import {InertiaApp} from '@inertiajs/inertia-vue'

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.mixin({methods: {route: window.route}}) //Make route helper available on client side
Vue.config.productionTip = false //Turns off warning during development
Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.use(VueMeta)
Vue.use(Vuex)

const app = document.getElementById('app')

const store = new Vuex.Store({
    state: {
        basic_information_completed: false,
    },
    mutations: {
        setBasicInformationCompleted(state){
            state.basic_information_completed = true;
        }
    }
})

new Vue({
    store: store,

    metaInfo: {
        titleTemplate: (title) => title ? `${title} - HRMEdge` : 'HRMEdge'
    },

    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app)
