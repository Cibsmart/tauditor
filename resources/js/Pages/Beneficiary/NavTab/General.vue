<template>
    <div class="w-full h-screen bg-white rounded">
        <v-tabs>
            <tab title="Basic Information" active>
                <p>Basic Information</p>
            </tab>
            <tab title="Next of Kin">
                <p>Next of Kin</p>
            </tab>
            <tab title="Work Details">
                <p>Work Details</p>
            </tab>
        </v-tabs>
    </div>
</template>

<script>
import Icon from "@/Shared/Icon";
import Tab from "@/Shared/Tab";
import VTabs from "@/Shared/VTabs";

export default {
    props: {

    },

    components: {
        Tab,
        Icon,
        VTabs,
    },

    data() {
        return {

        }
    },

    methods: {

    }
}
</script>

