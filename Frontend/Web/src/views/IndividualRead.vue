<template>
  <div class="container mt-5">
    <div class="d-flex w-100 mb-4">
      <div class="flex-grow-1">
        <select
          class="form-select selectpicker"
          required
          v-model="this.selected"
        >
          <option disabled selected value="-1">Choose a equipment</option>
          <option
            v-for="(equipment, index) in equipments"
            :key="index"
            :value="equipment"
          >
            {{ equipment.name }}
          </option>
        </select>
      </div>
      <q-btn color="primary" label="Record" type="button" class="btn btn-danger" @click="doSubscribe()" />
    </div>
    <Chart v-if="loaded" :config="graphConfig" :isBoolean="false" />
  </div>
</template>


<script>
import mqtt from "mqtt";
import axios from "axios";
import Chart from "../components/Chart.vue";
import { useQuasar } from 'quasar'

export default {
  components: { Chart },
  data() {
    return {
      currentdate: null,
      consumptions: [],
      graphConfig: {
        xAxis: [],
        yAxis: [],
      },
      loaded: false,
      collection: [],
      equipments: [],
      selected: -1,
      connection: {
        host: "e977e80565fb4a00ad2023273addb4b3.s1.eu.hivemq.cloud",
        port: 8884,
        endpoint: "/mqtt",
        clean: true, // Reserved session
        connectTimeout: 1000000, // Time out
        reconnectPeriod: 1000000, // Reconnection interval
        // Certification Information
        clientId: "clientId-wVkoTQLmp3",
        username: "SmartEnergyMonitoring",
        password: "Sem_12345678",
      },
      subscription: {
        topic: "ola",
        qos: 0,
      },
      publish: {
        topic: "ola",
        qos: 0,
        payload: '{ "msg": "Hello, I am browser." }',
      },
      receiveNews: "",
      qosList: [
        { label: 0, value: 0 },
        { label: 1, value: 1 },
        { label: 2, value: 2 },
      ],
      client: {
        connected: false,
      },
      subscribeSuccess: false,
    };
  },
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
  },
  async created() {
    this.$store.dispatch("fillStore");

    this.createConnection();
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
    createConnection() {
      // ws unencrypted WebSocket connection
      // wss encrypted WebSocket connection
      const { host, port, endpoint, ...options } = this.connection;
      const connectUrl = `wss://${host}:${port}${endpoint}`;
      try {
        this.client = mqtt.connect(connectUrl, options);
      } catch (error) {
        console.log("mqtt.connect error", error);
      }
      this.client.on("connect", () => {
        console.log("Connection succeeded!");
      });
      this.client.on("error", (error) => {
        console.log("Connection failed", error);
      });
      this.client.on("message", (topic, message) => {
        this.receiveNews = this.receiveNews.concat(message);
        var current = new Date();
        //console.log(current.toLocaleTimeString())

        this.collection.push([
          current.toLocaleTimeString(),
          message.toString(),
        ]);
        console.log(this.collection);
        this.loadChart(this.collection, "line", "Consumption (W)", false);
      });
    },
    doSubscribe() {
      const { topic, qos } = this.subscription;
      console.log(this.userId + "/power");
      this.client.subscribe(
        this.userId+ "/power",
        { qos },
        (error, res) => {
          if (error) {
            console.log("Subscribe to topics error", error);
            return;
          }
          this.subscribeSuccess = true;
          console.log("Subscribe to topics res", res);
        }
      );
    },
    doUnSubscribe() {
      const { topic } = this.subscription;
      this.client.unsubscribe(topic, (error) => {
        if (error) {
          console.log("Unsubscribe error", error);
        }
      });
    },
    doPublish() {
      const { topic, qos, payload } = this.publication;
      this.client.publish(topic, payload, qos, (error) => {
        if (error) {
          console.log("Publish error", error);
        }
      });
    },
    loadChart(collection, type, label, isBoolean) {
      this.loaded = false;

      this.graphConfig.label = label;
      this.graphConfig.type = type;

        this.graphConfig.xAxis.push(collection[collection.length-1][0]);
        this.graphConfig.yAxis.push(collection[collection.length-1][1]);
      console.log(this.graphConfig);
      this.loaded = true;
    },
  },
};
</script>

<style>
</style>