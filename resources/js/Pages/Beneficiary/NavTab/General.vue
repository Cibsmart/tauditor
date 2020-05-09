<template>
    <div class="bg-white rounded">
        <v-tabs>
            <tab title="Basic Information" active>
                <basic-information></basic-information>
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
import BasicInformation from "@/Pages/Beneficiary/Tabs/BasicInformation";

export default {
    props: {

    },

    components: {
        Tab,
        Icon,
        VTabs,
        BasicInformation,
    },

    data() {
        return {

        }
    },

    methods: {

    }
}
</script>

