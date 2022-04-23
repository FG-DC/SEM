<template>
  <div>
    <div
      class="container-login100"
      style="background-image: url('images/s1.jpg')"
    >
      <div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30">
        <div class="login100-form validate-form">
          <span class="login100-form-title p-b-37"> Login </span>

          <div class="wrap-input100 validate-input m-b-20">
            <input
              class="input100"
              type="text"
              v-model="email"
              placeholder="Email"
            />
            <span class="focus-input100"></span>
          </div>

          <div class="wrap-input100 validate-input m-b-25">
            <input
              class="input100"
              type="password"
              v-model="password"
              placeholder="Password"
            />
            <span class="focus-input100"></span>
          </div>

          <div class="container-login100-form-btn">
            <button
              @click.prevent="signin"
              type="submit"
              class="login100-form-btn"
              style="background-color: #379657"
            >
              Sign In
            </button>
          </div>
          <div class="text-center p-t-35">
              <img class="p-b-10" src="../../../assets/icons/icon-google.png" alt="GOOGLE" />
            <br />
            <span class="txt1 p-t-30">
              Don't have an account? Click to <router-link to="signup">Sign Up</router-link> 
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
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

@import "../../../assets/css/util.css";
@import "../../../assets/css/main.css";

</style>
