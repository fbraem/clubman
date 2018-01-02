import "babel-polyfill";

import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuex from 'vuex';
Vue.use(Vuex);
import store from '@/js/store';
import newsStore from './store';
store.registerModule('newsModule', newsStore);
import categoryStore from '@/apps/categories/store';
store.registerModule('categoryModule', categoryStore);

import Vuetify from 'vuetify';
Vue.use(Vuetify);
import '@/../node_modules/vuetify/dist/vuetify.min.css';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);
import messages from './lang/lang.js';
const i18n = new VueI18n({
    locale : 'nl',
    fallbackLocale : 'nl',
    messages
});
import moment from 'moment';
moment.locale('nl');

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import VueKindergarten from 'vue-kindergarten';
Vue.use(VueKindergarten, {
    child : (store) => {
        return store ? store.getters.user : null;
    }
});
import basePerimeter from '@/js/perimeter.js';
import storyPerimeter from './perimeter.js';

import { VueExtendLayout, layout } from 'vue-extend-layout';
Vue.use(VueExtendLayout);

import NewsApp from './App.vue';
import NewsCreate from './app/NewsCreate.vue';
import NewsUpdate from './app/NewsUpdate.vue';
import NewsBrowse from './app/NewsBrowse.vue';
import NewsRead from './app/NewsRead.vue';

const router = new VueRouter({
    routes : [
        {
            path : '/',
            component : NewsApp,
            children: [
                {
                    path : 'story/:id',
                    component : NewsRead,
                    props : true
                },
                {
                    path : 'category/:category_id',
                    component : NewsBrowse,
                    props : true
                },
                {
                    path : 'archive/:year/:month',
                    component : NewsBrowse,
                    props : true
                },
                {
                    path : '',
                    component : NewsBrowse
                }
            ]
        },
        {
            path : '/create',
            component : NewsCreate
        },
        {
            path : '/update/:id',
            component : NewsUpdate
        }
    ]
});

var app = new Vue({
    router,
    store,
    perimeters : [
        basePerimeter,
        storyPerimeter
    ],
    ...layout,
    i18n
}).$mount('#clubmanApp');
