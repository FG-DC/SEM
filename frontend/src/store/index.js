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
    profileUpdate: false,
    navbarUpdate: false,
    trainingExamples: false,
  },
  getters: {
    username: (state) => state.username,
    user_id: (state) => state.user_id,
    userType: (state) => state.userType,
    get_started: (state) => state.get_started,
    divisionUpdate: (state) => state.divisionUpdate,
    equipmentUpdate: (state) => state.equipmentUpdate,
    affiliateUpdate: (state) => state.affiliateUpdate,
    profileUpdate: (state) => state.profileUpdate,
    navbarUpdate: (state) => state.navbarUpdate,
    trainingExamples: (state) => state.trainingExamples,
  },
  mutations: {
    mutationAuthOk(state) {
      state.status = localStorage.getItem("status");
      state.username = localStorage.getItem("username");
      state.user_id = localStorage.getItem("user_id");
      state.access_token = localStorage.getItem("access_token");
      state.userType = localStorage.getItem("userType");
      state.get_started = localStorage.getItem("get_started");
    },
    mutationAuthReset(state) {
      (state.status = false),
        (state.username = null),
        (state.userType = null),
        (state.user_id = null),
        (state.access_token = null),
        localStorage.removeItem("username");
      localStorage.removeItem("user_id");
      localStorage.removeItem("access_token");
      localStorage.removeItem("userType");
      localStorage.removeItem("get_started");
      localStorage.removeItem("status");
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
    async SOCKET_trainingExamples(state) {
      state.trainingExamples = !state.trainingExamples;
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
    logout(context, state) {
      this.$socket.emit("logged_out", state);
      context.commit("mutationAuthReset");
    },
    async authRequest(context, credentials) {
      await axios
        .post("/login", {
          username: credentials.username,
          password: credentials.password,
        })
        .then(async (response) => {
          axios.defaults.headers.common.Authorization =
            "Bearer " + response.data.access_token;
          localStorage.setItem("access_token", response.data.access_token);
          localStorage.setItem("status", true);

          await context.dispatch("getAuthUser");

          this.$socket.emit("logged_in", response.data.id);
        })
        .catch(() => {
          context.commit("mutationAuthReset");
        });
    },
    async getAuthUser(context) {
      await axios
        .get("/user")
        .then((response) => {
          localStorage.setItem("username", response.data.email);
          localStorage.setItem("user_id", response.data.id);
          localStorage.setItem("get_started", response.data.get_started);
          localStorage.setItem("userType", response.data.type);
          context.commit("mutationAuthOk");
        })
        .catch((error) => {
          context.commit("mutationAuthReset");
          console.log(error);
        });
    },

    authLogout(context) {
      axios
        .post("/logout")
        .then((response) => {
          localStorage.removeItem("access_token");
          localStorage.removeItem("username");
          localStorage.removeItem("userType");
          context.commit("mutationAuthReset");
          resolve(response);
        })
        .catch((error) => {
          context.commit("mutationAuthReset");
          reject(error);
        });
    },
  },
});
