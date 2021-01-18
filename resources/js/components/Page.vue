<template>
    <div>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <header-component :title="title" :breadcrumb="breadcrumb"></header-component>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <slot></slot>
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
</template>

<script>
    export default {
        name: "Page",
        props: {
            title: {
                type: String,
                required: true
            },

            breadcrumb: {
                type: Array,
                required: true
            }
        },
    }
</script>

<style scoped>

</style>
