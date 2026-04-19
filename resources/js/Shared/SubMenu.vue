<template>
    <div class="hover:bg-gray-100" :class="isActive(uri) ? 'bg-gray-200' : 'bg-white'">
        <Link class="px-12 py-6 flex items-center group" :href="'/' + uri">
            <!--        <icon :name="icon" class="w-4 h-4 ml-4 mr-2 text-indigo-800 group-hover:text-orange-800"/>-->
            <div class="ml-6 font-semibold whitespace-no-wrap text-indigo-800 group-hover:text-orange-800"
                 :title="label" v-text="label">
            </div>
        </Link>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import { Link } from '@inertiajs/vue3'

export default {

    components: {
        Icon,
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
                return this.url === ''
            }

            return urls.filter(url => this.url.startsWith(url)).length
        },
    },
}
</script>
