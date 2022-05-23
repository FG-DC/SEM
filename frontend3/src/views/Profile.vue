<template>
  <b-container class="text-center mb-5">
    <v-card elevation="6" class="mt-5 mb-5 p-5">
      <label for="name">Name:</label>
      <v-text-field id="name" v-model="this.user.name" solo></v-text-field>
       <label for="birthdate">Birthdate:</label>
      <v-text-field id="birthdate" v-model="this.user.birthdate.split(' ')[0]" solo></v-text-field>
       <label for="email">Email:</label>
      <v-text-field id="email" v-model="this.user.email" solo></v-text-field>
    </v-card>
  </b-container>
</template>

<script>
import axios from "axios";
import EquipmentList from "../components/EquipmentList.vue";


export default {


    components: {
        EquipmentList
    },
    data(){
        return{
            user:[]
        }
    },
    created() {
       axios
      .get(`/user`)
      .then((response) => {
        this.user = response.data;
        console.log(this.user)
      })
      .catch((error) => {
        console.log(error);
      });
    },
}
</script>

<style>
body {
  background: rgb(99, 39, 120);
}

.form-control:focus {
  box-shadow: none;
  border-color: #ba68c8;
}

.profile-button {
  background: rgb(99, 39, 120);
  box-shadow: none;
  border: none;
}

.profile-button:hover {
  background: #682773;
}

.profile-button:focus {
  background: #682773;
  box-shadow: none;
}

.profile-button:active {
  background: #682773;
  box-shadow: none;
}

.back:hover {
  color: #682773;
  cursor: pointer;
}

.labels {
  font-size: 11px;
}

.add-experience:hover {
  background: #ba68c8;
  color: #fff;
  cursor: pointer;
  border: solid 1px #ba68c8;
}
</style>