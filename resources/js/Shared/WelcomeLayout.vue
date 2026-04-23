<template>
    <div
        class="bg-cool-gray-200 flex min-h-screen flex-col justify-center py-12 sm:px-6 lg:px-8"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-xl">
            <logo
                class="mx-auto block w-full max-w-xs fill-current"
                height="50"
            />
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <h2
                class="block text-center text-2xl leading-9 font-extrabold tracking-tight sm:text-3xl sm:leading-10"
            >
                Welcome to HRMEgde
                <br />
                <span class="text-xl">Select Application Domain</span>
            </h2>

            <slot />
        </div>
    </div>
</template>

<script>
import Logo from '@/Shared/Logo';

export default {
    components: {
        Logo,
    },
};
</script>
