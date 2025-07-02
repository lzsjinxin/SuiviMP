<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

import Layout     from '@/layout/main.vue';
import pageheader from '@/components/page-header.vue';

const adm = ref({});   // { users, late_tasks }

onMounted(async () => {
  adm.value = await axios.get('/api/dash/admin').then(r => r.data);
});
</script>

<template>
  <Layout>
    <pageheader title="Accueil Administration" />

    <BRow>
      <BCol md="3"><a-card title="Utilisateurs">{{ adm.users }}</a-card></BCol>
      <BCol md="3"><a-card title="Tâches en retard">{{ adm.late_tasks }}</a-card></BCol>
    </BRow>
  </Layout>
</template>
