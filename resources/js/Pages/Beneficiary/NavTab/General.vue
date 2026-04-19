<template>
    <div class="bg-white rounded">
        <v-tabs>
            <tab title="Basic Information" :completed="basic_information_completed">
                <basic-information :data="data.basic"></basic-information>
            </tab>

            <tab title="Employment Details">
                <employment-details></employment-details>
            </tab>

            <tab title="Bank Details">
                <bank-details></bank-details>
            </tab>

            <tab title="Next of Kin">
                <next-of-kin></next-of-kin>
            </tab>

            <tab title="Qualifications">
                <qualifications></qualifications>
            </tab>
        </v-tabs>
    </div>
</template>

<script>
import Tab from "@/Shared/Tab";
import Icon from "@/Shared/Icon";
import VTabs from "@/Shared/VTabs";
import NextOfKin from "@/Pages/Beneficiary/Tabs/NextOfKin";
import BankDetails from "@/Pages/Beneficiary/Tabs/BankDetails";
import Qualifications from "@/Pages/Beneficiary/Tabs/Qualifications";
import BasicInformation from "@/Pages/Beneficiary/Tabs/BasicInformation";
import EmploymentDetails from "@/Pages/Beneficiary/Tabs/EmploymentDetails";

export default {
    props: {
        data: Object,
    },

    components: {
        Tab,
        Icon,
        VTabs,
        NextOfKin,
        BankDetails,
        Qualifications,
        BasicInformation,
        EmploymentDetails,
    },

    data() {
        return {

        }
    },

    methods: {

    }
}
</script>

