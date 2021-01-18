import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes.map'
import NProgress from 'nprogress'
import Auth from './../auth'

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes
});

router.beforeResolve((to, from, next) => {
    const user = JSON.parse(localStorage.getItem('dataUsuario'))
    if (user?.controle_acessos && to.meta?.permission) {
        const auth = new Auth(user);
        if (!auth.can(to.meta.permission)) {
            localStorage.removeItem('dataUsuario');
            localStorage.removeItem('_token');
            window.location.href = '/';
        }
    }

    NProgress.start();
    next();
});

router.beforeEach((to, from, next) => {
    if (to.matched[0].name === 'master') {
        document.getElementsByTagName("body")[0].classList.add('sidebar-mini');
        document.getElementsByTagName("body")[0].classList.remove('register-page');
    } else if (to.matched[0].name === 'auth') {
        document.getElementsByTagName("body")[0].classList.remove('sidebar-mini');
        document.getElementsByTagName("body")[0].classList.add('register-page');
    }

    if (to.meta.requireAuth) {
        if (localStorage.getItem('_token')) {
            next();
        } else {
            window.location.href = '/';
        }
    } else {
        next();
    }
});

router.afterEach((to, from) => {
    NProgress.done()
});

export default router;
