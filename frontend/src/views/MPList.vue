<script>
import Layout from '@/layout/main.vue';
import pageheader from '@/components/page-header.vue';
import axios from 'axios';

// Datatables
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-buttons';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import frenchLanguage from 'datatables.net-plugins/i18n/fr-FR.json';

import Swal from 'sweetalert2';
import { notification } from 'ant-design-vue';
import { openPdf } from '@/utils/pdf';      // 🔄  NEW

DataTable.use(DataTablesCore);

export default {
  name: 'MPList',
  components: { Layout, pageheader, DataTable },
  data() {
    return {
      items: [],
      columns: [
        { data: 'id', title: '#' },
        { data: 'num', title: 'Numéro MP' },
        { data: 'material_type.type', title: 'Type MP' },
        {
          data: null,
          title: 'Qte',
          render: (data, type, row) =>
              row.qty + ' ' + row.unit.title,
        },
        { data: 'material_batch.batch_number', title: 'Num Lot' },
        {
          data: 'material_batch.can_be_expired',
          title: '<small>Peut être expiré</small>',
          render: (d, t, r) =>
              r.material_batch.can_be_expired
                  ? '<i class="fas fa-check-circle fw-bold text-success"></i>'
                  : '<i class="fas fa-times-circle fw-bold text-danger"></i>',
        },
        {
          data: null,
          title: "Date d'expiration",
          render: (d, t, r) =>
              r.material_batch.can_be_expired
                  ? r.material_batch.expiry_date
                  : 'N/A',
        },
        { data: 'location.name', title: 'Emplacement Actuel' },
        { data: 'material_status.status', title: 'Status' },
        /* ---------- Actions column ---------- */
        {
          data: null,
          title: 'Actions',
          orderable: false,
          render: (data, type, row) => {
            const editBtn = `
              <a href="/mp/${row.id}" class="avtar avtar-s btn-link-primary btn-pc-default">
                <i class="fa fa-edit f-15"></i>
              </a>`;
            const pdfBtn = `
              <a href="#" class="avtar avtar-s btn-link-info btn-pc-default btn-link-pdf" data-id="${row.id}">
                <i class="fa fa-file-pdf f-15"></i>
              </a>`;
            const deleteBtn = `
              <a href="#" class="avtar avtar-s btn-link-danger btn-pc-default btn-link-del" data-id="${row.id}">
                <i class="fa fa-trash f-15"></i>
              </a>`;
            return row.id_status == 1 && row.arrival.status !== 'Received'
                ? `<ul class="list-inline mb-0"><li class="list-inline-item">${editBtn}</li><li class="list-inline-item">${pdfBtn}</li><li class="list-inline-item">${deleteBtn}</li></ul>`
                : `<ul class="list-inline mb-0"><li class="list-inline-item">${editBtn}</li><li class="list-inline-item">${pdfBtn}</li></ul>`;
          },
        },
      ],
      dtOptions: {
        responsive: true,
        dom: 'Bfrtip',
        order: [[0, 'desc']],
        lengthMenu: [
          [50, 100, 150, 200, 250, 300, 400, 500, 1000, -1],
          [50, 100, 150, 200, 250, 300, 400, 500, 1000, 'Tous'],
        ],
        language: frenchLanguage,
        createdRow: (row, data) => {
          row.addEventListener('click', (e) => {
            /* DELETE */
            const del = e.target.closest('.btn-link-del');
            if (del) {
              e.preventDefault();
              this.deleteMP(data.id);
              return;
            }
            /* PDF */
            const pdf = e.target.closest('.btn-link-pdf');
            if (pdf) {
              e.preventDefault();
              this.printMaterialSheet(data.id);
            }
          });
        },
      },
    };
  },
  methods: {
    fetchData() {
      axios
          .get('/api/materials/')
          .then((r) => (this.items = r.data))
          .catch(() => alert('Erreur lors du chargement des données'));
    },

    async printMaterialSheet(id) {
      try {
        await openPdf(`/api/materials/${id}/sheet`, `MAT-${id}.pdf`);
      } catch {
        notification.error({
          message: 'Erreur',
          description: 'Impossible d’ouvrir le PDF',
        });
      }
    },

    deleteMP(id) {
      Swal.fire({
        title: 'Suppression',
        html: `Voulez-vous vraiment supprimer la MP n°<strong>${id}</strong> ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler',
      }).then((res) => {
        if (!res.isConfirmed) return;
        axios
            .delete(`/api/materials/${id}`)
            .then(() => {
              notification.success({
                message: 'Succès',
                description: 'MP supprimée avec succès',
              });
              this.fetchData();
            })
            .catch(() =>
                notification.error({
                  message: 'Erreur',
                  description: 'Erreur lors de la suppression',
                })
            );
      });
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>

<template>
  <Layout>
    <pageheader
        title="Liste des Matières Premières"
        pageTitle="Matières Premières"
    />

    <BRow>
      <BCol sm="12">
        <BCard no-body>
          <BCardBody>
            <div class="table-responsive">
              <DataTable
                  :data="items"
                  :columns="columns"
                  :options="dtOptions"
                  class="table table-striped table-bordered dt-responsive nowrap w-100 table-sm"
              />
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>

<style>
@import 'datatables.net-buttons-dt';
</style>
