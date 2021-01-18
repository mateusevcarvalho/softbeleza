<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{title}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item" v-for="bread in breadcrumb">
                            <router-link v-if="bread.to" :to="{name: bread.to}">{{bread.name}}</router-link>
                            <span v-else>{{bread.name}}</span>
                        </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
</template>

<script>
    export default {
        name: "Header",
        props: {
            title: {
                type: String,
                required: true
            },

            breadcrumb: {
                type: Array,
                required: true
            }
        }
    }
</script>

<style scoped>

</style>
