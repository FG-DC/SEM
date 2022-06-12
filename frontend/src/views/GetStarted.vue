<template>
  <div class="container mt-5 text-center">
    <b-tabs content-class="mt-3" fill>
      <!----------------------------------------------------------- Question 1 ------------------------------------------------------>
      <b-tab title="First" active>
        <div v-if="active == 0">
      <h3 class="mt-5">{{ this.getStarted[active].question }}</h3>
          <input
            required
            type="text"
            class="form-control"
            :class="{
              'is-valid': !step1DivisionError,
              'is-invalid': step1DivisionError,
            }"
            v-model="division"
            placeholder="Ex: Kitchen"
          />
          <div class="invalid">{{ step1DivisionError }}</div>
          <button class="btn btn-success mt-2" @click="pushDivision()">
            Add division
          </button>
          <div
            v-if="getStarted[active].answer.length > 0"
            class="card w-100 mt-5"
          >
            <div class="card-body">
              <button
                v-for="(division, index) in getStarted[active].answer"
                :key="division.id"
                class="btn btn-primary"
                style="margin-right: 10px"
              >
                {{ division.name }}
                <i
                  @click="deleteDivision(division.id, index)"
                  class="bi bi-x-circle"
                ></i>
              </button>
            </div>
          </div>
        </div>
      </b-tab>
      <b-tab title="Second">
        <!----------------------------------------------------------- Question 2 -------------------------------------------------------->
        <div v-if="active == 1">
          <div class="row">
            <div class="col-sm mb-3">
              <select
                class="form-select selectpicker"
                required
                v-model="equipment.selectedName"
              >
                <option disabled selected value="-1">Choose a equipment</option>
                <option v-for="(equipment, index) in equipments" :key="index">
                  {{ equipment }}
                </option>
              </select>

              <span class="invalid">{{ step2EquipmentNameError }}</span>
            </div>
            <div class="col-sm mb-3">
              <input
                required
                min="0.01"
                type="number"
                class="form-control"
                :class="{
                  'is-valid': !step2EquipmentConsumptionError,
                }"
                placeholder="Consumption"
                v-model.number="equipment.consumption"
              />
              <span class="invalid">{{ step2EquipmentConsumptionError }}</span>
            </div>
          </div>
          <div class="align-center d-flex align-items-center">
            <input
              type="text"
              class="form-control mb-3"
              :class="{
                'is-valid': !step2EquipmentNameError && equipment.name != '',
              }"
              v-model="equipment.name"
              placeholder="Equipment"
              v-if="equipment.selectedName == 'Other...'"
              required
            />
          </div>
          <div class="row">
            <div class="col-sm mb-3">
              <select class="form-select" required v-model="equipment.category">
                <option disabled selected value="-1">Choose a category</option>
                <option v-for="category in categories" :key="category.id">
                  {{ category.name }}
                </option>
              </select>
              <span class="invalid">{{ step2EquipmentCategoryError }}</span>
            </div>
            <div class="col-sm mb-3">
              <select class="form-select" v-model="equipment.division" required>
                <option disabled selected value="-1">Choose a division</option>
                <option
                  v-for="division in getStarted[0].answer"
                  :key="division"
                >
                  {{ division.name }}
                </option>
              </select>
              <span class="invalid">{{ step2EquipmentDivisionError }}</span>
            </div>
          </div>
          <button
            type="button"
            class="btn btn-success"
            @click="pushEquipment()"
          >
            Add equipment
          </button>

          <EquipmentList :data="getStarted[active].answer" />
        </div>
      </b-tab>
      <b-tab title="Very, very long title">
        <!----------------------------------------------------------- Question 3 ---------------------------------------------------------->

        <div class="mt-5" v-if="active == 2">
          <h4 class="mt-3">On Work Days:</h4>
          <div class="d-flex flex-wrap justify-content-around">
            <div class="col-5">
              <span>Get up hour</span>
              <input
                class="form-control mb-3"
                type="time"
                v-model="getStarted[active].answer.work.start"
              />
            </div>
            <div class="col-5">
              <span>Bedtime hour</span>
              <input
                class="form-control"
                type="time"
                v-model="getStarted[active].answer.work.end"
              />
            </div>
          </div>
          <span class="invalid">{{ step3WeekendDatesError }}</span>
          <h4 class="mt-3">On Weekends:</h4>
          <div class="d-flex flex-wrap justify-content-around">
            <div class="col-5">
              <span>Get up hour</span>
              <input
                class="form-control mb-3"
                type="time"
                v-model="getStarted[active].answer.weekend.start"
              />
            </div>
            <div class="col-5">
              <span>Bedtime hour</span>
              <input
                class="form-control"
                type="time"
                v-model="getStarted[active].answer.weekend.end"
              />
            </div>
          </div>
          <span class="invalid">{{ step3WeekendDatesError }}</span>
        </div>
      </b-tab>
      <b-tab title="Disabled" disabled>
        <div v-if="active == 3">
          <h1 style="font-size: 80px">All ready to begin!</h1>
        </div></b-tab
      >
    </b-tabs>

  

      <!----------------------------------------------------------- All ready to begin  ---------------------------------------------------------->

      <!----------------------------------------------------------- Buttons ---------------------------------------------------------->
      <div class="fixed-bottom mb-4">
        <button
          type="button"
          class="btn btn-dark btn-lg"
          style="margin-right: 10px"
          @click="previous()"
        >
          <i class="bi bi-caret-left-fill"></i>
        </button>
        <button
          type="button"
          v-if="active != 3"
          class="btn btn-dark btn-lg"
          @click="next()"
        >
          <i class="bi bi-caret-right-fill"></i>
        </button>
        <button
          type="button"
          v-else
          class="btn btn-success btn-lg btn-dark"
          @click="next()"
        >
          Submit
        </button>
      </div>
    </div>
</template>

<script>
import axios from "axios";
import EquipmentList from "../components/EquipmentList.vue";

export default {
  components: {
    EquipmentList,
  },
  data() {
    return {
      getStarted: [
        {
          question: "What are the divisions in your house?",
          answer: [],
        },
        {
          question:
            "Please enter information about the electrical equipment in your home",
          answer: [],
        },
        {
          question: "What is your get up and bedtime hour?",
          answer: {
            work: {
              start: "00:00",
              end: null,
            },
            weekend: {
              start: null,
              end: null,
            },
          },
        },
        {
          question: "",
        },
      ],
      active: 0,
      equipment: {
        name: null,
        consumption: null,
        category: -1,
        division: -1,
        selectedName: -1,
      },
      division: null,
      categories: null,
      nextStep: false,
      submit: false,
      equipments: [],
    };
  },
  created() {
    let divisions = ["Bathroom", "Bedroom", "Living Room", "Kitchen"];

    let equipments = [
      "Dishwasher",
      "Freezer",
      "Hairdyer",
      "Internet Router",
      "Light",
      "Oven",
      "Television",
      "Washing Machine",
      "Fridge",
      "Other...",
    ];

    equipments.forEach((equipment) => {
      this.equipments.push(equipment);
    });

    divisions.forEach((division) => {
      this.division = division;
      this.pushDivision();
    });

    axios
      .get(`/categories`)
      .then((response) => {
        this.categories = response.data.data;
      })
      .catch((error) => {
        console.log(error);
      });
  },
  computed: {
    userId() {
      return this.$store.getters.user_id;
    },
    step1DivisionError() {
      if (this.division == null || this.division.trim().length == 0)
        return this.submit ? "Division name is required" : "";

      return "";
    },
    isStep1Valid() {
      return this.getStarted[0].answer.length > 0;
    },

    step2EquipmentNameError() {
      if (
        this.equipment.name == null ||
        this.equipment.name.trim().length == 0
      ) {
        return this.submit ? "Equipment name is required" : "";
      }
      return "";
    },
    step2EquipmentConsumptionError() {
      if (this.equipment.consumption == null)
        return this.submit ? "Equipment consumption is required" : "";

      if (this.equipment.consumption <= 0) {
        return "Equipment consumption must be a positive number";
      }

      return "";
    },
    step2EquipmentCategoryError() {
      if (this.equipment.category == -1)
        return this.submit ? "Equipment category is required" : "";

      return "";
    },
    step2EquipmentDivisionError() {
      if (this.equipment.division == -1)
        return this.submit ? "Equipment division is required" : "";

      return "";
    },
    isStep2EquipmentValid() {
      return !(
        this.step2EquipmentConsumptionError ||
        this.step2EquipmentCategoryError ||
        this.step2EquipmentDivisionError
      );
    },
    isStep2Valid() {
      return this.getStarted[1].answer.length > 0;
    },
    step3WorkDatesError() {
      if (
        this.getStarted[this.active].answer.work.start == null ||
        this.getStarted[this.active].answer.work.end == null
      ) {
        return "";
      }

      let parts = this.getStarted[this.active].answer.work.start.split(":");
      let startHour = parseInt(parts[0]);
      let startMinute = parseInt(parts[1]);

      parts = this.getStarted[this.active].answer.work.end.split(":");
      let endHour = parseInt(parts[0]);
      let endMinute = parseInt(parts[1]);

      let errMsg = "The bedtime should be later then the get up time";

      if (startHour > endHour) return errMsg;

      if (startHour == endMinute && startMinute >= endMinute) return errMsg;

      return "";
    },
    step3WeekendDatesError() {
      if (
        this.getStarted[this.active].answer.weekend.start == null ||
        this.getStarted[this.active].answer.weekend.end == null
      ) {
        return "";
      }

      let parts = this.getStarted[this.active].answer.weekend.start.split(":");
      let startHour = parseInt(parts[0]);
      let startMinute = parseInt(parts[1]);

      parts = this.getStarted[this.active].answer.weekend.end.split(":");
      let endHour = parseInt(parts[0]);
      let endMinute = parseInt(parts[1]);

      let errMsg = "The bedtime should be later then the get up time";

      if (startHour > endHour) return errMsg;

      if (startHour == endMinute && startMinute >= endMinute) return errMsg;

      return "";
    },
    isStep3Valid() {
      if (
        this.getStarted[this.active].answer.weekend.start == null ||
        this.getStarted[this.active].answer.weekend.end == null
      )
        return false;

      if (
        this.getStarted[this.active].answer.weekend.start == null ||
        this.getStarted[this.active].answer.weekend.end == null
      )
        return false;

      return !(this.step3WorkDatesError || this.step3WeekendDatesError);
    },
  },
  methods: {
    next() {
      this.nextStep = true;

      switch (this.active) {
        case 0:
          if (!this.isStep1Valid) return;
          this.active++;
          break;
        case 1:
          if (!this.isStep2Valid) return;
          this.active++;
          break;
        case 2:
          if (!this.isStep3Valid) return;
          this.active++;
          break;
        case 3: //TODO: POST FORM DATA
          break;
      }
    },
    previous() {
      if (--this.active < 1) this.active = 0;
    },
    pushDivision() {
      this.submit = true;

      if (this.step1DivisionError) {
        return;
      }

      axios
        .post(`/users/${this.userId}/divisions`, {
          name: this.division,
          user_id: this.userId,
        })
        .then((response) => {
          this.getStarted[0].answer.push(response.data.data);
        })
        .catch((error) => {
          console.log(error);
        });
      this.division = "";

      this.submit = false;
    },
    pushEquipment() {
      this.submit = true;

      if (this.equipment.selectedName != "Other...") {
        this.equipment.name = this.equipment.selectedName;
      }

      if (this.step2EquipmentNameError) {
        return;
      }

      if (!this.isStep2EquipmentValid) {
        return;
      }

      delete this.equipment.selectedName;
      this.getStarted[1].answer.push({ ...this.equipment });
      this.submit = false;

      this.equipment = {
        name: null,
        consumption: null,
        category: -1,
        division: -1,
        selectedName: -1,
      };
    },
    deleteDivision(id, index) {
      axios
        .delete(`/users/${this.userId}/divisions/${id}`)
        .then((response) => {
          this.getStarted[0].answer.splice(index, 1);
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style>
.invalid {
  color: red;
}
</style>