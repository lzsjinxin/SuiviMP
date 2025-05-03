import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from "./state/store";
import i18n from "./i18n";
import BootstrapVueNext from 'bootstrap-vue-next';
import VueApexCharts from "vue3-apexcharts";
import PhosphorIcons from "@phosphor-icons/vue";
import Wizard from 'form-wizard-vue3';
import vSelect from 'vue-select'
// import CoolLightBox from 'vue-cool-lightbox';

// Packages CSS import
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
import '@vueform/slider/themes/default.css';
import 'form-wizard-vue3/dist/form-wizard-vue3.css'
import 'simplebar-vue/dist/simplebar.min.css';
import 'vue-select/dist/vue-select.css'
import '@/assets/scss/style.scss';

// bootstrap.bundle.js
import 'bootstrap/dist/js/bootstrap.bundle.js';
// Import Axios
import axios from 'axios';

axios.defaults.baseURL = 'http://192.168.1.62:8000';
axios.defaults.withCredentials = false; 



createApp(App)
.use(store)
.use(router)
.use(i18n)
.use(BootstrapVueNext)
.use(VueApexCharts)
.use(PhosphorIcons)
// .use(CoolLightBox)
.component('Wizard', Wizard)
.component('v-select', vSelect)
.mount('#app')