<template>
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
            name: "signin",
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
</style>