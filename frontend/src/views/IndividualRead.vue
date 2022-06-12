<template>
  <b-container class="container mt-2 text-center" style="user-select:none">
    <v-snackbar v-model="toast.state">{{ toast.message }}</v-snackbar>

    <!-- SELECT BOX -->
    <div class="d-flex" style="border-radius: 10px">
      <div class="flex-grow-1">
        <div data-app></div>
        <v-select
          :disabled="state"
          data-app
          v-model="selected"
          :items="equipments"
          item-text="name"
          label="Choose an equipment"
          return-object
          solo
        />
      </div>

      <!-- BUTTON START -->
      <b-button
        v-b-modal.modalAnalyse
        class="action-button"
        variant="primary"
        v-if="isSelected && step < 3"
      >
        <font-awesome-icon icon="fa-solid fa-play" size="lg" />
      </b-button>

      <!-- BUTTON STOP -->
      <b-button
        class="action-button"
        variant="danger"
        v-if="step >= 3"
        @click="stopAnalysis()"
      >
        <font-awesome-icon icon="fa-solid fa-stop" size="lg" />
      </b-button>
    </div>

    <!-- TIMER -->
    <v-card class="timer" style="border-radius: 10px">
      <span>{{ crono.time }}</span>
    </v-card>

    <!-- CHART LOADING... -->
    <v-progress-circular
      v-if="step == 4 && isCalibrating"
      :size="70"
      :width="7"
      color="purple"
      indeterminate
      class="mt-5"
    />
    <!-- CHART -->
    <Chart
      v-if="step == 4 && !isCalibrating"
      class="mt-5"
      :config="graphConfig"
      :isBoolean="false"
    />

    <v-data-table
      v-else
      class="mt-5"
      :headers="headers"
      :items="stats"
      :items-per-page="10"
    >
      <template v-slot:item.count="{ item }">
        <v-chip dark :color="item.count == 'Yes' ? '#f44336' : '#4caf50'">
          {{ item.count }}
        </v-chip>
      </template>
    </v-data-table>

    <b-modal
      ref="modalAnalyse"
      id="modalAnalyse"
      centered
      size="xl"
      :title="'Analysing the ' + selected.name"
    >
      <div class="text-justify lead">
        <v-stepper v-model="step" value="1">
          <v-stepper-header>
            <v-stepper-step step="1" :complete="step > 1">
              Information
            </v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="2" :complete="step > 2"
              >Ready?
            </v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="3" :complete="step > 3">
              Analysing
            </v-stepper-step>
          </v-stepper-header>

          <v-stepper-content class="mt-3" step="1">
            <span>
              This type of tool should be used to improve the accuracy of the
              system. The user must use this tool by following all the steps in
              the order they are presented, otherwise the monitoring system's
              conclusions will yield false results.
              <p>
                <b>
                  The process should be carried out from start to finish (1
                  minute)
                </b>
              </p>
              In case of any doubt, do not hesitate to contact us.
            </span>
          </v-stepper-content>

          <v-stepper-content class="mt-3" step="2">
            <span>
              <b>
                Please be sure that the {{ selected.name }} is turned OFF before
                starting the process.
              </b>
              <br />
              To carry out the consumption analysis process, please click on the
              button below.
            </span>
            <br />
            <b-button
              variant="danger"
              @click="startAnalyse()"
              class="mt-5 mb-2"
            >
              Start analysis
            </b-button>
          </v-stepper-content>

          <v-stepper-content class="mt-3" step="3">
            <span>
              <b>
                Please turn ON the equipment and only then press the button
                below!
              </b>
            </span>
            <br />
            <b-button variant="danger" class="mt-5 mb-2" @click="analyse()">
              The equipment is turned ON
            </b-button>
          </v-stepper-content>
        </v-stepper>
      </div>

      <template #modal-footer class="d-flex">
        <b-button @click="step--" v-if="step == 2"> Back </b-button>
        <b-button variant="primary" v-if="step == 1" @click="step++">
          Next
        </b-button>
      </template>
    </b-modal>
  </b-container>
</template>


<script>
import mqtt from "../MyMqtt";
import axios from "axios";
import Chart from "../components/Chart.vue";

export default {
  components: { Chart },
  data() {
    return {
      step: 1,
      state: false,

      equipments: [],
      selected: {},
      stats: [],

      consumptions: [],
      consumption: {},

      topics: [],

      graphConfig: {
        xAxis: [],
        yAxis: [],
      },
      crono: {
        timer: 0,
        interval: 0,
        time: "00:00",
      },
      toast: {
        message: null,
        state: false,
      },
      analysis: {
        start: null,
        end: null,
        equipment_id: null,
      },

      isCalibrating: false,
      timeout: 0,
      headers: [
        { text: "Equipment", value: "equipment_name" },
        { text: "Need to Analyze?", value: "count", sortable: false },
      ],
    };
  },
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
    isSelected() {
      return this.selected.name != null;
    },
    consumptionValue() {
      return this.consumption.value.toString();
    },
    consumptionTime() {
      if (!this.consumption.timestamp) return "";

      const timestamp = new Date(this.consumption.timestamp);

      return (
        timestamp.toLocaleDateString("pt") +
        " " +
        timestamp.toLocaleTimeString("pt-PT")
      );
    },
     trainingExamples() {
      return this.$store.getters.trainingExamples;
    },
  },
  watch: {
    trainingExamples() {
      this.getStats();
    },
  },
  async created() {

    //MQTT
    //-> Add topics to subscribe
    this.topics.push(this.userId + "/power");
    //-> Connect to MQTT Broker
    mqtt.connect(this.onMessage);
    await this.getStats();
    await axios
      .get(`/users/${this.userId}/equipments`)
      .then((response) => {
        this.equipments = response.data.data;
      })
      .catch((error) => {
        console.log(error);
      });
  },
  methods: {
    getColor(count) {
      if (count == "yes") return "green";
      else return "red";
    },
    onMessage(topic, message) {
      switch (topic) {
        case this.userId + "/power":
          this.consumption = {
            value: message,
            timestamp: new Date(),
          };
          this.loadChart(
            [this.consumptionValue, this.consumptionTime],
            "area",
            "Consumptions",
            false
          );
          break;
      }
    },
    loadChart(collection, type, label, isBoolean) {
      this.graphConfig.type = type;
      this.graphConfig.label = label;
      this.graphConfig.yAxis.push(collection[0]);
      this.graphConfig.xAxis.push(collection[1]);
    },
    startAnalyse() {
      this.step++;
      this.state = true;
      mqtt.publish(`${this.userId}/tare`, "tare");
    },
    analyse() {
      this.step++;
      this.isCalibrating = true;
      this.hideModal();
      this.crono.timer = 60;

      this.timeout = setTimeout(() => {
        mqtt.subscribe(this.topics);
        this.analysis.start = parseInt(new Date().getTime() / 1000);
        this.crono.interval = setInterval(this.countdown, 1000);

        this.isCalibrating = false;
      }, 5000);
    },
    countdown() {
      const minutes = Math.floor(this.crono.timer / 60);
      const seconds = this.crono.timer % 60;

      const minutesStr = minutes < 10 ? "0" + minutes : minutes;
      const secondsStr = seconds < 10 ? "0" + seconds : seconds;

      //Timer String
      this.crono.time = minutesStr + ":" + secondsStr;

      //Timer Number
      this.crono.timer--;

      if (this.crono.timer < 0) {
        this.saveAnalysis();
      }
    },
    stopAnalysis() {
      clearTimeout(this.timeout);
      clearInterval(this.crono.interval);

      mqtt.publish(`${this.userId}/reset`, "reset");

      this.step = 1;
      this.crono.time = "00:00";

      mqtt.unsubscribe(this.topics);

      this.graphConfig = {
        xAxis: [],
        yAxis: [],
      };

      this.state = false;
    },
    saveAnalysis() {
      this.analysis.equipment_id = this.selected.id;
      this.analysis.end = parseInt(new Date().getTime() / 1000);

      this.stopAnalysis();

      return axios
        .post(`/users/${this.userId}/training-examples`, {
          start: this.analysis.start,
          end: this.analysis.end,
          individual: true,
          equipments_on: [this.analysis.equipment_id],
        })
        .then((response) => {
          this.toast.state = true;
          this.toast.message = response.data.msg;
          this.$socket.emit("trainingExamples", this.userId);
        })
        .catch((error) => {
          console.log(error);
          return Promise.reject(error);
        });
    },
    getStats() {
      axios
        .get(`/users/${this.userId}/stats`)
        .then((response) => {
          console.log(response.data.training_examples);
          this.stats = response.data.training_examples.map((item) => {
            let x = item.count > 0 ? "No" : "Yes";
            return {
              ...item,
              count: x,
            };
          });
          console.log(this.stats)
        })
        .catch((error) => {
          console.log(error);
        });
    },
    hideModal() {
      this.$refs["modalAnalyse"].hide();
    },
  },
};
</script>

<style scoped>
.timer {
  font-size: 6vw;
}
.action-button {
  width: 50px;
  height: 50px;
  margin-left: 10px;
}
</style>