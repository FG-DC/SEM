<template>
  <b-container class="text-center mt-5" style="color: black">
    <v-card
      elevation="6"
      class="flex-grow-1 mt"
      style="border-radius: 10px; margin-right: 3%"
    >
      <div class="icons">
        <font-awesome-icon icon="fa-solid fa-house-chimney-window" />
        <span class="p-5">Divisions</span>
      </div>
    </v-card>
    <v-card
      elevation="6"
      class="icons flex-grow-1 mt-5"
      style="border-radius: 10px; margin-right: 3%"
    >
      <div class="icons">
        <font-awesome-icon icon="fa-solid fa-plug" />
        <span class="p-5">Equipments</span>
      </div>
    </v-card>
  </b-container>
</template>

<script>
export default {};
</script>

<style>
.icons {
  color: #191645;
  font-size: 6vw;
}
</style>