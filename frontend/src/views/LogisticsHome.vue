<script setup>
import { ref, onMounted } from 'vue';
import axios  from 'axios';
import Layout from '@/layout/main.vue';
import pageheader from '@/components/page-header.vue';
import ApexChart from 'vue3-apexcharts';

const kpi = ref({});

onMounted(async () => {
  kpi.value = await axios.get('/api/dash/logistics').then(r => r.data);
});
</script>

<template>
  <Layout>
    <pageheader title="Accueil Logistique" />

    <BRow class="mb-3">
      <BCol md="3">
        <a-card title="Expéditions">
          {{ kpi.outgoing }}
        </a-card>
      </BCol>

      <BCol md="3">
        <a-card title="Réceptions">
          {{ kpi.incoming }}
        </a-card>
      </BCol>

      <BCol md="3">
        <a-card title="Emplacements libres">
          <a-progress
              type="circle"
              :percent="kpi.free_pct ?? 0"
              :width="70"
              stroke-color="#faad14"
          />
        </a-card>
      </BCol>

      <BCol md="3">
        <a-card title="Couverture stock (j)">
          <h3 class="text-center">{{ kpi.coverage_days ?? '—' }}</h3>
        </a-card>
      </BCol>
    </BRow>

    <BRow>
      <BCol md="3">
        <a-card title="Lots expirant ≤ 30 j">
          <a-badge :count="kpi.expiring_lots" :number-style="{background:'#f5222d'}" />
        </a-card>
      </BCol>

    </BRow>
    <BRow class="mt-4" v-if="kpi.locationSeries && kpi.locationSeries.length">
      <BCol md="12">
        <a-card title="Matériaux par emplacement">
          <ApexChart
              type="bar"
              height="280"
              :series="[{ name: 'Qty', data: kpi.locationSeries }]"
              :options="{
          chart: { toolbar: { show:false } },
          xaxis: { categories: kpi.locationLabels, labels:{ rotate: -45 } },
          dataLabels:{ enabled:false },
          plotOptions:{ bar:{ distributed:true } }
        }"
          />
        </a-card>
      </BCol>
    </BRow>
  </Layout>
</template>
