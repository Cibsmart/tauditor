<template>
    <div class="rounded bg-white">
        <v-tabs>
            <tab title="Photo Capture">
                <photo-capture></photo-capture>
            </tab>

            <tab title="Finger Prints">
                <finger-prints></finger-prints>
            </tab>
        </v-tabs>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon';
import Tab from '@/Shared/Tab';
import VTabs from '@/Shared/VTabs';
import PhotoCapture from '@/Pages/Beneficiary/Tabs/PhotoCapture';
import FingerPrints from '@/Pages/Beneficiary/Tabs/FingerPrints';

export default {
    props: {
        // data: Object,
    },

    components: {
        Tab,
        Icon,
        VTabs,
        PhotoCapture,
        FingerPrints,
    },

    data() {
        return {};
    },

    methods: {},
};
</script>
