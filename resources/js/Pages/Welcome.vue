<template>
    <div class="mt-8 flex justify-around">
        <div class="inline-flex rounded-md shadow">
            <inertia-link :href="route('login', {domain: 'state'})" class="inline-flex items-center justify-center px-10 py-3 border border-white text-base leading-6 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                State
            </inertia-link>
        </div>
        <div class="ml-3 inline-flex rounded-md shadow">
            <inertia-link :href="route('login', {domain: 'jaac'})" class="inline-flex items-center justify-center px-10 py-3 border border-indigo-500 text-base leading-6 font-medium rounded-md text-indigo-600 bg-white hover:text-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                JAAC
            </inertia-link>
        </div>
    </div>
</template>

<script>
    import WelcomeLayout from '@/Shared/WelcomeLayout'


    export default {
        metaInfo: { title: 'Welcome' },
        layout: WelcomeLayout,
    }
</script>
