<template>
  <div>
    <Navbar v-if="currentRouteName != 'login' && currentRouteName != 'register'" />
   
    <div class="w-100" :class="{'col-md-9 ms-sm-auto col-lg-10 px-md-4': currentRouteName != 'login' && currentRouteName != 'register'}"></div>
    <router-view />
  </div>
</template>

<script>
import axios from "axios";
import Navbar from "./components/Navbar.vue";

export default {
  components: {
    Navbar,
  },
  computed: {
    currentRouteName() {
      return this.$route.name;
    },
    userId() {
      return this.$store.getters.user_id;
    },
  },

  created() {
    this.$store.dispatch("fillStore");
  },
};
</script>
