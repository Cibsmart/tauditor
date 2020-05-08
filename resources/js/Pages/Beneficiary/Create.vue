<template>
    <h-tabs>
        <tab title="General" active>
            <general></general>
        </tab>
        <tab title="Bio Data">
            <p>Bio Data</p>
        </tab>
        <tab title="Salary">
            <p>Salary</p>
        </tab>
    </h-tabs>
</template>

<script>
import Icon from "@/Shared/Icon";
import Tab from "@/Shared/Tab";
import HTabs from "@/Shared/HTabs";
import Layout from "@/Shared/Layout";
import General from "@/Pages/Beneficiary/NavTab/General";

export default {
    metaInfo: {
        title: "Add Beneficiary"
    },

    layout: Layout,

    props: {

    },

    components: {
        Tab,
        Icon,
        HTabs,
        General
    },

    data() {
        return {
        }
    },

    methods: {

    }
}
</script>

