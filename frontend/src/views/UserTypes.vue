<template>
  <v-container class="text-center mt-2" style="color: black; user-select: none">
    <v-card
      @click="$router.push({ name: 'admins' })"
      elevation="6"
      class="flex-grow-1 card-selectable"
      style="border-radius: 10px"
    >
      <b-container class="icons">
        <b-row no-gutters>
          <b-col cols="2">
            <font-awesome-icon
              class=""
              icon="fa-solid fa-user-gear"
            />
          </b-col>
          <b-col>
            <span class="p-5">Administrators</span>
          </b-col>
        </b-row>
      </b-container>
    </v-card>

    <v-card
      @click="$router.push({ name: 'users' })"
      elevation="6"
      class="flex-grow-1 mt-4 card-selectable"
      style="border-radius: 10px"
    >
         <b-container class="icons">
        <b-row no-gutters>
          <b-col cols="2">
            <font-awesome-icon
              class=""
              icon="fa-solid fa-user"
            />
          </b-col>
          <b-col>
            <span class="p-5">Users</span>
          </b-col>
        </b-row>
      </b-container>
    </v-card>
    
   
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
    };
  },
  computed: {

  },
  async created() {

  },
  methods: {
  
  },
};
</script>
  
<style scoped>
.icons {
  color: #191645;
  font-size: 5.5vw;
}
.card-selectable {
  cursor: pointer;
  transition: transform 200ms;
}

.card {
  animation: shake 4.7s ease;
  animation-iteration-count: infinite;
}
</style>