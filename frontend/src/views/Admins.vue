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
      <div class="mb-3"><b style="color: #191645">Administrators</b></div>

      <!-- CREATE -->
      <b-button variant="success" @click="showModal('modalAdd', {})">
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
          <b-button
            variant="primary"
            style="margin: 2px"
            @click="showModal('modalEdit', item)"
          >
            <font-awesome-icon icon="fa-solid fa-pen" />
          </b-button>

          <b-button
            variant="danger"
            style="margin: 2px"
            @click="showModal('modalRemove', item)"
          >
            <font-awesome-icon icon="fa-solid fa-trash" size="lg" />
          </b-button>
        </template>
      </v-data-table>
    </v-card>

    <b-modal id="modalAdd" ref="modalAdd" title="Create Administrator" centered>
      <template #modal-footer class="d-flex">
        <b-button
          variant="primary"
          :disabled="validateCreate('create')"
          @click="addAdmin"
        >
          Create
        </b-button>
      </template>
      <div data-app />

      <!-- INPUT NAME -->
      <span>Name</span>
      <v-text-field
        label="Name"
        :rules="fieldRequired"
        v-model="user.name"
        solo
      />

      <!-- INPUT EMAIL -->
      <span class="p-3">Email</span>
      <v-text-field
        :rules="emailRules"
        label="Email"
        v-model="user.email"
        solo
      />

      <!-- INPUT BIRTHDATE -->
      <span>Birthdate</span>
      <v-text-field
        :rules="fieldRequired"
        label="Birthdate"
        v-model="user.birthdate"
        type="date"
        solo
      />

      <!-- INPUT PASSWORD -->
      <span>Password</span>
      <v-text-field
        :rules="fieldRequired"
        label="Password"
        v-model="user.password"
        type="password"
        solo
      />
    </b-modal>

    <b-modal id="modalEdit" ref="modalEdit" title="Edit Administrator" ok-only  centered>
      
      <div class="d-flex justify-content-center">
      <b-button variant="primary">Reset Password (NOT IMPLEMENTED)</b-button>
      </div>
    </b-modal>
      <b-modal
        id="modalRemove"
        ref="modalRemove"
        :title="'Do you want to delete user ' + user.name + '?'"
        centered
        @ok="deleteAdmin"
      >
      </b-modal>
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
          text: "",
          value: "actions",
          sortable: false,
        },
      ],
      user: {
        id:"",
        name: "",
        email: "",
        password: "",
        birthdate: "",
      },
      toast: {
        state: false,
        message: "",
        color: "",
      },
      fieldRequired: [(v) => !!v || "Field required"],
      emailRules: [
        (v) => !!v || "E-mail is required",
        (v) => /.+@.+/.test(v) || "E-mail must be valid",
      ],

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
    validateCreate(type) {
      let item = type == "create" ? this.user : this.user;
      if (
        item.name == "" ||
        item.email == "" ||
        item.birthdate == "" ||
        item.password == ""
      ) {
        return true;
      }
      return false;
    },

    showModal(modal, item) {
      this.user = {
        name: "",
        email: "",
        password: "",
        birthdate: "",
      };

      if (modal != "modalAdd") this.user = { ...item };
      this.$refs[modal].show();
    },
    hideModal(modal) {
      this.$refs[modal].hide();
    },
    addAdmin() {
      this.hideModal("modalAdd");

      axios
        .post(`/users`, {
          name: this.user.name,
          email: this.user.email,
          password: this.user.password,
          birthdate: this.user.birthdate.split("-").reverse().join("/"),
        })
        .then(() => {
          //this.$socket.emit("equipmentUpdate", this.userId);
          this.showToastMessage(
            "Administrator was created successfully",
            "#4caf50"
          );
        })
        .catch((error) => {
          console.log(error);
          this.showToastMessage(error, "#333333");
        });
    },
    deleteAdmin() {
      this.hideModal("modalRemove");
      axios
        .delete(`/users/${this.user.id}`)
        .then(() => {
          //this.$socket.emit("equipmentUpdate", this.userId);
          this.showToastMessage(
            `${this.user.name} was deleted successfully`,
            "#dd2929"
          );
        })
        .catch((error) => {
          this.showToastMessage(
            "There was an error removing the user",
            "#333333"
          );
        });
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