<template>
    <div class="bg-white rounded">
        <v-tabs>
            <tab title="Salary">
                <salary></salary>
            </tab>

            <tab title="Deductions">
                <deductions></deductions>
            </tab>
        </v-tabs>
    </div>
</template>

<script>
    import Icon from "@/Shared/Icon";
    import Tab from "@/Shared/Tab";
    import VTabs from "@/Shared/VTabs";
    import Salary from "@/Pages/Beneficiary/Tabs/Salary";
    import Deductions from "@/Pages/Beneficiary/Tabs/Deductions";

    export default {
        props: {
            // data: Object,
        },

        components: {
            Tab,
            Icon,
            VTabs,
            Salary,
            Deductions,
        },

        data() {
            return {

            }
        },

        methods: {

        }
    }
</script>

