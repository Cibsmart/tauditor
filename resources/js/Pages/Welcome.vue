<template>
    <Head title="Welcome" />
    <div class="mt-8 flex justify-around">
        <div class="inline-flex rounded-md shadow">
            <Link :href="route('login', {domain: 'state'})"
                  class="inline-flex items-center justify-center px-10 py-3 border border-white text-base leading-6 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                State
            </Link>
        </div>
        <div class="ml-3 inline-flex rounded-md shadow">
            <Link :href="route('login', {domain: 'jaac'})"
                  class="inline-flex items-center justify-center px-10 py-3 border border-indigo-500 text-base leading-6 font-medium rounded-md text-indigo-600 bg-white hover:text-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                JAAC
            </Link>
        </div>
    </div>
</template>

<script>
import WelcomeLayout from '@/Shared/WelcomeLayout'
import {Link} from '@inertiajs/vue3'


export default {
    layout: WelcomeLayout,

    components: {
       Link,
    },
}
</script>
