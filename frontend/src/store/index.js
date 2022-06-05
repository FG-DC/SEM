import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    username: null,
    userType: null,
    user_id: null,
    status: false,
    access_token: null,
    get_started: null,
    divisionUpdate: false,
    equipmentUpdate: false,
    affiliateUpdate: false,
    profileUpdate:false,
    navbarUpdate:false
  },
  getters: {
    username: (state) => state.username,
    user_id: (state) => state.user_id,
    get_started: (state) => state.get_started,
    divisionUpdate: (state) => state.divisionUpdate,
    equipmentUpdate: (state) => state.equipmentUpdate,
    affiliateUpdate: (state) => state.affiliateUpdate,
    profileUpdate: (state) => state.profileUpdate,
    navbarUpdate: (state) => state.navbarUpdate,
  },
  mutations: {
    mutationAuthOk(state) {
      state.status = true;
      state.username = localStorage.getItem("username");
      state.userType = localStorage.getItem("user_type");
      state.user_id = localStorage.getItem("user_id");
      state.access_token = localStorage.getItem("access_token");
      state.get_started = localStorage.getItem("get_started");
    },
    mutationAuthReset(state) {
      (state.status = false),
      (state.username = null),
      (state.userType = null),
      (state.user_id = null),
      (state.access_token = null),
      localStorage.removeItem("username");
      localStorage.removeItem("user_type");
      localStorage.removeItem("user_id");
      localStorage.removeItem("access_token");
    },
    async SOCKET_divisionUpdate(state) {
      state.divisionUpdate = !state.divisionUpdate;
    },
    async SOCKET_equipmentUpdate(state) {
      state.equipmentUpdate = !state.equipmentUpdate;
    },
    async SOCKET_affiliateUpdate(state) {
      state.affiliateUpdate = !state.affiliateUpdate;
    },
    async SOCKET_profileUpdate(state) {
      state.profileUpdate = !state.profileUpdate;
    },
    async SOCKET_navbarUpdate(state) {
      state.navbarUpdate = !state.navbarUpdate;
    },
  },

  actions: {
    fillStore(context) {
      context.commit("mutationAuthOk");
      axios.defaults.headers.common.Authorization =
        "Bearer " + this.state.access_token;
    },
    logout(context) {
      context.commit("mutationAuthReset");
    },
    authRequest(context, credentials) {
      return new Promise((resolve, reject) => {
        axios
          .post("/login", {
            username: credentials.username,
            password: credentials.password,
          })
          .then((response) => {
            axios.defaults.headers.common.Authorization =
              "Bearer " + response.data.access_token;
            localStorage.setItem("access_token", response.data.access_token);
            localStorage.setItem("username", credentials.username);
            axios
              .get("/user")
              .then((response) => {
                localStorage.setItem("user_id", response.data.id);
                this.$socket.emit("logged_in", response.data.id);
                localStorage.setItem("get_started", response.data.get_started);
                context.commit("mutationAuthOk");
                resolve(response);
              })
              .catch((error) => {
                context.commit("mutationAuthReset");
                console.log(error);
                reject(error);
              });
          })
          .catch((error) => {
            context.commit("mutationAuthReset");
            reject(error);
          });
      });
    },

    authLogout(context) {
      return new Promise((resolve, reject) => {
        axios
          .post("/logout")
          .then((response) => {
            localStorage.removeItem("access_token");
            localStorage.removeItem("username");
            localStorage.removeItem("user_type");
            context.commit("mutationAuthReset");
            resolve(response);
          })
          .catch((error) => {
            context.commit("mutationAuthReset");
            reject(error);
          });
      });
    },
  },
});
