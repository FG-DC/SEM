<template>
  <div>
    <div
      class="container-login100"
      style="background-image: url('images/s1.jpg')"
    >
      <div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30">
        <div class="login100-form validate-form">
          <span class="login100-form-title p-b-37"> Sign Up </span>

          <div class="wrap-input100 validate-input m-b-20">
            <input
              class="input100"
              type="text"
              v-model="name"
              placeholder="Name"
            />
            <span class="focus-input100"></span>
          </div>

          <div class="wrap-input100 validate-input m-b-20">
            <input
              class="input100"
              type="text"
              v-model="email"
              placeholder="Email"
            />
            <span class="focus-input100"></span>
          </div>

          <div class="wrap-input100 validate-input m-b-20">
            <input
              class="input100"
              type="date"
              v-model="birthdate"
              placeholder="Birthdate"
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
              class="login100-form-btn"
              style="background-color: #379657"
              @click="signup"
            >
              Sign Up
            </button>
              <div class="text-center p-t-35">
           
            <span class="txt1 p-t-30">
              Already have an account? <router-link to="/">Sign in</router-link> 
            </span>
          </div>
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
      name: "",
      email: "",
      birthdate: "",
      password: "",
      errors: null,
    };
  },
  methods: {
    async signup() {
      axios.post("users", {
          name: this.name,
          email: this.email,
          birthdate:  this.formatDate(this.birthdate), //String(this.birthdate).replaceAll(/-/g,"/"),
          password: this.password,
        })
        .then(async () => {
          await this.$store.dispatch("authRequest", {
            username: this.email,
            password: this.password,
          });
          this.$router.push({
            name: "dashboard",
          });
        })
        .catch((error) => {
          console.log("Error");
        });
    },
	formatDate(date){
		return date.split("-").reverse().join("/")
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
