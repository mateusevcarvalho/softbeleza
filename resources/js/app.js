import Menu from "./layouts/Menu";

require('./bootstrap');

import Vue from 'vue'
import router from "./routes";
import store from "./store";

import Header from "./components/Header";
import HeaderTop from "./layouts/HeaderTop";
import Page from "./components/Page";
import Card from "./components/Card";
import Status from "./components/Status";
import {HasError, AlertError} from 'vform';
import Snotify, {SnotifyPosition} from 'vue-snotify';
import VueTheMask from 'vue-the-mask'
import VueResource from 'vue-resource';
import vSelect from 'vue-select'
import {BootstrapVue} from 'bootstrap-vue'
import money from 'v-money'
import moment from 'moment'
import VueCal from 'vue-cal'
import "vue-cal/dist/vuecal.css";
import 'vue-cal/dist/i18n/pt-br'
import ECharts from 'vue-echarts' // refers to components/ECharts.vue in webpack
import Auth from './auth'
import VueSweetalert2 from 'vue-sweetalert2';

// import ECharts modules manually to reduce bundle size
import 'echarts/lib/chart/line'
import 'echarts/lib/chart/bar'
import 'echarts/lib/component/tooltip'
import 'sweetalert2/dist/sweetalert2.min.css';

// If you want to use ECharts extensions, just import the extension package and it will work
// Taking ECharts-GL as an example:
// You only need to install the package with `npm install --save echarts-gl` and import it as follows
//import 'echarts-gl'

const options = {
    toast: {
        position: SnotifyPosition.rightTop
    }
};

Vue.use(VueSweetalert2);
Vue.use(Snotify, options);
Vue.use(VueTheMask);
Vue.use(VueResource);
Vue.use(BootstrapVue);
Vue.use(money, {
    decimal: ',',
    thousands: '.',
    prefix: 'R$ ',
    precision: 2,
    masked: false
});

Vue.directive('uppercase', {
    update (el) {
        el.value = el.value.toUpperCase()
    },
})

Vue.component('menu-component', Menu);
Vue.component('status-component', Status);
Vue.component('header-component', Header);
Vue.component('header-top-component', HeaderTop);
Vue.component('page-component', Page);
Vue.component('card-component', Card);
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component('v-select', vSelect);
Vue.component('vue-cal', VueCal);
Vue.component('v-chart', ECharts);

Vue.filter('firstLastName', function (value) {
    const dataNome = value.split(' ');
    const ultimoNome = dataNome.length > 1 ? ' ' + dataNome[dataNome.length - 1] : '';
    return dataNome[0] + ultimoNome;
});

Vue.filter('dateBr', function (value) {
    if (value)
        return moment(value).format('DD/MM/YYYY');
    return '';
});

Vue.filter('dateTimeBr', function (value) {
    return moment(value).format('DD/MM/YYYY HH:mm');
});

Vue.filter('time', function (value) {
    return moment(value, 'HH:mm:ss').format('HH:mm');
});

Vue.filter('celular', function (value) {
    if (value && value.length === 11) {
        let cleaned = ('' + value).replace(/\D/g, '');
        let match = cleaned.match(/^(\d{2})(\d{5})(\d{4})$/);
        if (match) {
            return `(${match[1]}) ${match[2]}-${match[3]}`;
        }
    } else if (value && value.length === 10) {
        let cleaned = ('' + value).replace(/\D/g, '');
        let match = cleaned.match(/^(\d{2})(\d{4})(\d{4})$/);
        if (match) {
            return `(${match[1]}) ${match[2]}-${match[3]}`;
        }
    }

    return value;
});

Vue.filter('money', function (value) {
    return new Intl.NumberFormat('pt-BR', {style: 'currency', currency: 'BRL'}).format(value);
});

Vue.filter('percent', function (value) {
    return new Intl.NumberFormat('pt-BR',
        {style: 'decimal', minimumFractionDigits: 2}).format(value) + ' %';
});

Vue.prototype.$auth = new Auth(JSON.parse(localStorage.getItem('dataUsuario')));

const app = new Vue({
    el: "#app",
    router,
    store,
    beforeCreate() {
        Vue.$snotify = this.$snotify;
    },
});
