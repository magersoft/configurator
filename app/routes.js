import Vue from 'vue';
import VueRouter from 'vue-router'

import LandingPage from './pages/LandingPage';
import AboutPage from './pages/AboutPage';
import LoginPage from './pages/LoginPage';
import RegistrationPage from './pages/RegistrationPage';
import CategoriesPage from './pages/CategoriesPage';
import CategoryPage from './pages/CategoryPage';
import ProductPage from './pages/ProductPage';

Vue.use(VueRouter);

let routes = [
    { path: '/', component: LandingPage, name: 'landing'},
    { path: '/about', component: AboutPage, name: 'about'},
    { path: '/login', component: LoginPage, name: 'login'},
    { path: '/registration', component: RegistrationPage, name: 'registration' },
    { path: '/categories', component: CategoriesPage, name: 'categories' },
    { path: '/category/:id', component: CategoryPage, name: 'category' },
    { path: '/product/:id', component: ProductPage, name: 'product' },
];

let router = new VueRouter({
    mode: 'history',
    routes
});

export default router;