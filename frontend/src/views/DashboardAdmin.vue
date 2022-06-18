<template>
  <b-container class="text-center">
    <v-card>
      <div class="d-flex p-4">
        <v-text-field type="date" v-model="infoDate"></v-text-field>
        <b-button
          class="action-button"
          @click="getInfoFromDay()"
          variant="primary"
        >
          <font-awesome-icon icon="fa-solid fa-magnifying-glass" size="lg" />
        </b-button>
      </div>
    </v-card>
    <v-card class="mt-4">
      <apexchart width="380" :options="userChartOptions" :series="userSeries"
    /></v-card>

    <div class="d-flex flex-wrap mt-4">
      <v-card
        elevation="6"
        class="flex-grow-1 card-selectable mx-auto"
      >
        <div class="text-card text-center">
          <span>Observations</span>
          <br />
          {{ infoStatistics.observations }}
        </div>
      </v-card>
      <v-card
        elevation="6"
        class="flex-grow-1 card-selectable"
        style="border-radius: 10px; margin-left: 20px; padding-right: 2vw"
      >
        <div class="text-card text-center">
          <span>Alerts</span>
          <br />
          {{ infoStatistics.alerts }}
        </div>
      </v-card>
      <v-card
        elevation="6"
        class="flex-grow-1 card-selectable"
        style="border-radius: 10px; margin-left: 20px; padding-right: 2vw;"
      >
        <div class="text-card text-center">
          <span>Consumptions</span>
          <br />
          {{ infoStatistics.consumptions }}
        </div>
      </v-card>
    </div>
  </b-container>
</template>

<script>
import axios from "axios";
import Chart from "../components/Chart.vue";

export default {
  components: { Chart },

  data() {
    return {
      chartOptions: {},
      userChartOptions: {},
      userStatistics: {},
      infoStatistics: {},
      userSeries: [],
      labelSeries: [],
      infoDate: "",
    };
  },
  created() {
    this.getUsersStats();
    this.getInfoFromDay();
  },
  methods: {
    getUsersStats() {
      axios
        .get(`statistics/users`)
        .then((response) => {
          this.userStatistics = response.data;

          Object.entries(response.data).forEach((item) => {
            this.labelSeries.push(
              item[0].charAt(0).toUpperCase() + item[0].slice(1)
            );
            this.userSeries.push(item[1]);
          });

          this.userChartOptions = {
            chart: {
              width: 380,
              type: "pie",
            },
            labels: this.labelSeries,
          };
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getInfoFromDay() {
      let timestamp;
      if (this.infoDate == "") {
        timestamp = Math.floor(Date.now() / 1000);
      } else {
        timestamp = Date.parse(this.infoDate) / 1000;
      }
      this.filledValues = false;
      axios
        .get(`statistics?timestamp=${timestamp}`)
        .then((response) => {
          this.infoStatistics = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
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

.text-card{
   font-size: 2.5vw;
  color: #191645;
}

</style>