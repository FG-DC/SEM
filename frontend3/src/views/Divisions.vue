<template>
  <b-container class="mt-5 text-center">
    <v-snackbar v-model="toast.state">{{ toast.message }}</v-snackbar>
    <v-card>
      <v-card-title>
        Divisions
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
        :items="divisions"
        :items-per-page="10"
        :search="search"
        class="elevation-1"
      >
        <template class="d-flex" v-slot:item.actions="{ item }">
          <b-button
            @click="showModal('modalDelete', item)"
            variant="danger"
            style="margin-right: 2%"
            ><span>Delete</span>
          </b-button>
          <b-button @click="showModal('modalEdit', item)" variant="primary"
            >Edit</b-button
          >
        </template></v-data-table
      >
    </v-card>

    <b-modal
      ref="modalDelete"
      centered
      id="modalDelete"
      @ok="deleteDivision"
      title="Are you sure you want to delete?"
    >
      <span>Division: {{ division.name }}</span>
    </b-modal>

    <b-modal
      ref="modalEdit"
      centered
      id="modalEdit"
      @ok="editDivision(newDivisionName)"
      title="Edit Division"
    >
      <span>Current name: {{ division.name }}</span>
      <v-text-field
        v-model="newDivisionName"
        label="Change division name"
        hide-details="auto"
        solo
      ></v-text-field>
    </b-modal>

    <b-modal id="modalEdit"></b-modal>
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
      headers: [
        { text: "ID", value: "id" },
        { text: "Name", value: "name" },
        { text: "Actions", value: "actions" },
      ],
      search: "",
      divisions: [],
      newDivisionName: "",
      division: { name: "", id: null },
      toast: {
        message: null,
        state: false,
      },
    };
  },
  async created() {
    await this.getDivisions();
  },
  methods: {
    showModal(modal, item) {
      this.newDivisionName = "";
      this.division = item;
      this.$refs[modal].show();
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
    editDivision() {
      axios
        .put(`/users/${this.userId}/divisions/${this.division.id}`, {
          name: this.newDivisionName,
        })
        .then(() => {
          this.getDivisions();
          this.toast.message = "Division was renamed successfully";
          this.toast.state = true;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    deleteDivision() {
      axios
        .delete(`/users/${this.userId}/divisions/${this.division.id}`)
        .then(() => {
          this.getDivisions();
          this.toast.message = `${this.division.name} was deleted successfully`;
          this.toast.state = true;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
<style>
</style>