import Vue from 'vue';
import MultiSelect from 'vue-multiselect';
import draggable from 'vuedraggable';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

window.axios = require('axios');

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);

Vue.component('multiselect', MultiSelect);
Vue.component('draggable', draggable);
Vue.component('modal', require('./components/modal.vue').default);
Vue.component('nested', require('./components/nested.vue').default);
Vue.component('structure', require('./components/structure.vue').default);
Vue.component('menu-editor', require('./components/menu.vue').default);

const app = new Vue({
    el: '#app'
});
