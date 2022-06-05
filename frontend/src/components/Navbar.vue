<template>
  <v-bottom-navigation height="" :background-color="'#191645'">
    <router-link
      :to="{ name: 'dashboard' }"
      class="m-4 notification"
      v-if="this.get_started >= 5"
    >
      <font-awesome-icon
        class="notSelected"
        :class="{ selected: $route.name == 'dashboard' }"
        icon="fa-solid fa-house"
        size="2x"
      />
      <div class="badge">3</div>
    </router-link>

    <router-link
      :to="{ name: 'read' }"
      class="m-4 notification"
      v-if="this.get_started >= 3"
    >
      <font-awesome-icon
        class="notSelected"
        :class="{ selected: $route.name == 'read' }"
        icon="fa-solid fa-magnifying-glass-chart"
        size="2x"
      />
      <div class="badge">3</div>
    </router-link>

    <router-link
      :to="{ name: 'settings' }"
      class="m-4 notification"
      v-if="this.get_started >= 0"
    >
      <font-awesome-icon
        @click="$router.push({ name: 'settings' })"
        class="notSelected"
        :class="{ selected: $route.path.startsWith('/settings') }"
        icon="fa-solid fa-gear"
        size="2x"
      />
      <div class="badge">!</div>
    </router-link>
    <div class="m-3">
      <b-dropdown variant="link" right no-caret>
        <template #button-content>
          <font-awesome-icon
            icon="fa-solid fa-circle-chevron-down"
            size="2x"
            style="color: white !important"
          />
        </template>

        <b-dropdown-item :to="{ name: 'profile' }">Profile</b-dropdown-item>

        <b-dropdown-divider></b-dropdown-divider>

        <b-dropdown-item @click="logout()">Log Out</b-dropdown-item>
      </b-dropdown>
    </div>
  </v-bottom-navigation>
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
    get_started() {
      return this.$store.getters.get_started;
    },
    navbarUpdate() {
      return this.$store.getters.navbarUpdate;
    },
  },
  watch: {
    navbarUpdate(params) {
      switch (params[0]) {
        case "divisionUpdate":
        case "equipmentUpdate":
        case "affiliateUpdate":
        case "profileUpdate":
      }
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
  created() {},
  methods: {
    logout() {
      axios
        .post(`logout`)
        .then(() => {
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

          console.log(this.equipments);

          this.equipments.forEach((equipment) => {
            console.log(equipment.id);
            this.analyze.observation.equipments.forEach(
              (analyzeEquip, index) => {
                if (equipment.id == analyzeEquip.id) {
                  console.log("Index" + index);
                  this.equipments.splice(index, 1);
                  console.log(this.equipments);
                }
              }
            );
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

.selected {
  color: #44c6ac !important;
}

.notSelected {
  color: white;
}

.notification {
  color: white;
  text-decoration: none;
  position: relative;
  display: inline-block;
  border-radius: 2px;
}

.notification .badge {
  position: absolute;
  top: -11px;
  left: 35px;
  border-radius: 50%;
  background: red;
  color: white;
}
</style>
