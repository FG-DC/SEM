<template>
  <Navbar
    v-if="currentRouteName != 'signup' && currentRouteName != 'signin'"
    @obsAnalyse="obsAnaliyse"
  />
  <div
    class="w-100"
    :class="{
      'col-md-9 ms-sm-auto col-lg-10 px-md-4':
        currentRouteName != 'signup' && currentRouteName != 'signin',
    }"
  >
    <router-view />
  </div>
</template>


<script>
import axios from "axios";
import Navbar from "./components/Navbar.vue";
import { ref } from 'vue'


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
   setup () {
    return {
      leftDrawerOpen: ref(false)
    }
  },

  created() {
    this.$store.dispatch("fillStore");
  },
};
</script>



<style>
</style>
