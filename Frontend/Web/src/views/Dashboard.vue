<template>
  <div class="container mt-5 text-center">
    <div class="mb-4 card-timestamp">
      <span> Last read at {{ currentDate }}</span>
    </div>
    <div class="d-flex flex-wrap">
      <el-col :span="8" :class="{ selected: currentChart == 1 }">
        <el-card
          @click="
            this.loadChart(
              'consumptions',
              consumptions,
              'area',
              'Consumption (W)',
              false
            )
          "
          style="cursor: pointer; min-width: fit-content"
          shadow="hover"
        >
          <span class="card-text" style="color: red">Consumption</span>
          <h3 class="mt-2">{{ currentWatts }}</h3>
        </el-card>
      </el-col>
      <el-col :span="8" :class="{ selected: currentChart == 2 }">
        <el-card
          @click="
            this.loadChart('activities', activities, 'area', 'Activity', true)
          "
          style="cursor: pointer; min-width: fit-content"
          shadow="hover"
        >
          <span class="card-text" style="color: orange">Activity</span>
          <h3 class="mt-2">{{ currentActivity }}</h3>
        </el-card>
      </el-col>
      <el-col :span="8" :class="{ selected: currentChart == 3 }">
        <el-card
          @click="
            this.loadChart(
              'equipments',
              equipments,
              'area',
              'Equipments',
              false
            )
          "
          style="cursor: pointer; min-width: fit-content"
          shadow="hover"
        >
          <span class="card-text" style="color: rgb(60, 60, 233)"
            >Equipments</span
          >
          <h3 class="mt-2">{{ currentEquipments }}</h3>
        </el-card>
      </el-col>
    </div>
    <hr class="mt-5" />
    <Chart
      v-if="loaded && currentChart == 1"
      :config="graphConfig"
      :isBoolean="false"
    ></Chart>
    <Chart
      v-if="loaded && currentChart == 2"
      :config="graphConfig"
      :isBoolean="true"
    ></Chart>
    <Chart
      v-if="loaded && currentChart == 3"
      :config="graphConfig"
      :isBoolean="false"
    ></Chart>
  </div>
</template>

<script>
import axios from "axios";
import Chart from "../components/Chart.vue";

export default {
  components: { Chart },
  data() {
    return {
      numEquipments: null,
      graphConfig: {},
      loaded: false,
      activities: [],
      consumptions: [],
      equipments: [],
      currentChart: null,
    };
  },
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
    currentWatts() {
      return this.consumptions.length == 0
        ? "-"
        : parseInt(this.consumptions[this.consumptions.length - 1][1]) + " W";
    },
    currentDate() {
      if (this.consumptions.length == 0) return "";

      return this.formatDate(
        this.consumptions[this.consumptions.length - 1][0],
        false
      );
    },
    currentEquipments() {
      return this.equipments.length == 0
        ? "-"
        : this.equipments[this.equipments.length - 1][1];
    },
    currentActivity() {
      return this.activities.length == 0
        ? "-"
        : this.activities[this.activities.length - 1][1];
    },
  },
  created() {
    this.$store.dispatch("fillStore");
    axios
      .get(`/users/${this.userId}/statistics/consumption`)
      .then((response) => {
        this.consumptions = response.data;
        this.loadChart(
          "consumptions",
          this.consumptions,
          "line",
          "Consumption (W)"
        );
      })
      .catch((error) => {
        console.log(error);
      });

    axios
      .get(`/users/${this.userId}/statistics/activity`)
      .then((response) => {
        this.activities = response.data;
      })
      .catch((error) => {
        console.log(error);
      });

    axios
      .get(`/users/${this.userId}/statistics/equipments`)
      .then((response) => {
        this.equipments = response.data;
      })
      .catch((error) => {
        console.log(error);
      });
  },
  methods: {
    loadChart(url, collection, type, label, isBoolean) {
      this.loaded = false;

      this.graphConfig.xAxis = [];
      this.graphConfig.yAxis = [];

      this.graphConfig.label = label;
      this.graphConfig.type = type;

      collection.forEach((item) => {
        this.graphConfig.xAxis.push(this.formatDate(item[0], true));
        this.graphConfig.yAxis.push(
          isBoolean ? (item[1] == "Yes" ? 1 : 0) : item[1]
        );
      });

      if (url == "consumptions") {
        this.currentChart = 1;
      } else if (url == "activities") {
        this.currentChart = 2;
      } else {
        this.currentChart = 3;
      }
      this.loaded = true;
    },
    formatDate(dateStr, withFullDate) {
      if (dateStr == null || dateStr == "") return "";

      let date = new Date(dateStr);
      let formatedDate = "";

      let day = date.getDate().toString();
      day = day.length == 1 ? "0" + day : day;
      let month = (date.getMonth() + 1).toString();
      month = month.length == 1 ? "0" + month : month;
      let year = date.getFullYear();

      formatedDate = day + "/" + month + "/" + year;

      if (!withFullDate) return formatedDate;

      let hours = date.getHours().toString();
      hours = hours.length == 1 ? "0" + hours : hours;
      let minutes = date.getMinutes().toString();
      minutes = minutes.length == 1 ? "0" + minutes : minutes;

      formatedDate += " " + hours + ":" + minutes;

      return formatedDate;
    },
  },
};
</script>

<style scoped>
h3 {
  font-size: 1.3rem;
}

.card-text {
  font-size: 1.3rem;
  font-family: "Trebuchet MS", Helvetica, sans-serif;
  font-weight: 500;
}

.card-timestamp {
  font-size: 2.5rem;
  font-weight: 500;
  font-family: Tahoma, Geneva, sans-serif;
  font-size: 23px;
  letter-spacing: 0px;
  word-spacing: 0px;
  color: black;
  font-weight: 400;
  font-family: "Trebuchet MS", Helvetica, sans-serif;
}

.selected {
  border: 2px solid #409eff;
  border-radius: 5px;
}
</style>