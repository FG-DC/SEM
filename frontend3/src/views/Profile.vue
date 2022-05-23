<template>
  <b-container class="text-center mb-5">
    <v-snackbar v-model="toast.state">{{ toast.message }}</v-snackbar>
    <v-card elevation="6" class="mt-5 mb-5 p-5">
      <label for="name">Name:</label>
      <v-text-field id="name" v-model="this.user.name" solo></v-text-field>
      <label for="birthdate">Birthdate:</label>
      <v-text-field
        id="birthdate"
        v-model="this.user.birthdate.split(' ')[0]"
        solo
      ></v-text-field>
      <label for="email">Email:</label>
      <v-text-field id="email" v-model="this.user.email" solo></v-text-field>
    </v-card>
    <b-button variant="danger" @click="showModal('modalChangePSW')"
      >Change Password</b-button
    >

    <b-modal
      ref="modalChangePSW"
      centered
      id="modalChangePSW"
      @ok="changePSW()"
      title="Change Password"
    >
      <label for="oldPSW">Old Password:</label>
      <v-text-field
        id="oldPSW"
        v-model="oldPSW"
        hide-details="auto"
        solo
        type="password"
      ></v-text-field>
      <br />
      <label for="newPSW">New Password:</label>
      <v-text-field
        id="newPSW"
        v-model="newPSW"
        hide-details="auto"
        solo
        type="password"
      ></v-text-field>
    </b-modal>
  </b-container>
</template>

<script>
import axios from "axios";
import EquipmentList from "../components/EquipmentList.vue";

export default {
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
  },
  components: {
    EquipmentList,
  },
  data() {
    return {
      oldPSW: "",
      newPSW: "",
      user: {
        name: "",
        email: "",
        birthdate: "",
      },
      toast: {
        message: null,
        state: false,
      },
    };
  },
  async created() {
    await axios
      .get(`/user`)
      .then((response) => {
        this.user = response.data;
      })
      .catch((error) => {
        console.log(error);
      });
  },
  methods: {
    changePSW() {
      axios
        .patch(`/users/${this.userId}/password`, {
          oldPassword: this.oldPSW,
          newPassword: this.newPSW,
        })
        .then(() => {
          this.toast.message = "Password has been changed";
          this.toast.state = true;
          this.newPSW = "";
          this.oldPSW = "";
        })
        .catch((error) => {
          this.toast.message = "There has been an error changing the password";
          this.toast.state = true;
          this.newPSW = "";
          this.oldPSW = "";
        });
    },
    showModal(modal) {
      this.$refs[modal].show();
    },
  },
};
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