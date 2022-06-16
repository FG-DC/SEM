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
import Equipments from '../views/Equipments.vue'
import Divisions from '../views/Divisions.vue'
import Alerts from '../views/Alerts.vue'


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
  {
    path: '/settings/divisions',
    name: 'divisions',
    component: Divisions
  },
  {
    path: '/settings/equipments',
    name: 'equipments',
    component: Equipments
  },
  {
    path: '/alerts',
    name: 'alerts',
    component: Alerts
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})


import store from '../store'

router.beforeEach((to, from, next) => {
  //store.dispatch("fillStore");
  var userType = store.state.userType;
  var isAuthenticated = store.state.status;
  if ((to.name == 'login') || (to.name == 'register')) {
    next()
    return
  }
  if (!isAuthenticated) {
    next({ name: 'login' })
    return
  }
  if (to.name == 'dashboardAdmin') {
    if (userType != 'C') {
      next({ name: 'dashboard' })
      return
    }
  }
  /*
  if (to.name == 'dashboard') {
    if (userType != 'A') {
      next({ name: 'dashboardAdmin' })
      return
    }
  }
  */
  next()
});

export default router
