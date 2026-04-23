<template>
    <Head title="Welcome" />
    <div class="mt-8 flex justify-around">
        <div class="inline-flex rounded-md shadow">
            <Button asChild size="lg">
                <Link :href="route('login', { domain: 'state' })"> State </Link>
            </Button>
        </div>
        <div class="ml-3 inline-flex rounded-md shadow">
            <Button asChild size="lg">
                <Link :href="route('login', { domain: 'jaac' })"> JAAC </Link>
            </Button>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import WelcomeLayout from '@/Shared/WelcomeLayout';

export default {
    layout: WelcomeLayout,

    components: {
        Button,
        Link,
    },
};
</script>
