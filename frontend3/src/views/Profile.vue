<template>
  <div class="container">
    <div class="row">
        <div class="col-md-3 border-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5"><img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg"><span class="font-weight-bold">Edogaru</span><span class="text-black-50">edogaru@mail.com.my</span></div>
        </div>
        <div class="col-md-5 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Profile Settings</h4>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6"><label class="labels">Name</label><input type="text" class="form-control" v-model="this.name"></div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12"><label class="labels">Email</label><input type="text" class="form-control" v-model="this.email"></div>                   
                </div>
                <div class="row mt-3">
                    <div class="col-md-6"><label class="labels">Birthdate</label><input type="text" class="form-control" v-model="this.birthdate"></div>
                </div>
                <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="button">Save Profile</button></div>
            </div>
        </div>
    </div>
    <EquipmentList :data="dummyData" :deleteFunction="deleteItem" :editFunction="deleteItem" />


       <q-carousel
        v-model="slide"
        transition-prev="scale"
        transition-next="scale"
        swipeable
        animated
        control-color="white"
        navigation
        padding
        arrows
        height="300px"
        class="bg-primary text-white shadow-1 rounded-borders"
      >
        <q-carousel-slide name="style" class="column no-wrap flex-center">
          <q-icon name="style" size="56px" />
          <div class="q-mt-md text-center">
            {{ lorem }}
          </div>
        </q-carousel-slide>
        <q-carousel-slide name="tv" class="column no-wrap flex-center">
          <q-icon name="live_tv" size="56px" />
          <div class="q-mt-md text-center">
            {{ lorem }}
          </div>
        </q-carousel-slide>
        <q-carousel-slide name="layers" class="column no-wrap flex-center">
          <q-icon name="layers" size="56px" />
          <div class="q-mt-md text-center">
            {{ lorem }}
          </div>
        </q-carousel-slide>
        <q-carousel-slide name="map" class="column no-wrap flex-center">
          <q-icon name="terrain" size="56px" />
          <div class="q-mt-md text-center">
            {{ lorem }}
          </div>
        </q-carousel-slide>
      </q-carousel>

      <br>


</div>
</template>

<script>
import axios from "axios";
import EquipmentList from "../components/EquipmentList.vue";


export default {


    components: {
        EquipmentList
    },
    data(){
        return{
            name:"",
            email:"",
            birthdate:"",
            dummyData: [
        {
            id: 1,
            name: "Frigorifico",
            consumption: "105.00",
            division: "Kitchen",
            category: "Home Appliance"
        },
        {
            id: 2,
            name: "Maquina café",
            consumption: "750.00",
            division: "Kitchen",
            category: "Small Home Appliance"
        },
        {
            id: 3,
            name: "Microondas",
            consumption: "1200.00",
            division: "Kitchen",
            category: "Small Home Appliance"
        },
        {
            id: 4,
            name: "Tostadeira",
            consumption: "1000.00",
            division: "Kitchen",
            category: "Small Home Appliance"
        },
        {
            id: 5,
            name: "Placa de Indução",
            consumption: "1200.00",
            division: "Kitchen",
            category: "Home Appliance"
        },
        {
            id: 6,
            name: "Chaleira",
            consumption: "700.00",
            division: "Kitchen",
            category: "Small Home Appliance"
        },
        {
            id: 7,
            name: "Maquina de lavar loiça",
            consumption: "1200.00",
            division: "Kitchen",
            category: "Home Appliance"
        },
        {
            id: 8,
            name: "Painel Led 1",
            consumption: "42.00",
            division: "Kitchen",
            category: "Illumination"
        },
        {
            id: 9,
            name: "Painel Led 2",
            consumption: "17.00",
            division: "Living room",
            category: "Illumination"
        },
        {
            id: 10,
            name: "Painel Led 3",
            consumption: "17.00",
            division: "Living room",
            category: "Illumination"
        },
        {
            id: 11,
            name: "Televisão Samsung",
            consumption: "150.00",
            division: "Living room",
            category: "Image"
        },
        {
            id: 12,
            name: "Aparelhagem SONY",
            consumption: "100.00",
            division: "Living room",
            category: "Sound"
        },
        {
            id: 13,
            name: "Grunding Home Cinema",
            consumption: "160.00",
            division: "Living room",
            category: "Sound"
        },
        {
            id: 14,
            name: "Carregador Portátil",
            consumption: "65.00",
            division: "Living room",
            category: "Charger"
        },
        {
            id: 15,
            name: "Consola Wii",
            consumption: "13.50",
            division: "Living room",
            category: "Entertainment"
        }
      ]
        }
    },
    created() {
        axios
        .get("/user")
        .then((response) => {
            console.log(response)
            this.name = response.data.name,
            this.email = response.data.email,
            this.birthdate = response.data.birthdate
        })
        .catch((error) => {
            console.log("error");
        });
    },
    methods: {
        deleteItem(id) {
            this.dummyData = this.dummyData.filter((item) => {
                return item.id !== id
            })
        }
    }
}
</script>

<style>
body {
    background: rgb(99, 39, 120)
}

.form-control:focus {
    box-shadow: none;
    border-color: #BA68C8
}

.profile-button {
    background: rgb(99, 39, 120);
    box-shadow: none;
    border: none
}

.profile-button:hover {
    background: #682773
}

.profile-button:focus {
    background: #682773;
    box-shadow: none
}

.profile-button:active {
    background: #682773;
    box-shadow: none
}

.back:hover {
    color: #682773;
    cursor: pointer
}

.labels {
    font-size: 11px
}

.add-experience:hover {
    background: #BA68C8;
    color: #fff;
    cursor: pointer;
    border: solid 1px #BA68C8
}
</style>