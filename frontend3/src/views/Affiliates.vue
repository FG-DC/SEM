<template>
  <b-container class="container mt-5">
    <b-card class="text-card">
      <span class="mb-3"><b>Affiliates</b></span>
      <div class="text-center">
        <v-text-field
          class="mt-3"
          solo
          v-model="email"
          label="Email"
        ></v-text-field>
        <b-button variant="primary">Add member</b-button>
      </div>
    </b-card>

    <b-card class="mt-5">
      <v-simple-table fixed-header height="300px">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">Name</th>
              <th class="text-left">Email</th>
              <th class="text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Daniel Carreira</td>
              <td>dcarreira7@mail.pt</td>
              <td><b-button variant="danger">Unlink user</b-button></td>
            </tr>
            <tr>
              <td>Carlos Costa</td>
              <td>carloscosta@mail.pt</td>
              <td><b-button variant="danger">Unlink user</b-button></td>
            </tr>
            <tr>
              <td>Maria Ferreira</td>
              <td>mariaferreira@mail.pt</td>
              <td><b-button variant="danger">Unlink user</b-button></td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </b-card>

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
      <div class="text-center"></div>
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
export default {
  data() {
    return {
      email: "",
    };
  },
};
</script>

<style>
.text-card {
  color: #191645;
  font-size: 2.5rem;
}
</style>