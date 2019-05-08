import Vue from 'vue';
import VueRouter from 'vue-router'

import Main from './pages/MainPage';
import About from './pages/AboutPage';
import Login from './pages/LoginPage';
import Registration from './pages/RegistrationPage';
import Categories from './pages/CategoriesPage';
import Category from './pages/CategoryPage';
import Product from './pages/ProductPage';

// const Main = () => import(/* webpackChunkName: "home" */'./pages/MainPage');
// const About = () => import(/* webpackChunkName: "about" */'./pages/AboutPage');
// const Login = () => import(/* webpackChunkName: "login" */'./pages/LoginPage');
// const Registration = () => import(/* webpackChunkName: "registration" */'./pages/RegistrationPage');
// const Categories = () => import(/* webpackChunkName: "categories" */'./pages/CategoriesPage');
// const Category = () => import(/* webpackChunkName: "category" */'./pages/CategoryPage');
// const Product = () => import(/* webpackChunkName: "product" */'./pages/ProductPage');

Vue.use(VueRouter);

let routes = [
    { path: '/', component: Main, name: 'main'},
    { path: '/about', component: About, name: 'about'},
    { path: '/login', component: Login, name: 'login'},
    { path: '/registration', component: Registration, name: 'registration' },
    { path: '/categories', component: Categories, name: 'categories' },
    { path: '/category/:id', component: Category, name: 'category' },
    { path: '/product/:id', component: Product, name: 'product' }
];

let router = new VueRouter({
    mode: 'history',
    routes
});

export default router;