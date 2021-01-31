<template>
    <div id="loading" class="d-flex justify-content-center align-items-center" v-if="load">
        <img src="/images/load.gif" alt="Loading..."/>
    </div>
</template>

<script>
export default {
    name: "LoadFull",
    props: {
        load: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style scoped>

</style>
