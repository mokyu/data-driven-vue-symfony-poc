/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import Vue from 'vue';
import App from './App';
import bootstrap from "./bootstrap";

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Dashboard from "./components/views/Dashboard";
import Members from "./components/views/Members";

const routes = [
    { path: '/', component: Dashboard},
    {path: '/members', component: Members}
]

const router = new VueRouter({
    routes
})

new Vue({
    el: '#vue-app',
    ...bootstrap,
    router,
    render: h => h(App),
});