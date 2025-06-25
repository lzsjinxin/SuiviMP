<script>
import Layout from '@/layout/main.vue';
import pageheader from '@/components/page-header.vue';

import { ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'ProductOrderGenerate',
  components: { Layout, pageheader },

  setup() {
    const route     = useRoute();
    const foId      = route.params.id;   // /fo/:id/products/generate
    const loading   = ref(false);
    const details   = ref([]);           // rows with produced / remaining

    fetchDetails();

    async function fetchDetails() {
      loading.value = true;
      // you already have FO detail endpoint, adjust if needed
      const { data } = await axios.get(`/api/fabricationOrders/${foId}`);
      // map to detail rows + count produced
      const enriched = await Promise.all(
          data.details.map(async d => {
            const done = await axios.get(
                `/api/fabricationOrderDetails/${d.id}/count-product-orders`
            ).then(r => r.data.count);
            return { ...d, produced: done, remaining: d.quantity - done };
          })
      );
      details.value = enriched;
      loading.value = false;

      console.log(details)
    }

    async function generate(detail) {
      if (detail.remaining <= 0) return;

      const ok = await Swal.fire({
        title: 'Générer les numéros ?',
        html: `<b>${detail.remaining}</b> unité(s) pour <br><strong>${detail.product_title}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui',
        cancelButtonText: 'Non',
      }).then(r => r.isConfirmed);

      if (!ok) return;

      await axios.post(`/api/fabricationOrderDetails/${detail.id}/generate`);
      await fetchDetails();              // refresh counters
      Swal.fire({ icon:'success', title:'Créé !' , timer:1200, toast:true, position:'center', showConfirmButton:false});
    }

    return { loading, details, generate };
  },
};
</script>

<template>
  <Layout>
    <pageheader
        title="Générer les Product Orders"
        page-title="Ordres de Fabrication"
        :subtitle="`OF #${$route.params.id}`"
    />

    <BRow>
      <BCol sm="12">
        <BCard no-body>
          <BCardBody>
            <div v-if="loading" class="text-center my-3">
              <BSpinner variant="primary" style="width:3rem;height:3rem"/>
            </div>

            <a-table
                v-else
                :dataSource="details"
                :columns="[
                { title:'Produit', dataIndex:'product_title', key:'prod' },
                { title:'Qté demandée', dataIndex:'quantity', key:'qty' },
                { title:'Produites', dataIndex:'produced', key:'done' },
                { title:'Restantes', dataIndex:'remaining', key:'remain' },
                { title:'Action', key:'act' }
              ]"
                row-key="id"
                :pagination="false"
            >
              <template #bodyCell="{ record, column }">
                <template v-if="column.key==='act'">
                  <a-button
                      type="primary"
                      size="small"
                      :disabled="record.remaining<=0"
                      @click="generate(record)"
                  >
                    Générer
                  </a-button>
                </template>
              </template>
            </a-table>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>
