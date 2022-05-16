import Vue from 'vue'
import VueRouter from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import LogIn from '../views/auth/Login.vue'
import Register from '../views/auth/Register.vue'
import Profile from '../views/Profile.vue'
import GetStarted from '../views/GetStarted.vue'
import IndividualRead from '../views/IndividualRead.vue'
import Settings from '../views/Settings.vue'
import Affiliates from '../views/Affiliates.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'login',
    component: LogIn
  },
  {
    path: '/register',
    name: 'register',
    component: Register
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile
  },
  {
    path: '/getStarted',
    name: 'getStarted',
    component: GetStarted
  },
  {
    path: '/read',
    name: 'read',
    component: IndividualRead
  },
  {
    path: '/settings',
    name: 'settings',
    component: Settings
  },
  {
    path: '/affiliates',
    name: 'affiliates',
    component: Affiliates
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
