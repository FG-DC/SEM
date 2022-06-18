<template>
  <v-container>
    <v-snackbar
      style="margin-top: 5%"
      :color="toast.color"
      top
      v-model="toast.state"
      >{{ toast.message }}</v-snackbar
    >
    <!-- MAIN CARD -->
    <v-card elevation="6" class="text-card p-4" style="border-radius: 10px">
      <!-- TITLE -->
      <div class="mb-3"><b style="color: #191645">Users</b></div>

      <!-- CREATE -->
      <b-button variant="success">
        + Create
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
        :items="users"
        :items-per-page="10"
        :search="search"
      >
        <template class="d-flex" v-slot:item.actions="{ item }">
          <b-button variant="primary" style="margin: 2px">
            <font-awesome-icon icon="fa-solid fa-pen" />
          </b-button>

          <b-button variant="danger" style="margin: 2px">
            <font-awesome-icon icon="fa-solid fa-trash" size="lg" />
          </b-button>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>

<script>
import axios from "axios";
export default {
  created() {
    this.getUsers();
  },
  data() {
    return {
      headers: [
        {
          text: "Name",
          value: "name",
        },
        {
          text: "Email",
          value: "email",
        },
        {
          text: "Email",
          value: "email",
        },
        {
          text: "Birthdate",
          value: "birthdate",
        },
        {
          text: "Type",
          value: "type",
        },
      ],
      toast: {
        state: false,
        message: "",
        color: "",
      },
      search: "",
      users: [],
    };
  },
  methods: {
    getUsers() {
      return axios
        .get(`/users`)
        .then((response) => {
          this.users = response.data.data;
        })
        .catch((error) => {
          return Promise.reject(error);
        });
    },
  },
};
</script>

<style>
</style>