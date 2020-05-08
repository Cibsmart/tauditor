<template>
    <tabs>
        <tab title="General" class="ml">
            <p>General</p>
        </tab>
        <tab title="Bio">
            <p>Bio</p>
        </tab>
        <tab title="Salary">
            <p>Salary</p>
        </tab>
    </tabs>
</template>

<script>
import Icon from "@/Shared/Icon";
import Tab from "@/Shared/Tab";
import Tabs from "@/Shared/Tabs";
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
        Tabs,
    },

    data() {
        return {
        }
    },

    methods: {

    }
}
</script>

