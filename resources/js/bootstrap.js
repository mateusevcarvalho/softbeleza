import NProgress from 'nprogress';
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    require('admin-lte');
} catch (e) {
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['Content-Type'] = 'application/json';
window.axios.defaults.headers.common['Accept'] = 'Application/json';

const token = localStorage.getItem('_token');
if (token)
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

// before a request is made start the nprogress
window.axios.interceptors.request.use(config => {
    NProgress.start();
    return config
});

// before a response is returned stop nprogress
window.axios.interceptors.response.use(response => {
    NProgress.done();
    return response
});
