<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
// Eager or lazy imports – your choice
import AdminHome from '../views/AdminHome.vue'
import LogisticsHome from '../views/LogisticsHome.vue'
import ProductionHome from '../views/ProductionHome.vue';
import EngineeringHome from '../views/EngineeringHome.vue'
import QAHome from '../views/QAHome.vue'

const router = useRouter();
const store = useStore();
const dept  = computed(() => store.getters['auth/department']);

if (!dept.value){
  router.push("/login");
}
const component = computed(() => {
  switch (dept.value) {
    case 'Administration'    : return AdminHome;
    case 'Logistics'    : return LogisticsHome;
    case 'Production'  : return ProductionHome;
    case 'Engineering': return EngineeringHome;
    case 'Quality Control'    : return QAHome;
    default         : return () => import('../views/login.vue');
  }
});
</script>

<template>
  <component :is="component" />
</template>
