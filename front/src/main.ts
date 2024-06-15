import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import i18n from './i18n';
import router from './router';
import library from './font-awesome/index';


import './style.css';
import App from './App.vue';

const pinia = createPinia();

createApp(App)
    .use(pinia)
    .use(i18n)
    .use(router)
    .use(library)
    .component('font-awesome-icon', FontAwesomeIcon)
    .mount('#app');
