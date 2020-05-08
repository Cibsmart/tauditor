<template>
    <div role="tabpanel" v-show="show">

        <slot></slot>
    </div>
</template>

<script>
export default {
    props:{
        title: String,
    },

    data(){
        return{
            show: false,
        }
    },
}
</script>

