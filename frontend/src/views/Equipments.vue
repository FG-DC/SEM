<template>
  <v-container class="mt-2">

    <v-snackbar v-model="toast.state">{{ toast.message }}</v-snackbar>

    <!-- MAIN CARD -->
    <v-card elevation="6" class="text-card p-4" style="border-radius: 10px">

      <!-- TITLE -->
      <div class="mb-3"><b style="color:#191645">Divisions</b></div>

      <!-- CREATE EQUIPMENT -->
      <b-button variant="success" @click="showModal('modalAdd', {})">
        Create Equipment
      </b-button>

      <!-- SEARCH -->
      <v-text-field
        v-model="search"
        append-icon="mdi-magnify"
        label="Search"
        single-line
        hide-details
      />

      <!-- DATA TABLE -->
      <v-data-table
        :headers="headers"
        :items="equipments"
        :items-per-page="10"
        :search="search"
      >
        <template class="d-flex" v-slot:item.actions="{ item }">
          <b-button variant="primary" style="margin: 2px" @click="showModal('modalEdit', item)">
            <font-awesome-icon icon="fa-solid fa-pen" />
          </b-button>
          
          <b-button variant="danger" style="margin: 2px" @click="showModal('modalRemove', item)">
            <font-awesome-icon icon="fa-solid fa-trash" size="lg" />
          </b-button>
        </template>
      </v-data-table>

      <!-- MODAL REMOVE EQUIPMENT -->
      <b-modal
        id="modalRemove"
        ref="modalRemove"
        :title="'You want to delete the ' + equipment.name + '?'"
        centered
        @ok="deleteEquipment"
      >
      </b-modal>

      <!-- MODAL EDIT EQUIPMENT -->
      <b-modal
        id="modalEdit"
        ref="modalEdit"
        :title="'Edit Equipment ' + equipment.name"
        centered
        @ok="editEquipment"
      >
        <div data-app />
        
        <!-- INPUT NAME -->
        <span>Name</span>
        <v-text-field
            v-model="equipment.name"
            solo
        />

        <!-- INPUT TYPE -->
        <span>Type</span>
        <v-select
          v-model="equipment.type"
          :items="equipmentTypes"
          item-text="name"
          item-value="id"
          solo
        />

        <!-- INPUT DIVISION -->
        <span>Division</span>
        <v-select
          v-model="equipment.division"
          :items="divisions"
          item-text="name"
          item-value="id"
          solo
        />

        <!-- INPUT CONSUMPTION -->
        <span>Consumption</span>
        <v-text-field
            v-model="equipment.consumption"
            type="number"
            solo
        />

        <!-- INPUT STANDBY -->
        <span>Standby</span>
        <v-text-field
            v-model="equipment.standby"
            type="number"
            solo
        />

        <!-- INPUT ACTIVITY -->
        <span>Activity</span>
        <v-select
          v-model="equipment.activity"
          :items="['Yes', 'No']"
          solo
        />

      </b-modal>

      <!-- MODAL CREATE EQUIPMENT -->
      <b-modal
        id="modalAdd"
        ref="modalAdd"
        title="Create Equipment"
        centered
        @ok="addEquipment"
      >
        <div data-app />

        <!-- INPUT NAME -->
        <span>Name</span>
        <v-text-field
            v-model="newEquipment.name"
            solo
        />

        <!-- INPUT TYPE -->
        <span>Type</span>
        <v-select
          v-model="newEquipment.type"
          :items="equipmentTypes"
          item-text="name"
          item-value="id"
          solo
        />

        <!-- INPUT DIVISION -->
        <span>Division</span>
        <v-select
          v-model="newEquipment.division"
          :items="divisions"
          item-text="name"
          item-value="id"
          solo
        />

        <!-- INPUT CONSUMPTION -->
        <span>Consumption</span>
        <v-text-field
            v-model="newEquipment.consumption"
            type="number"
            solo
        />

        <!-- INPUT STANDBY -->
        <span>Standby</span>
        <v-text-field
            v-model="newEquipment.standby"
            type="number"
            solo
        />

        <!-- INPUT ACTIVITY -->
        <span>Activity</span>
        <v-select
          v-model="newEquipment.activity"
          :items="['Yes', 'No']"
          solo
        />

      </b-modal>
    </v-card>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      headers: [
        {
          text: "Name",
          value: "name",
        },
        {
          text: "Consumption",
          value: "consumption",
        },
        {
          text: "Standby",
          value: "standby",
        },
        {
          text: "Division",
          value: "division_name",
        },
        {
          text: "Type",
          value: "type_name",
        },
        {
          text: "Activity",
          value: "activity",
        },
        {
          text: "",
          value: "actions",
          sortable: false,
        },
      ],
      search: "",

      equipments: [],
      divisions: [],
      equipmentTypes: [],
      newEquipment: {},
      equipment: {
        name: "",
        id: null,
      },
      toast: {
        message: null,
        state: false,
      },
    };
  },
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
    this.getDivisions();
    this.getEquipmentTypes();
  },
  methods: {
    showModal(modal, item) {
      this.newEquipment = {
        name: '',
        consumption: '',
        activity: '',
        division_id: '',
        equipment_type_id: '',
        standby: '',
        type: '',
        division: '',
      };
      
      this.equipment = { ...item };
      
      this.$refs[modal].show();
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
    getDivisions() {
      return axios
        .get(`/users/${this.userId}/divisions`)
        .then((response) => {
          this.divisions = response.data.data;
        })
        .catch((error) => {
          return Promise.reject(error);
        });
    },
    getEquipmentTypes() {
      return axios
        .get(`/equipment-types`)
        .then((response) => {
          this.equipmentTypes = response.data.data;
        })
        .catch((error) => {
          return Promise.reject(error);
        });
    },
    editEquipment() {
      axios
        .put(`/users/${this.userId}/equipments/${this.equipment.id}`, {
          name: this.equipment.name,
          consumption: this.equipment.consumption,
          activity: this.equipment.activity,
          division_id: this.equipment.division,
          equipment_type_id: this.equipment.type,
          standby: this.equipment.standby,
        })
        .then(() => {
          this.$socket.emit("equipmentUpdate",this.userId);
          this.showToastMessage('Equipment was edited successfully');
        })
        .catch((error) => {
          this.showToastMessage('There was an error editing the device');
          console.log(error);
        });
    },
    deleteEquipment() {
      axios
        .delete(`/users/${this.userId}/equipments/${this.equipment.id}`)
        .then(() => {
          this.$socket.emit("equipmentUpdate",this.userId);
          this.showToastMessage(`${this.equipment.name} was deleted successfully`);
        })
        .catch((error) => {
          this.showToastMessage('There was an error removing the device');
        });
    },
    addEquipment() {
      axios
        .post(`/users/${this.userId}/equipments`, {
          name: this.newEquipment.name,
          consumption: this.newEquipment.consumption,
          activity: this.newEquipment.activity,
          division_id: this.newEquipment.division,
          equipment_type_id: this.newEquipment.type,
          standby: this.newEquipment.standby,
        })
        .then(() => {
          this.$socket.emit("equipmentUpdate",this.userId);
          this.showToastMessage('Equipment was added successfully');
        })
        .catch((error) => {
          this.showToastMessage('There was an error adding the device');
        });
    },
    showToastMessage(message) {
      this.toast.message = message;
      this.toast.state = true;
    }
  },
};
</script>

<style>
</style>
