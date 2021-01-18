<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
            data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->

            <li class="nav-item"
                v-for="menu in dataMenu"
                v-if="$auth.can(menu.permission) && ((menu.nameRoute === 'Tour' && tour)|| menu.nameRoute !== 'Tour')"
                :class="isMenuOpen(menu, $route.name) ? 'menu-open' : ''">
                <router-link :to="{name: menu.name}"
                             class="nav-link" :class="menu.name === $route.name ? 'active' : ''"
                             v-if="!menu.hasOwnProperty('submenu')">
                    <i :class="'nav-icon fas fa-' + menu.icon"></i>
                    <p>{{ menu.nameRoute }}</p>
                </router-link>
                <a href="javascript:void(0)"
                   class="nav-link" :class="isMenuOpen(menu, $route.name) ? 'active' : ''"
                   v-if="menu.hasOwnProperty('submenu')">
                    <i :class="'nav-icon fas fa-' + menu.icon"></i>
                    <p>
                        {{ menu.nameRoute }}
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" :style="{display: isMenuOpen(menu, $route.name) ? 'block' : 'none' }"
                    v-if="menu.hasOwnProperty('submenu')"
                    v-for="menuChild in menu.submenu">
                    <li class="nav-item">
                        <router-link :to="{name: menuChild.name}"
                                     class="nav-link" :class="menuChild.name === $route.name ? 'active' : ''">
                            <i class="fas fa-dot-circle nav-icon"></i>
                            <p>{{ menuChild.nameRoute }}</p>
                        </router-link>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>
</template>

<script>
export default {
    name: "Menu",
    data() {
        return {
            tour: !!parseInt(localStorage.getItem('tour')),
            dataMenu: [
                {nameRoute: 'Tour', name: 'tour', icon: 'list'},
                {nameRoute: 'Agenda', name: 'agenda', icon: 'calendar-alt', permission: 'agenda'},
                {nameRoute: 'Caixa', name: 'caixa', icon: 'cash-register', permission: 'caixa'},
                {nameRoute: 'Clientes', name: 'clientes', icon: 'users', permission: 'clientes'},
                {nameRoute: 'Profissionais', name: 'profissionais', icon: 'id-card-alt', permission: 'profissionais'},
                {nameRoute: 'Serviços', name: 'servicos', icon: 'cut', permission: 'servicos'},
                {nameRoute: 'Estoque', name: 'estoque', icon: 'cubes', permission: 'estoque'},
                {nameRoute: 'Fornecedores', name: 'fornecedores', icon: 'truck', permission: 'fornecedores'},

                {
                    nameRoute: 'Relatórios', name: null, icon: 'chart-line', submenu: [
                        {nameRoute: 'Faturamento P/ Período', name: 'faturamento-periodo'},
                        {nameRoute: 'Forma de Pagamento', name: 'faturamento-forma-pagamento'},
                        {nameRoute: 'Fechamento de Caixa', name: 'fechamento-caixa'},
                        {nameRoute: 'Rateio P/ Profissional', name: 'rateio-profissional'},
                        {nameRoute: 'Lista de Aniversariantes', name: 'lista-aniversariantes'},
                    ], permission: 'relatorios'
                },

                {
                    nameRoute: 'Configurações', name: null, icon: 'cog', submenu: [
                        {nameRoute: 'Estabelecimento', name: 'estabelecimento'},
                        {nameRoute: 'Meios de Pagamento', name: 'meiosPagamentos'},
                    ], permission: 'configuracoes'
                },
            ]
        }
    },
    methods: {
        isMenuOpen(menuItem, routeName) {
            return menuItem.hasOwnProperty('submenu') &&
                !!menuItem.submenu.find(item => item.name === routeName);
        }
    }
}
</script>

<style scoped>

</style>
