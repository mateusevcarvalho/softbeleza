<template>
    <div class="wrapper">
        <AlertTopHeader/>

        <!-- Navbar -->
        <header-top-component></header-top-component>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/agenda" class="brand-link" style="font-family: 'American Typewriter',cursive; font-size: 30px; color: #6982ff">
                <img src="/images/icone-softbeleza-azul.png" alt="Logo SoftBeleza" style="width: 50px; margin-bottom: 14px"/>
                <span class="brand-text font-weight-normal mt-3">SoftBeleza</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->

                <!-- Sidebar Menu -->
                <menu-component></menu-component>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <router-view></router-view>
        <vue-snotify></vue-snotify>

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                Anything you want
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2020
                <router-link to="/">SoftBeleza</router-link>
                .
            </strong>
            Todos os direitos reservados.
        </footer>
    </div>
</template>

<script>
import moment from 'moment'
import AlertTopHeader from "../components/AlertTopHeader";

export default {
    name: "Master",
    components: {AlertTopHeader}
}
</script>

<style scoped>

</style>
