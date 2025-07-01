<script>
import Layout from '@/layout/main.vue';
import pageheader from '@/components/page-header.vue';
import { openPdf } from '@/utils/pdf';

import { ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'ProductOrderGenerate',
  components: { Layout, pageheader },

  setup () {
    const route   = useRoute();
    const foId    = route.params.id;
    const loading = ref(false);
    const details = ref([]);

    fetchDetails();

    async function fetchDetails () {
      loading.value = true;

      const { data } = await axios.get(`/api/fabricationOrders/${foId}`);
      const enriched = await Promise.all(
          data.details.map(async d => {
            const done = await axios
                .get(`/api/fabricationOrderDetails/${d.id}/count-product-orders`)
                .then(r => r.data.count);
            return { ...d, produced: done, remaining: d.quantity - done };
          })
      );

      details.value = enriched;
      loading.value = false;
    }

    async function generate (detail) {
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
      await fetchDetails();
      Swal.fire({ icon: 'success', title: 'Créé !', timer: 1200, toast: true, position: 'center', showConfirmButton: false });
    }

    /** openPdf() helper will stream the sheet in a new tab */
    async function getPdf (detail) {
      await openPdf(`/api/product-orders/${detail.id}/sheet`, `PO-${detail.id}.pdf`);
    }

    return { loading, details, generate, getPdf };
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
              <BSpinner variant="primary" style="width:3rem;height:3rem" />
            </div>

            <a-table
                v-else
                :dataSource="details"
                :columns="[
                { title:'Produit',           dataIndex:'product_title', key:'prod'   },
                { title:'Qté demandée',      dataIndex:'quantity',      key:'qty'    },
                { title:'Produites',         dataIndex:'produced',      key:'done'   },
                { title:'Restantes',         dataIndex:'remaining',     key:'remain' },
                { title:'Action',            key:'act' }
              ]"
                row-key="id"
                :pagination="false"
            >
              <template #bodyCell="{ record, column }">
                <template v-if="column.key === 'act'">
                  <!-- Generate button -->
                  <a-button
                      type="primary"
                      size="small"
                      :disabled="record.remaining <= 0"
                      @click="generate(record)"
                  >
                    Générer
                  </a-button>

                  <!-- Print button (shows only when everything is generated) -->
                  <a-button
                      v-if="record.remaining <= 0"
                      type="default"
                      size="small"
                      class="ms-2"
                      @click="getPdf(record)"
                  >
                    Imprimer
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
