<template>
    <div class="register-box">
        <div class="register-logo">
            <img src="/images/softbeleza-logo-azul.png" alt="Logo SoftBeleza">
        </div>

        <vue-snotify></vue-snotify>
        <router-view></router-view>
        <!-- /.card -->
    </div>
</template>

<script>
    export default {
        name: "Auth"
    }
</script>

<style scoped>

</style>
