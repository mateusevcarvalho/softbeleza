<template>
    <div>
        <span class="badge badge-danger" v-if="!status">Inativo</span>
        <span class="badge badge-success" v-else>Ativo</span>
    </div>
</template>

<script>
    export default {
        name: "Status",
        props: ['status']
    }
</script>

<style scoped>

</style>
