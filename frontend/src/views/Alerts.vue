<template>
  <b-container class="mt-2">
    <v-snackbar
      style="margin-top: 5%"
      :color="toast.color"
      top
      v-model="toast.state"
      >{{ toast.message }}</v-snackbar
    >
    <v-card elevation="6" class="text-card p-4" style="border-radius: 10px">
      <div class="mb-3 d-flex justify-content-between mb-4">
        <b style="color: #191645">Alerts</b>
        <v-chip
          class="ma-2"
          dark
          large
          :color="userNotifications == 'OFF' ? '#f44336' : '#4caf50'"
          @click="changeUserNotification()"
        >
          <span class="p-2">Notifications {{ userNotifications }}</span>
        </v-chip>
      </div>
      <v-data-table :headers="headers" :items="equipments" :items-per-page="10">
        <template class="d-flex" v-slot:item.state="{ item }">
          {{
            item.notify_when_passed != null
              ? item.notify_when_passed + " minutes"
              : ""
          }}
        </template>
        <template class="d-flex" v-slot:item.actions="{ item }">
          <b-button
            @click="showModal('modalAlert', item)"
            variant="primary"
            style="margin: 2px"
          >
            <font-awesome-icon icon="fa-solid fa-pen" />
          </b-button> </template
      ></v-data-table>
    </v-card>

    <b-modal ref="modalAlert" centered title="Edit email preferences">
      <h6>Equipment: {{ equipment.name }}</h6>
      <div class="d-flex justify-content-center">
        <v-chip
          class="m-3"
          dark
          :color="equipment.notify_when_passed > 0 ? '#4caf50' : '#f44336'"
          @click="changeState(equipment.notify_when_passed)"
        >
          {{ equipment.notify_when_passed > 0 ? "ON" : "OFF" }}
        </v-chip>
      </div>

      <div v-if="equipment.notify_when_passed" class="mt-3">
        <span>Notify after the equipment has been ON for:</span
        ><v-text-field
          type="number"
          placeholder="x minutes"
          v-model="notificationTime"
          solo
        ></v-text-field>
      </div>
      <template #modal-footer class="d-flex">
        <b-button
          @click="changeEquipmentNotifications()"
          :disabled="equipment.notify_when_passed && !notificationTime"
        >
          Save
        </b-button>
      </template>
    </b-modal>
  </b-container>
</template>

<script>
import axios from "axios";
export default {
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
    equipmentUpdate() {
      return this.$store.getters.equipmentUpdate;
    },
  },
  watch: {
    equipmentUpdate() {
      this.getEquipments();
    },
  },
  created() {
    this.getEquipments();
    this.getNotifications();
  },
  data() {
    return {
      toast: {
        state: false,
        message: "",
        color: "",
      },
      userNotifications: "",
      equipment: "",
      notificationTime: "",
      equipments: [],
      headers: [
        { text: "Name", value: "name" },
        { text: "Notify after", value: "state" },
        { text: "Alerts", value: "actions", sortable: false },
      ],
    };
  },
  methods: {
    getNotifications() {
      axios
        .get(`/users/${this.userId}/notifications`)
        .then((response) => {
          this.userNotifications = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    changeUserNotification() {
      axios
        .patch(`/users/${this.userId}/notifications`)
        .then((response) => {
          this.userNotifications = response.data;

          this.showToastMessage(
            "Notifications " + this.userNotifications,
            this.userNotifications == "ON" ? "#4caf50" : "#dd2929"
          );
        })
        .catch((error) => {
          this.showToastMessage(response.data.msg, "#333333");
        });
    },
    getEquipments() {
      return axios
        .get(`/users/${this.userId}/equipments`)
        .then((response) => {
          this.equipments = response.data.data;
        })
        .catch((error) => {
          return Promise.reject(error);
        });
    },
    changeEquipmentNotifications() {
      this.hideModal("modalAlert");
      let state = this.equipment.notify_when_passed
        ? this.notificationTime
        : null;
      axios
        .patch(`/users/${this.userId}/equipments/${this.equipment.id}`, {
          notify_when_passed: state,
        })
        .then((response) => {
          this.$socket.emit("equipmentUpdate", this.userId);
          this.showToastMessage(
            response.data.msg,
            state == null ? "#dd2929" : "#4caf50"
          );
        })
        .catch((error) => {
          this.showToastMessage(error, "#333333");
          return Promise.reject(error);
        });
    },
    showModal(modal, item) {
      this.equipment = { ...item };
      this.notificationTime = this.equipment.notify_when_passed
        ? this.equipment.notify_when_passed
        : "";
      this.$refs[modal].show();
    },
    hideModal(modal) {
      this.$refs[modal].hide();
    },
    changeState(state) {
      this.equipment.notify_when_passed = this.equipment.notify_when_passed
        ? false
        : true;
    },
    showToastMessage(message, color) {
      this.toast.color = color;
      this.toast.message = message;
      this.toast.state = true;
    },
  },
};
</script>

<style>
</style>