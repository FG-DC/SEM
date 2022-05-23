<template>
  <b-container class="top">
    <v-card class="center card">
      <div class="text-center center w-75 mt-5">
        <img src="../../assets/logo3.png" class="mb-5 img" />

        <v-text-field v-model="email" label="Email" required solo>
        </v-text-field>

        <v-text-field
          solo
          v-model="password"
          :append-icon="showPassw ? 'mdi-eye' : 'mdi-eye-off'"
          :type="showPassw ? 'text' : 'password'"
          label="Password"
          @click:append="showPassw = !showPassw"
        >
        </v-text-field>

        <v-btn color="#44c6ac" class="w-100" large @click.prevent="signin">
          Log In
        </v-btn>

        <br />

        <v-card-subtitle class="mt-1">
          Don't have an account? Click to

          <router-link :to="{ name: 'signup' }">Register</router-link>
        </v-card-subtitle>
      </div>
    </v-card>
  </b-container>
</template>

<script>
import axios from "axios";

export default {
  auth: false,
  data() {
    return {
      email: "",
      password: "",
      errors: null,
      showPassw: false,
      password: "",
    };
  },
  methods: {
    signin() {
      this.$store
        .dispatch("authRequest", {
          username: this.email,
          password: this.password,
        })
        .then(() => {
          this.$router.push({
            name: "dashboard",
          });
        })
        .catch((error) => {
          localStorage.removeItem("access_token");
          localStorage.removeItem("username");
          delete axios.defaults.headers.common.Authorization;
          this.errors = error;
        });
    },
    onReset() {
      this.email = null;
      this.password = null;
    },
  },
};
</script>


<style scoped>
img {
  min-width: 22vmax;
  width: 20vw;
}

.center {
  margin-left: auto;
  margin-right: auto;
}

.card {
  width: 40vw;
  min-width: 40vmax;
  border-radius: 10px;
  background-color: #191645;
  color: white;
  font-weight: bold;
}

.top {
  margin-top: 20vh;
}
</style>
