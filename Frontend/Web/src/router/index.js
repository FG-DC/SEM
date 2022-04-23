import { createRouter, createWebHistory } from 'vue-router'
import SignIn from '../views/auth/SignIn.vue'
import SignUp from '../views/auth/SignUp.vue'
import Dashboard from '../views/Dashboard.vue'
import Profile from '../views/Profile.vue'
import GetStarted from '../views/GetStarted.vue'





const routes = [
  {
    path: '/',
    name: 'signin',
    component: SignIn
  },
  {
    path: '/signup',
    name: 'signup',
    component: SignUp
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
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
