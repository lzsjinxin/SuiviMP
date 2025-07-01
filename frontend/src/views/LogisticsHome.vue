<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
// import { ref } from 'vue'
// import { useVuelidate } from '@vuelidate/core'
// import { required, helpers } from '@vuelidate/validators'
// import axios from 'axios'
// import Swal from "sweetalert2";
export default {
  name: "LogisticsHome",
  components: {
    Layout, pageheader
  },

}
</script>

<template>
  <Layout>
    <pageheader title="Accueil Logistique" pageTitle="Accueil" />

    <BRow>
      <BCol sm="12">
        <BCard no-body>
          <BCardBody style="width: 50%; margin: auto;">

          </BCardBody>
          <BCardFooter>

          </BCardFooter>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>