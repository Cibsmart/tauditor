<template>
    <h-tabs>
        <tab title="General" active>
            <general :data="general"></general>
        </tab>
        <tab title="Biometrics">
            <biometrics></biometrics>
        </tab>
        <tab title="Basic Pay">
            <salary></salary>
        </tab>
    </h-tabs>
</template>

<script>
import { mapMutations } from "vuex";
import Tab from "@/Shared/Tab";
import Icon from "@/Shared/Icon";
import HTabs from "@/Shared/HTabs";
import Layout from "@/Shared/Layout";
import Salary from "@/Pages/Beneficiary/NavTab/Salary";
import General from "@/Pages/Beneficiary/NavTab/General";
import Biometrics from "@/Pages/Beneficiary/NavTab/Biometrics";

export default {
    metaInfo: {
        title: "Add Beneficiary"
    },

    layout: Layout,

    props: {
        beneficiary_view_model: Object,
    },

    components: {
        Tab,
        Icon,
        HTabs,
        Salary,
        General,
        Biometrics,
    },

    data() {
        return {
            beneficiary: this.beneficiary_view_model.beneficiary,
            general: {
                'basic': this.beneficiary_view_model,
            },
        }
    },

    mounted() {
        this.setBeneficiary(this.beneficiary);
    },

    methods: {
        ...mapMutations([
            'setBeneficiary'
        ]),
    },
}
</script>

