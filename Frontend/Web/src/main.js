import { createApp } from 'vue'
import axios from "axios";
import App from './App.vue'
import router from './router'
import store from './store'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import { ElNotification } from 'element-plus'
import VueChartkick from 'vue-chartkick'
import 'chartkick/chart.js'
import VueApexCharts from "vue3-apexcharts";
import { Quasar } from 'quasar'
import quasarUserOptions from './quasar-user-options'
axios.defaults.baseURL = 'http://backend.test/api'

const app = createApp(App).use(Quasar, quasarUserOptions).use(store).use(router).use(ElementPlus).use(VueChartkick).use(VueApexCharts).mount('#app')
