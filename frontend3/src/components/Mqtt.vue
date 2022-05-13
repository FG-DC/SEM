<script>
import mqtt from "mqtt";

export default {
  props: {
    topics: Array,
  },
  data() {
    return {
      message: "-",
      connection: {
        host: "e977e80565fb4a00ad2023273addb4b3.s1.eu.hivemq.cloud",
        port: 8884,
        endpoint: "/mqtt",
        clean: true, // Reserved session
        connectTimeout: 1000000, // Time out
        reconnectPeriod: 1000000, // Reconnection interval
        clientId: "clientId-wVkoTQLmp3",
        username: "SmartEnergyMonitoring",
        password: "Sem_12345678",
      },
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
    this.createConnection();
    this.doSubscribe();
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
        this.message = message.toString();
        this.$emit("updValue", topic, message.toString());
      });
    },
    doSubscribe() {
      this.topics.forEach((topic) => {
        this.client.subscribe(topic, 1, (error, res) => {
          if (error) {
            console.log("Subscribe to topics error", error);
            return;
          }
          this.subscribeSuccess = true;
          console.log("Subscribe to topics res", res);
        });
      });
    },
    doUnSubscribe() {
      this.client.unsubscribe(topic, (error) => {
        if (error) {
          console.log("Unsubscribe error", error);
        }
      });
    },
    doPublish() {
      this.topics.forEach((topic) => {
        this.client.publish(topic, payload, qos, (error) => {
          if (error) {
            console.log("Publish error", error);
          }
        });
      });
    },
  },
};
</script>

<style>
</style>