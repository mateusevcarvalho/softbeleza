<template>
    <div class="card card-primary card-outline">
        <div class="card-body">
            <slot></slot>
        </div>

        <div class="overlay" v-if="load">
            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Card",
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
