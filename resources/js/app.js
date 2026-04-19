import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createHead } from '@vueuse/head'
import { ZiggyVue } from 'ziggy-js'

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });

createInertiaApp({
    title: (title) => title ? `${title} - HRMEdge` : 'HRMEdge',
    resolve: (name) => pages[`./Pages/${name}.vue`],
    setup({ el, App, props, plugin }) {
        const head = createHead()
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(head)
            .use(ZiggyVue)
            .mount(el)
    },
})
