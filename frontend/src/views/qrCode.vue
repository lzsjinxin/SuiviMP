<template>

  <!-- QR + print button wrapper -->
  <div class="d-flex flex-column justify-content-center align-items-center gap-2 p-6">
  <!-- Header hidden on print -->
  <a-page-header :title="url" />
    <!-- Ant Design Vue QR Code -->
    <a-qrcode :value="url" :size="240" />

    <!-- Print action (hidden in print mode) -->
    <a-button type="primary" @click="print" class="no-print">
      <template #icon>
        <PrinterOutlined />
      </template>
      Imprimer
    </a-button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { PrinterOutlined } from '@ant-design/icons-vue';

// Read ?u= query; fallback to current origin
const route = useRoute();
const url = computed(() => route.query.u || window.location.origin);

function print() {
  window.print();
}
</script>

<style>
/* Hide header & button when printing */
@media print {
  .no-print { display: none !important; }
  body { margin: 0; }
  /* Full‑page centering for QR */
  .flex { justify-content: center; align-items: center; height: 100vh; }
}
</style>
