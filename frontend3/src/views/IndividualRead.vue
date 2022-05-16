<template>
  <b-container class="container mt-5 text-center">
    <v-snackbar v-model="toast.state">{{ toast.message }}</v-snackbar>
    <div data-app></div>
    <v-select
      :disabled="state"
      data-app
      v-model="selected"
      :items="equipments"
      item-text="name"
      label="Select"
      return-object
      solo
    ></v-select>

    <b-button
      style="margin-right: 3%"
      v-b-modal.modalAnalyse
      class="mr-3"
      variant="primary"
      :disabled="selected == -1 || state"
      >Analyse Equipment</b-button
    >

    <b-button variant="danger" :disabled="!state" @click="saveAnalysis()"
      >Stop Analysing</b-button
    >
    <br />
    <v-card class="mt-3 timer">
      <span>{{ crono.time }}</span>
    </v-card>

    <Chart class="mt-5" :config="graphConfig" :isBoolean="false" />

    <b-modal
      ref="modalAnalyse"
      id="modalAnalyse"
      hide-header-close
      no-close-on-backdrop
      no-close-on-esc
      centered
      size="lg"
      title="Analyze"
    >
      <div class="text-center">
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
          <v-stepper-content class="mt-3" step="1"
            ><span
              >This type of tool should be used to improve the accuracy of the
              system. The user must use this tool by following all the steps in
              the order they are presented, otherwise the monitoring system's
              conclusions will yield false results.
              <b
                >The process must be carried out from start to finish (about 30
                seconds per device or stopped by the user)</b
              >. In case of any doubt, do not hesitate to contact us.</span
            >
          </v-stepper-content>
          <v-stepper-content class="mt-3" step="2"
            ><span
              ><b
                >Please be sure that the equipment that you are analysing is
                turned OFF before starting the process.</b
              >
              <br />
              To carry out the consumption analysis process, please click on the
              button below.</span
            >
            <br />
            <b-button variant="danger" @click="startAnalyse()" class="mt-5 mb-2"
              >Start analyse</b-button
            >
          </v-stepper-content>
          <v-stepper-content class="mt-3" step="3"
            ><span
              ><b
                >Please turn on the equipment and only then press the button
                below!</b
              >
            </span>
            <br />
            <b-button variant="danger" class="mt-5 mb-2" @click="analyse()"
              >The equipment is turned on</b-button
            >
          </v-stepper-content>
        </v-stepper>
      </div>
      <template #modal-footer class="d-flex">
        <b-button @click="step--" v-if="step != 1 && step != 3"
          >Previous</b-button
        >
        <b-button variant="primary" v-if="step == 1" @click="step++"
          >Next</b-button
        >
        <b-button variant="danger" class="mr-2" @click="hideModal()"
          >Close</b-button
        >
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
      loaded: false,
      selected: -1,
      step: 1,
      state: false,
      equipments: null,
      currentdate: null,
      topics: [],
      consumptions: [],
      consumption: {
        value: "---",
        timestamp: "",
      },
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
    };
  },
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
    consumptionValue() {
      return this.consumption.value;
    },
    consumptionTime() {
      if (!this.consumption.timestamp) return "";

      const timestamp = new Date(this.consumption.timestamp);

      const hours = timestamp.getHours();
      const minutes = timestamp.getMinutes();

      return (
        timestamp.toLocaleDateString("pt") +
        " " +
        timestamp.toLocaleTimeString("pt-PT")
      );
    },
  },
  async created() {
    this.$store.dispatch("fillStore");
    //MQTT
    //-> Add topics to subscribe
    this.topics.push(this.userId + "/power");
    //-> Connect to MQTT Broker
    mqtt.connect(this.onMessage);
    mqtt.unsubscribe(this.topics)

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
    onMessage(topic, message) {
      switch (topic) {
        case this.userId + "/power":
          this.consumption = {
            value: message,
            timestamp: new Date(),
          };
          this.loadChart(
            [this.consumptionValue.toString(), this.consumptionTime],
            "area",
            "Consumptions",
            false
          );
          break;
      }
    },
    loadChart(collection, type, label, isBoolean) {
      this.loaded = false;
      this.graphConfig.type = type;
      this.graphConfig.label = label;
      this.graphConfig.xAxis.push(collection[1]);
      this.graphConfig.yAxis.push(collection[0]);
      if (this.graphConfig.xAxis.length > 50) {
        this.graphConfig.xAxis.shift();
        this.graphConfig.yAxis.shift();
      }
      this.loaded = true;
    },
    startAnalyse() {
      this.step++;
      this.state = true;
      mqtt.publish(`${this.userId}/tare`, "tare");
    },
    analyse() {
      mqtt.subscribe(this.topics);
      this.hideModal();
      this.crono.timer = 60;
      this.analysis.start = parseInt(new Date().getTime() / 1000);
      this.crono.interval = setInterval(this.countdown, 1000);
    },
    countdown() {
      const minutes = Math.floor(this.crono.timer / 60);
      this.crono.time = "0" + minutes + ":" + (this.crono.timer % 60);
      this.crono.timer--;
      if (this.crono.timer < 0) {
        this.saveAnalysis();
      }
    },
    saveAnalysis() {
      this.analysis.equipment_id = this.selected.id;
      this.analysis.end = parseInt(new Date().getTime() / 1000);
      mqtt.publish(`${this.userId}/reset`, "reset");

      this.step = 1;
      this.crono.time = "00:00";

      mqtt.unsubscribe(this.topics);

      clearInterval(this.crono.interval);
      this.graphConfig= {
        xAxis: [],
        yAxis: [],
      },

      axios
        .post(`/users/${this.userId}/training-examples`, {
          start: this.analysis.start,
          end: this.analysis.end,
          individual: true,
          equipments_on: [this.analysis.equipment_id],
        })
        .then((response) => {
          this.toast.state = true;
          this.toast.message = response.data.msg;
        })
        .catch((error) => {
          console.log(error);
        });
      this.state = false;
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
</style>