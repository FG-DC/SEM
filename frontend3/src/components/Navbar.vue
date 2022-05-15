<template>
  <div class="navbar d-flex">
    <div class="flex-grow-1">
      <div class="d-flex justify-content-center">
        <router-link to="/dashboard" class="space"><font-awesome-icon @click="$router.push({name: 'dashboard'})" :class="{selected: $route.name == 'dashboard'}" icon="fa-solid fa-house" size="2x"/></router-link>
        <router-link to="/read" class="space"><font-awesome-icon class="space" :class="{selected: $route.name == 'read'}" icon="fa-solid fa-magnifying-glass-chart" size="2x" /></router-link>
        <router-link to="/dashboard" class="space"><font-awesome-icon class="space" icon="fa-solid fa-bolt" size="2x" /></router-link>
        <router-link to="/settings" class="space"><font-awesome-icon @click="$router.push({name:'settings'})" class="space" :class="{selected: $route.name == 'settings'}" icon="fa-solid fa-gear" size="2x" /></router-link>
        <router-link to="/dashboard" class="space"><font-awesome-icon class="space" icon="fa-solid fa-user-group" size="2x" /></router-link>
      </div>
    </div>
    <b-dropdown variant="link" right no-caret>
      <template #button-content>
        <font-awesome-icon class="align-content-end" icon="fa-solid fa-circle-chevron-down" size="2x" style="color:white !important" />
      </template>
      <b-dropdown-item>Profile</b-dropdown-item>
      <b-dropdown-divider></b-dropdown-divider>
      <b-dropdown-item @click="logout()">Log Out</b-dropdown-item>
    </b-dropdown>
  </div>
<!--
  <el-menu
    :default-active="activeIndex2"
    class="el-menu-demo"
    mode="horizontal"
    background-color="#545c64"
    text-color="#fff"
    active-text-color="#ffd04b"
    @select="handleSelect"
  >
    <router-link to="dashboard" style="text-decoration: none">
      <el-menu-item index="1">Dashboard</el-menu-item>
    </router-link>
    <router-link to="profile" style="text-decoration: none">
      <el-menu-item index="2">Profile</el-menu-item>
    </router-link>
    <el-menu-item index="3" @click="this.logout()">Logout</el-menu-item>

    <el-menu-item index="4" @click="obsAnaliyse">Analyse</el-menu-item>
  </el-menu>

  <el-dialog v-model="modalState" width="80%">
    <span class="modalText"
      ><b
        >Are this the equipments that were turned ON on {{analyze.consumption.date}}
        </b
      ></span
    >
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="analyseEdit">Edit</el-button>
        <el-button type="primary" @click="dialogVisible = false"
          >Confirm</el-button
        >
      </span>
    </template>
    <br />
    <div
      class="row modalText"
      v-for="equipment in analyze.observation.equipments"
      :key="equipment.id"
    >
      <div class="col-7">
        {{ equipment.name }}
      </div>

      <div class="col">
        <button class="btn btn-success btn-sm" :disabled="!edit">ON</button>
      </div>
    </div>

    <div
      class="row modalText"
      v-for="equipment in this.equipments"
      :key="equipment.id"
    >
      <div class="col-7">
        {{ equipment.name }}
      </div>

      <div class="col">
        <button class="btn btn-danger btn-sm" :disabled="!edit">OFF</button>
      </div>
    </div>
  </el-dialog>
  -->
</template>

<script>
import axios from "axios";

export default {
  created() {
    this.$store.dispatch("fillStore");
  },
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
  },
  data() {
    return {
      modalState: false,
      analyze: null,
      edit: false,
      equipments: null,
    };
  },
  mounted() {},
  methods: {
    logout() {
      axios
        .post(`logout`)
        .then((response) => {
          this.$store.dispatch("logout");
          this.$router.push({
            name: "login",
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
    obsAnaliyse() {
      this.modalState = true;
      axios
        .get(`users/${this.userId}/observations/last`)
        .then((response) => {
          this.analyze = response.data;
          console.log(this.analyze);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    analyseEdit() {
      this.edit = true;
      axios
        .get(`users/${this.userId}/equipments`)
        .then((response) => {
          this.equipments = response.data.data;

          console.log(this.equipments)
     

          this.equipments.forEach((equipment) => {
            console.log(equipment.id)
            this.analyze.observation.equipments.forEach((analyzeEquip,index) => {
              if (equipment.id == analyzeEquip.id) {
                console.log("Index" + index)
                this.equipments.splice(index,1);
                console.log(this.equipments)
              }
            });
          });
          
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style scoped>
.modalText {
  font-size: 20px;
}

.navbar {
  width: 100%;
  background-color: #191645;
  height: 5%;
}

.space{
  margin-right:3%;
  color:white
}

.selected{
  color: #44c6ac;
  display: inline-block;
}
</style>