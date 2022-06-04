<template>
  <v-container class="text-center mt-2" style="color: black">
    <v-card
      @click="$router.push({ name: 'divisions' })"
      elevation="6"
      :class="{ card: get_started == 0 }"
      class="flex-grow-1 card-selectable"
      style="border-radius: 10px"
    >
      <b-container class="icons">
        <b-row no-gutters>
          <b-col cols="2">
            <font-awesome-icon
              class=""
              icon="fa-solid fa-house-chimney-window"
            />
          </b-col>
          <b-col>
            <span class="p-5">Divisions</span>
          </b-col>
          <b-col cols="2">
            <font-awesome-icon
              title="Divisions must be inserted"
              style="color: red"
              icon="fa-solid fa-triangle-exclamation"
              v-if="get_started == 0"
            />
          </b-col>
        </b-row>
      </b-container>
    </v-card>

    <v-card
      :disabled="get_started == 0"
      @click="$router.push({ name: 'equipments' })"
      elevation="6"
      :class="{ card: get_started == 1 }"
      class="flex-grow-1 mt-4 card-selectable"
      style="border-radius: 10px"
    >
      <b-container class="icons">
        <b-row no-gutters>
          <b-col cols="2">
            <font-awesome-icon icon="fa-solid fa-plug" />
          </b-col>
          <b-col> <span class="p-5">Equipments</span></b-col>
          <b-col cols="2">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <font-awesome-icon
                  title=""
                  style="color: red"
                  icon="fa-solid fa-triangle-exclamation"
                  v-if="get_started == 1"
                />
              </template> </v-tooltip
          ></b-col>
        </b-row>
      </b-container>
    </v-card>
    <v-card
      @click="$router.push({ name: 'affiliates' })"
      elevation="6"
      class="flex-grow-1 mt-4 card-selectable"
      style="border-radius: 10px"
    >
      <b-container class="icons">
        <b-row no-gutters>
          <b-col cols="2">
            <font-awesome-icon icon="fa-solid fa-user-group"
          /></b-col>
          <b-col><span class="p-5">Affiliates</span></b-col>
          <b-col cols="2"></b-col>
        </b-row>
      </b-container>
    </v-card>
  </v-container>
</template>

<script>
export default {
  computed: {
    get_started() {
      return this.$store.getters.get_started;
    },
  },
};
</script>
  
<style scoped>
.icons {
  color: #191645;
  font-size: 5.5vw;
}
.card-selectable {
  cursor: pointer;
  transition: transform 200ms;
}

.card-selectable:hover {
  transform: scale(1.02);
  z-index: 1;
}

.card {
  animation: shake 4.7s ease;
  animation-iteration-count: infinite;
}

@keyframes shake {
  0% {
    transform: translate(0, 0);
  }
  1.78571% {
    transform: translate(5px, 0);
  }
  3.57143% {
    transform: translate(0, 0);
  }
  5.35714% {
    transform: translate(5px, 0);
  }
  7.14286% {
    transform: translate(0, 0);
  }
  8.92857% {
    transform: translate(5px, 0);
  }
  10.71429% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(0, 0);
  }
}
</style>