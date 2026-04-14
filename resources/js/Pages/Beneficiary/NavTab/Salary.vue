<template>
    <div class="bg-white rounded">
        <v-tabs>
            <tab title="Salary">
                <salary></salary>
            </tab>

        </v-tabs>
    </div>
</template>

<script>
    import Icon from "@/Shared/Icon";
    import Tab from "@/Shared/Tab";
    import VTabs from "@/Shared/VTabs";
    import Salary from "@/Pages/Beneficiary/Tabs/Salary";

    export default {
        props: {
            // data: Object,
        },

        components: {
            Tab,
            Icon,
            VTabs,
            Salary,
        },

        data() {
            return {

            }
        },

        methods: {

        }
    }
</script>

