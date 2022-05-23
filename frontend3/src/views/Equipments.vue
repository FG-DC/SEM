<template>
  <b-container class="mt-5 text-center">
    <v-snackbar v-model="toast.state">{{ toast.message }}</v-snackbar>
    <b-button variant="success" class="mb-5" @click="showModal('modalAdd', '')"
      >Add Equipment</b-button
    >
    <v-card>
      <v-card-title>
        Equipments
        <v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Search"
          single-line
          hide-details
        ></v-text-field>
      </v-card-title>
      <v-data-table
        :headers="headers"
        :items="equipments"
        :items-per-page="10"
        :search="search"
        class="elevation-1"
      >
        <template class="d-flex" v-slot:item.actions="{ item }">
          <b-button
            @click="showModal('modalRemove', item)"
            variant="danger"
            style="margin-right: 2%"
            ><span>Remove</span>
          </b-button>
          <b-button @click="showModal('modalEdit', item)" variant="primary"
            >Edit</b-button
          >
        </template></v-data-table
      >
    </v-card>

    <b-modal
      ref="modalRemove"
      centered
      id="modalRemove"
      @ok="deleteEquipment"
      title="Are you sure you want to delete?"
    >
      <span>Equipment: {{ equipment.name }}</span>
    </b-modal>

    <b-modal
      ref="modalEdit"
      centered
      id="modalEdit"
      @ok="editEquipment()"
      title="Edit equipment"
    >
      <div data-app />
      <div v-for="(item, key) in newEquipment" v-if="inputs.includes(key)">
        <span>{{ key.charAt(0).toUpperCase() + key.slice(1) + ":" }}</span>
        <v-text-field
        
          solo
          :label="key.charAt(0).toUpperCase() + key.slice(1) + ':'"
          v-if="key != 'division' && key != 'type'"
          v-model="newEquipment[key]"
        ></v-text-field>
        <v-select
          solo
          :label="key.charAt(0).toUpperCase() + key.slice(1) + ':'"
          v-if="key == 'division'"
          v-model="newEquipment[key]"
          :items="divisions"
          item-text="name"
          item-value="id"
        ></v-select>
        <v-select
          solo
          :label="key.charAt(0).toUpperCase() + key.slice(1) + ':'"
          v-if="key == 'type'"
          v-model="newEquipment[key]"
          :items="equipmentTypes"
          item-text="name"
          item-value="id"
        ></v-select>
      </div>
    </b-modal>

    <b-modal
      ref="modalAdd"
      centered
      id="modalAdd"
      @ok="addEquipment()"
      title="Add equipment"
    >
      <div data-app />
      <div v-for="(item, key) in newEquipment" v-if="inputs.includes(key)">
        <span>{{ key.charAt(0).toUpperCase() + key.slice(1) + ":"}}</span>
        <v-text-field
          v-if="key != 'division' && key != 'type'"
          v-model="newEquipment[key]"
          solo
        ></v-text-field>
        <v-select
          v-if="key == 'division'"
          v-model="newEquipment[key]"
          :items="divisions"
          item-text="name"
          item-value="id"
          label="Select"
          solo
        ></v-select>
        <v-select
          v-if="key == 'type'"
          v-model="newEquipment[key]"
          :items="equipmentTypes"
          item-text="name"
          item-value="id"
          label="Select"
          solo
        ></v-select>
      </div>
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
  },
  data() {
    return {
      inputs: [
        "name",
        "consumption",
        "activity",
        "standby",
        "type",
        "division",
      ],
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
          text: "Actions",
          value: "actions",
        },
      ],
      search: "",
      equipments: [],
      divisions: [],
      equipmentTypes: [],
      newEquipment: [],
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
  async created() {
    this.getEquipments();
    this.getDivisions();
    this.getEquipmentTypes();
  },
  methods: {
    showModal(modal, item) {
      if (item == "") {
        this.newEquipment = {
          name: "",
          consumption: "",
          activity: "",
          division_id: "",
          equipment_type_id: "",
          standby: "",
          type: "",
          division: "",
        };
      } else {
        this.newEquipment = { ...item };
        this.equipment = item;
      }
      this.$refs[modal].show();
      console.log(this.newEquipment);
    },
    getEquipments() {
      axios
        .get(`/users/${this.userId}/equipments`)
        .then((response) => {
          this.equipments = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getDivisions() {
      axios
        .get(`/users/${this.userId}/divisions`)
        .then((response) => {
          this.divisions = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getEquipmentTypes() {
      axios
        .get(`/equipment-types`)
        .then((response) => {
          this.equipmentTypes = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    editEquipment() {
      axios
        .put(`/users/${this.userId}/equipments/${this.newEquipment.id}`, {
          name: this.newEquipment.name,
          consumption: this.newEquipment.consumption,
          activity: this.newEquipment.activity,
          division_id: this.newEquipment.division,
          equipment_type_id: this.newEquipment.type,
          standby: this.newEquipment.standby,
        })
        .then(() => {
          this.getEquipments();
          this.toast.message = "Equipment was edited successfully";
          this.toast.state = true;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    deleteEquipment() {
      axios
        .delete(`/users/${this.userId}/equipments/${this.equipment.id}`)
        .then(() => {
          this.getEquipments();
          this.toast.message = `${this.equipment.name} was deleted successfully`;
          this.toast.state = true;
        })
        .catch((error) => {
          this.toast.message = "There was an error removing the device";
          this.toast.state = true;
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
          this.getEquipments();
          this.toast.message = "Equipment was added successfully";
          this.toast.state = true;
        })
        .catch((error) => {
          this.toast.message = "There was an error adding the device";
          this.toast.state = true;
        });
    },
  },
};
</script>

<style>
</style>
