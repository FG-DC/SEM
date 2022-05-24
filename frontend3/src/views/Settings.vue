<template>
  <v-container class="text-center mt-2" style="color: black">
    <router-link :to="{name:'divisions'}" style="text-decoration:none">
    <v-card
      elevation="6"
      class="flex-grow-1 card-selectable"
      style="border-radius: 10px;"
    >
      <div class="icons">
        <font-awesome-icon icon="fa-solid fa-house-chimney-window" />
        <span class="p-5">Divisions</span>
      </div>
    </v-card></router-link>
     <router-link :to="{name:'equipments'}" style="text-decoration:none">
    <v-card
      elevation="6"
      class="flex-grow-1 mt-4 card-selectable"
      style="border-radius: 10px;"
    >
      <div class="icons">
        <font-awesome-icon icon="fa-solid fa-plug" />
        <span class="p-5">Equipments</span>
      </div>
    </v-card>
     </router-link>
  </v-container>
</template>

<script>
export default {};
</script>

<style>
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


</style>