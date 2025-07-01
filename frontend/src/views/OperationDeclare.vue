<script setup>
import { ref, computed } from 'vue';
import axios  from 'axios';
import Swal   from 'sweetalert2';
import { toast } from '@/utils/toast';

import pageheader from '@/components/page-header.vue';

/* ---------- reactive state --------------------------------------- */
const step           = ref('idle');              // idle → po → op → mat → qty → running
const po             = ref(null);               // PO object from API
const opsFlow        = ref([]);                 // ordered array of op defs
const doneIds        = ref(new Set());          // finished op_def ids
const currentOpDef   = ref(null);               // next op to scan
const opId           = ref(null);               // scanned op id
const mat            = ref(null);               // material object
const qtyUsed        = ref(1);

/* ---------- scan handler ----------------------------------------- */
async function onScan(raw) {
  const text = raw?.trim();
  if (!text) return;
  const [tag, val] = text.split(':');

  if (tag === 'PO')  return handlePoScan(+val);
  if (tag === 'OP' && step.value === 'po')  return handleOpScan(+val);
  if (tag === 'MAT' && step.value === 'op') return handleMatScan(+val);

  toast('error', 'Code inattendu ou ordre incorrect');
}

/* -------------------- PO ----------------------------------------- */
async function handlePoScan(id) {
  try {
    const { data } = await axios.get(`/api/product-orders/${id}`);

    if (data.status === 'finished') {
      toast('error', 'Ce produit est déjà terminé'); return;
    }

    po.value   = data;
    opsFlow.value = data.operation_flow;
    doneIds.value = new Set(data.operations_done);

    currentOpDef.value = opsFlow.value.find(d => !doneIds.value.has(d.id));
    if (!currentOpDef.value) {
      toast('error', 'Toutes les opérations sont déjà réalisées'); return;
    }

    step.value = 'po';
    toast('success', `S/N ${data.serial_number} — prochaine op : ${currentOpDef.value.name}`);
  } catch (e) {
    toast('error', e.response?.data?.message ?? 'Produit introuvable');
  }
}

/* -------------------- OP ----------------------------------------- */
async function handleOpScan(defId) {
  if (defId !== currentOpDef.value.id) {
    toast('error', 'Ordre incorrect : scannez l\'opération suivante'); return;
  }

  const { data } = await axios.get(`/api/ops/check/${po.value.id}/${defId}`);
  if (data.done) {
    toast('error', 'Opération déjà déclarée'); return;
  }

  opId.value  = defId;
  step.value  = 'op';
  toast('success', 'Opération OK — scannez la matière');
}

/* -------------------- MAT ---------------------------------------- */
async function handleMatScan(id) {
  try {
    const { data } = await axios.get(`/api/materials/${id}`);
    mat.value  = data;
    step.value = 'mat';
    askQty();
  } catch {
    toast('error', 'Matière inconnue');
  }
}

/* -------------------- qty prompt + start ------------------------- */
function askQty() {
  Swal.fire({
    title: 'Quantité utilisée',
    input: 'number',
    inputValue: 1,
    inputAttributes: { min: 0.001, step: 0.001 },
    showCancelButton: true,
  }).then(async res => {
    if (!res.isConfirmed) return reset();
    qtyUsed.value = res.value;
    await startOp();
  });
}

async function startOp() {
  try {
    await axios.post('/api/ops/declare/start', {
      po_id: po.value.id,
      op_def_id: opId.value,
      mat_id: mat.value[0].id,
      qty_used: qtyUsed.value,
    });
    toast('success', 'Opération démarrée');
    step.value = 'running';
  } catch (e) {
    toast('error', e.response?.data?.message ?? 'Erreur démarrage op');
  }
}

async function finishOp() {
  await axios.post('/api/ops/declare/finish', {
    po_id: po.value.id,
    op_def_id: opId.value,
  });
  doneIds.value.add(opId.value);
  toast('success', 'Terminée');
  reset(false);                  // keep PO context
}

/* -------------------- helpers ----------------------------------- */
function reset(clearPo = true) {
  if (clearPo) { po.value = opsFlow.value = null; doneIds.value = new Set(); }
  step.value  = po.value ? 'po' : 'idle';
  opId.value  = mat.value = currentOpDef.value = null;
  qtyUsed.value = 1;
}

/* progress label */
const progressLabel = computed(() =>
    po.value ? `${doneIds.value.size}/${opsFlow.value.length}` : ''
);

/* tag color helper */
const opStatus = (id) => {
  if (doneIds.value.has(id)) return 'success';
  if (id === currentOpDef.value?.id) return 'processing';
  return 'default';
};
</script>

<template>
  <pageheader title="Déclaration d'opération" page-title="Production" />

  <!-- Serial number banner -->
  <a-alert v-if="po" :message="'S/N : ' + po.serial_number" type="info" banner />

  <!-- Steps timeline -->
  <div v-if="opsFlow?.length" class="my-3">
    <a-steps direction="horizontal" >
      <a-step
          v-for="op in opsFlow"
          :key="op.id"
          :title="op.name"
          :status="opStatus(op.id)"
          :description="'Op ' + op.sequence"
      />
    </a-steps>
  </div>

  <!-- Material card -->
  <a-card class="mb-3" v-if="mat">
    <template #title> Matière scannée </template>
    <p><b>Code :</b> {{ mat.code }}</p>
    <p><b>Désignation :</b> {{ mat.title }}</p>
    <p><b>Batch :</b> {{ mat.batch_number }}</p>
    <p><b>Qte dispo :</b> {{ mat.qty }} {{ mat.unit?.title }}</p>
  </a-card>

  <!-- Scan field -->
  <div class="text-center my-4">
    <input
        class="form-control text-center"
        placeholder="Scanner QR…"
        @keyup.enter="onScan($event.target.value); $event.target.value='';"
        autofocus
        style="max-width: 320px; margin: 0 auto"
    />

    <div class="mt-3" v-if="po">
      <a-tag color="processing">{{ progressLabel }}</a-tag>
      <a-tag color="blue" v-if="step === 'running'">En cours…</a-tag>
    </div>

    <a-button
        v-if="step === 'running'"
        type="primary"
        class="mt-3"
        @click="finishOp"
    >
      Finir l'opération
    </a-button>
    <a-button
        v-if="po"
    type="default"
    danger
    class="mt-3"
    @click="reset(true)"
    >
    Réinitialiser
    </a-button>
  </div>
</template>
