<template>
  <b-container class="mt-2">
    <v-snackbar v-model="toast.state">{{ toast.message }}</v-snackbar>
    
    <!-- MAIN CARD -->
    <v-card elevation="6" class="p-4" style="border-radius: 10px">

      <!-- TITLE -->
      <div class="text-card mb-3"><b style="color:#191645">Profile</b></div>

      <label for="name">Name</label>
      <v-text-field 
        id="name" 
        v-model="user.name" 
        :disabled="!isEditing" 
        solo 
      />

      <label for="email">Email</label>
      <v-text-field 
        id="email" 
        v-model="user.email" 
        :disabled="true" 
        solo 
      />
      
      <label for="birthdate">Birthdate</label>
      <v-text-field
        id="birthdate"
        v-model="user.birthdate"
        :disabled="!isEditing"
        type="date"
        solo
      />

      <label for="price">Energy Price per kWh</label>
      <v-text-field 
        id="price" 
        v-model.number="user.energy_price"
        :disabled="!isEditing"
        type="number" 
        suffix="€" 
        solo 
      />

      <div class="d-flex justify-content-between">
        <b-button variant="danger" @click="showModal('modalChangePSW')">
          Change Password
        </b-button>

        <div>
          <b-button v-if="!isEditing" variant="primary" @click="startEdit">
            <font-awesome-icon icon="fa-solid fa-pen" />
          </b-button>
          <div v-else>
            <b-button variant="primary" style="margin-right: 10px" @click="editUser">
              <font-awesome-icon icon="fa-solid fa-floppy-disk" />
            </b-button>
            <b-button variant="danger" @click="cancelEdit">
              <font-awesome-icon icon="fa-solid fa-ban" />
            </b-button>
          </div>
        </div>
      </div>
    </v-card>

    <!-- MODAL CHANGE PASSWORD -->
    <b-modal
      id="modalChangePSW"
      ref="modalChangePSW"
      title="Change Password"
      centered
      @ok="editPassword"
    >
      <label for="oldPSW">Current Password</label>
      <v-text-field
        v-model="oldPSW"
        id="oldPSW"
        type="password"
        hide-details="auto"
        solo
      />
      <br>
      <label for="newPSW">New Password</label>
      <v-text-field
        v-model="newPSW"
        id="newPSW"
        type="password"
        hide-details="auto"
        solo
      />
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
      user: {},
      userClone: {},
      toast: {
        message: null,
        state: false,
      },
      isEditing: false,
    };
  },
  created() {
    return axios
      .get(`/user`)
      .then((response) => {
        this.user = response.data;
        this.user.birthdate = this.user.birthdate.split(' ')[0];
      })
      .catch((error) => {
        return Promise.reject(error);
      });
  },
  methods: {
    editPassword() {
      return axios
        .patch(`/users/${this.userId}/password`, {
          oldPassword: this.oldPSW,
          newPassword: this.newPSW,
        })
        .then(() => {
          this.showToastMessage("Password has been changed");
        })
        .catch((error) => {
          this.showToastMessage("There has been an error changing the password");
        });
    },
    editUser() {
      let date = new Date(this.user.birthdate)
      let formatedData = date.toLocaleDateString('pt', { timeZone: 'Europe/Lisbon' });

      return axios
        .put(`/users/${this.userId}`, {...this.user, birthdate: formatedData})
        .then((response) => {
          this.showToastMessage("User edited with success");
          this.user = response.data.data;
          this.user.birthdate = this.user.birthdate.split('/').reverse().join('-');
          this.isEditing = false;
        })
        .catch((error) => {
          this.showToastMessage("There has been an error editing the user");
          this.user = this.userClone;
          this.isEditing = false;
        });
    },
    cancelEdit() {
      this.user = this.userClone;
      this.isEditing = false;
    },
    startEdit() {
      this.isEditing = true;
      this.userClone = {...this.user};
    },
    showModal(modal) {
      this.oldPSW = "";
      this.newPSW = "";
      this.$refs[modal].show();
    },
    showToastMessage(message) {
      this.toast.message = message;
      this.toast.state = true;
    },
  },
};
</script>

<style scoped>
.text-card {
  color: #191645;
  font-size: 2.5rem;
}
</style>