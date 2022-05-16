<template>
  <div class="container mt-5 text-center">
    <div class="d-flex flex-wrap">
      <v-card elevation="6" class="flex-grow-1 card-selectable" style="border-radius: 10px;" @click="showModal(0)">
        <div class="text-card">
          <span>{{consumptionValue}}</span>
          <span style="font-size:3vw">W</span>
        </div>
        <div class="text-footer">{{consumptionTime}}</div>
      </v-card>
      <!--
      <v-card elevation="6" class="flex-grow-1" style="border-radius: 10px">
        <div class="text-card">
          <span>15,53</span>
          <span style="font-size:3vw">€</span>
        </div>
        <div class="text-footer">April</div>
      </v-card>
      -->
    </div>
    <v-card class="mt-5 card-selectable" elevation="6" style="border-radius: 10px;" >
      <div class="text-card">
        <font-awesome-icon icon="fa-solid fa-location-dot" style="margin-right: 2vw;" />
        <span>{{divisionValue}}</span>
      </div>
      <div class="text-footer">{{divisionTime}}</div>
    </v-card>
    <v-card class="mt-5 card-selectable" elevation="6" style="border-radius: 10px" @click="showModal(2)">
      <div class="text-card">
        <font-awesome-icon icon="fa-solid fa-plug-circle-bolt" style="margin-right: 2vw;" />
        <span>{{equipmentValue}}</span>
      </div>
      <div class="text-footer">{{equipmentTime}}</div>
    </v-card>

    <!-- MODAL -->
    <b-modal ref="graph-modal" hide-footer centered size="xl">
      <template #modal-title>
        {{modalTitle}}
      </template>

      <!-- CHART -->
      <apexchart
        v-if="cardClicked != null"
        width="100%"
        height="auto"
        type="area"
        :options="chartOptions"
        :series="chartSeries"
      />

    </b-modal>
  </div>
</template>

<script>
import axios from "axios";
import mqtt from "../MyMqtt";

export default {
  data() {
    return {
      //FULL DATA
      consumptions: [],
      divisions: [],
      equipments: [],

      //LAST DATA
      consumption: {
        value: 0,
        timestamp: ""
      },
      division: {
        value: "",
        timestamp: ""
      },
      equipment: {
        value: {},
        timestamp: "" 
      },

      chartOptions: {},
      chartSeries: [],

      cardClicked: null, //0 = Consumption, 1 = Expected Division, 2 = Equipments ON
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

      return this.formatDate(this.consumption.timestamp, true);
    },
    divisionValue() {
      return this.division.value;
    },
    divisionTime() {
      if (!this.division.timestamp) return "";

      return this.formatDate(this.division.timestamp, true);
    },
    equipmentValue() {
      return this.equipment.value.length;
    },
    equipmentTime() {
      if (!this.equipment.timestamp) return "";

      return this.formatDate(this.equipment.timestamp, true);
    },
    modalTitle() {
      if (this.cardClicked == null) return "";
      if (this.cardClicked === 0) return "Consumption";
      if (this.cardClicked === 1) return "Expected Division";
      if (this.cardClicked === 2) return "Equipments Activity";
    }
  },
  created() {
    //MQTT
    //-> Connect to the MQTT Broker
    mqtt.connect(this.onMessage);
    //-> Subscribe to topics
    mqtt.subscribe([this.userId + "/power", this.userId + "/observation"]);

    this.$store.dispatch("fillStore");

    this.getLastNConsumptions(12);
    this.getLastNObservations(12);
  },
  methods: {
    onMessage(topic, message) {
      switch(topic) {
        //TOPIC: #/POWER
        case(this.userId + "/power"):
          this.consumption = {
            value: message,
            timestamp: new Date()
          }
          this.addToArray(this.consumptions, this.consumption);
          break;

        //TOPIC: #/OBSERVATION
        case(this.userId + "/observation"):
          this.getLastNObservations(1);
          break;
      }

      this.loadChart(this.cardClicked == 0 ? this.consumptions : this.equipments);
    },
    initChart() {
      this.chartOptions = {
        chart: {
          id: "chart",
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: [], // TIMESTAMP VALUES
          labels: {
            show: false
          },
        },
      }
      this.chartSeries = [];
    },
    loadChart(collection) {
      this.initChart();

      //LOAD TIMESTAMPS
      this.chartOptions.xaxis.categories = collection.map((item) => {
        return this.formatDate(item.timestamp, true);
      });

      //LOAD SERIES
      //-> 1 Serie
      let array = collection.map((item) => {
        return item.value;
      })

      if (!Array.isArray(collection[0].value)) {
        this.chartSeries.push({
          name: "",
          data: array
        });
        return;
      }

      //-> N Series
      const mapIdToName = this.getEquipmentsHashMap();
      const mapIdToSerie = new Map();
      mapIdToName.forEach((value, key) => {
        const serie = {
          name: value,
          data: []
        };
        this.chartSeries.push(serie);
        mapIdToSerie.set(key, serie);
      });
      collection.forEach((item) => {
        const visited = []
        item.value.forEach((equipment) => {
          const serie = mapIdToSerie.get(equipment.id);
          serie.data.push(equipment.value == 0 ? null : equipment.value);

          visited.push(equipment.id);
        })
        for (let key in mapIdToSerie.keys()) {
          if (!visited.includes(key)) {
            const serie = mapIdToSerie.get(key);
            serie.data.push(null);
          }
        }
      });
    },
    formatDate(dateStr, withFullDate) {
      if (dateStr == null || dateStr == "")
        return "";

      let date = new Date(dateStr);
      let formatedDate = "";

      formatedDate = date.toLocaleDateString('pt');

      if (!withFullDate)
        return formatedDate;
      
      return formatedDate + " " + date.toLocaleTimeString('pt-PT');
    },
    addToArray(array, obj) {
      const MAX_ITEMS_ON_ARRAY = 50;

      if (array.length >= MAX_ITEMS_ON_ARRAY) {
        array.shift();
      }
      array.push(obj);
    },
    getLastNConsumptions(limit) {
      axios
      .get(`/users/${this.userId}/consumptions?limit=${limit}`)
      .then((response) => {
        let data = response.data.data;
        console.log(data)
        data.forEach((consumption) => {
          this.consumption = {
            value: consumption.value,
            timestamp: new Date(consumption.timestamp)
          }
          this.addToArray(this.consumptions, this.consumption);
        })
      })
      .catch((error) => {
        console.log(error);
      });
    },
    getLastNObservations(limit) {
      axios
      .get(`/users/${this.userId}/observations?limit=${limit}`)
      .then((response) => {
        let dataArray = response.data;

        dataArray.forEach((data) => {
          let obs = data.observation;

          let timestamp = new Date(obs.created_at);

          this.division = {
            value: obs.expected_division,
            timestamp: timestamp
          }
          this.addToArray(this.divisions, this.division);

          this.equipment = {
            value: [],
            timestamp: timestamp
          };

          obs.equipments.forEach((item) => {
            this.equipment.value.push({
              id: item.id,
              name: item.name,
              value: item.consumption
            });
          });

          this.addToArray(this.equipments, this.equipment);
        })
      })
      .catch((error) => {
        console.log(error);
      });
    },
    showModal(cardNumber) {
      this.cardClicked = cardNumber;

      switch (cardNumber) {
        case 0:
          if (this.consumptions.length == 0)
            return;
          
          this.loadChart(this.consumptions)
          break;
        case 2:
          if (this.equipments.length == 0)
            return;
          
          this.loadChart(this.equipments)
          break;
      }
      this.$refs['graph-modal'].show();
    },
    getEquipmentsHashMap() {
      const map = new Map();

      this.equipments.forEach((list) => {
        list.value.forEach((item) => {
          map.set(item.id, item.name);
        });
      });

      return map;
    },
    
  },
};
</script>

<style scoped>
.text-card {
  font-size: 5.5vw;
  color:#191645
}

.text-footer {
  color: grey;
  text-align: left;
  margin-left: 2vw;
  padding-bottom: 1vh; 
  font-size: 16px;
}

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

.card-selectable {
  cursor: pointer;
  transition: transform 200ms;
}

.card-selectable:hover {
  transform: scale(1.02);
}
</style>