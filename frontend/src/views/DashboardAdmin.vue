<template>
   <b-container class="icons">
        <b-row no-gutters>
          <b-col cols="2">
            <font-awesome-icon
              class=""
              icon="fa-solid fa-user-gear"
            />
          </b-col>
          <b-col>
            <span class="p-5">Administrators</span>
          </b-col>
        </b-row>
      </b-container>
</template>

<script>
import axios from 'axios'

export default {
    data(){
        return{

        }
    }


}
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
</style>