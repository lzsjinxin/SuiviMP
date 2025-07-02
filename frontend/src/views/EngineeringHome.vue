<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

import Layout     from '@/layout/main.vue';
import pageheader from '@/components/page-header.vue';
import ApexChart  from 'vue3-apexcharts';

const kpi = ref({});         // { open_ecns, cad_load, chartOpt, chartSeries }

onMounted(async () => {
  kpi.value = await axios.get('/api/dash/engineering').then(r => r.data);
});
</script>

<template>
  <Layout>
    <pageheader title="Accueil Ingénierie" />

    <BRow>
      <BCol md="4"><a-card title="ECN ouverts">{{ kpi.open_ecns }}</a-card></BCol>
      <BCol md="4"><a-card title="Charge CAO">{{ kpi.cad_load }} %</a-card></BCol>
      <BCol md="4" v-if="kpi.chartSeries">
        <ApexChart :options="kpi.chartOpt" :series="kpi.chartSeries" height="160" type="bar" />
      </BCol>
    </BRow>
  </Layout>
</template>
