<template>
    <h-tabs>
        <tab title="General">
            <p>General</p>
        </tab>
        <tab title="Bio Data">
            <p>Bio Data</p>
        </tab>
        <tab title="Salary" active>
            <p>Salary</p>
        </tab>
    </h-tabs>
</template>

<script>
import Icon from "@/Shared/Icon";
import Tab from "@/Shared/Tab";
import HTabs from "@/Shared/HTabs";
import Layout from "@/Shared/Layout";

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
    },

    data() {
        return {
        }
    },

    methods: {

    }
}
</script>

