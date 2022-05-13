<template>
  <b-container class="top">
    <v-card class="center card">
      <div class="text-center center w-75 mt-5">
        <img src="../../assets/logo3.png" class="mb-5 img" />
        <v-text-field v-model="name" label="Name" required solo> </v-text-field>
        <v-text-field v-model="email" label="Email" required solo>
        </v-text-field>

        <v-text-field
          solo
          v-model="password"
          :append-icon="showPassw ? 'mdi-eye' : 'mdi-eye-off'"
          :type="showPassw ? 'text' : 'password'"
          label="Password"
          @click:append="showPassw = !showPassw"
        ></v-text-field>
       
        <v-text-field type="date" v-model="birthdate" solo></v-text-field>

        <v-btn color="#44c6ac" class="w-100 mb-2 button" large @click.prevent="signup">
          Sign up
        </v-btn>
        <v-btn color="#DD2929" class="w-100 mb-5 button"  large>
         <router-link  style="text-decoration:none; color:white" to="/">Back to Sign in</router-link>
        </v-btn>
        <br />
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
      name: "",
      email: "",
      birthdate: "",
      password: "",
      errors: null,
      showPassw: false,
    };
  },
  methods: {
    async signup() {
      axios
        .post("users", {
          name: this.name,
          email: this.email,
          birthdate: this.formatDate(this.birthdate), //String(this.birthdate).replaceAll(/-/g,"/"),
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
    formatDate(date) {
      return date.split("-").reverse().join("/");
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
  min-width: 44vmax;
  border-radius: 10px;
  color: white;
  font-size: 2rem;
  color: black;
  }

.top {
  margin-top: 12vh;
}

.button{
  color: white
}
</style>
