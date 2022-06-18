import Vue from "vue";
import VueRouter from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import LogIn from "../views/auth/Login.vue";
import Register from "../views/auth/Register.vue";
import Profile from "../views/Profile.vue";
import GetStarted from "../views/GetStarted.vue";
import IndividualRead from "../views/IndividualRead.vue";
import Settings from "../views/Settings.vue";
import Affiliates from "../views/Affiliates.vue";
import Equipments from "../views/Equipments.vue";
import Divisions from "../views/Divisions.vue";
import Alerts from "../views/Alerts.vue";
import AdminDashboard from "../views/DashboardAdmin.vue";
import EquipmentTypes from "../views/EquipmentTypes.vue";
import Users from "../views/Users.vue";


Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "login",
    component: LogIn,
  },
  {
    path: "/register",
    name: "register",
    component: Register,
  },
  {
    path: "/dashboard",
    name: "dashboard",
    component: Dashboard,
  },
  {
    path: "/profile",
    name: "profile",
    component: Profile,
  },
  {
    path: "/getStarted",
    name: "getStarted",
    component: GetStarted,
  },
  {
    path: "/read",
    name: "read",
    component: IndividualRead,
  },
  {
    path: "/settings",
    name: "settings",
    component: Settings,
  },
  {
    path: "/affiliates",
    name: "affiliates",
    component: Affiliates,
  },
  {
    path: "/settings/divisions",
    name: "divisions",
    component: Divisions,
  },
  {
    path: "/settings/equipments",
    name: "equipments",
    component: Equipments,
  },
  {
    path: "/alerts",
    name: "alerts",
    component: Alerts,
  },
  {
    path: "/admin",
    name: "adminDashboard",
    component: AdminDashboard,
  },
  {
    path: "/admin/equipmentTypes",
    name: "equipmentTypes",
    component: EquipmentTypes,
  },
  {
    path: "/admin/users",
    name: "users",
    component: Users,
  },
  {
    path: "*",
    redirect: "/dashboard",
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

import store from "../store";

router.beforeEach((to, from, next) => {
  if (to.name == "login" || to.name == "register") {
    store.commit("mutationAuthReset");
    next();
    return;
  }

  store.dispatch("fillStore").then(() => {
    var isAuthenticated = store.state.status;
    var userType = store.state.userType;
    if (!isAuthenticated) {
      next({ name: "login" });
      return;
    }


    if (to.path.split("/")[1] != "admin" && userType == "A") {
      next({ name: "adminDashboard" });
      return;
    }


    if (to.path.split("/")[1] == "admin" && userType == "C") {
      next({ name: "dashboard" });
      return;
    }
    
    
    if (to.path.split("/")[1] == "admin" && userType == "C") {
      next({ name: "dashboard" });
      return;
    }


    if (to.name == "dashboard") {
      if (userType == "A") {
        next({ name: "adminDashboard" });
        return;
      }
    }
    next();
  });
});

export default router;
