<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import axios from "axios"
//DATATABLES
import DataTable from 'datatables.net-vue3'
import DataTablesCore from 'datatables.net'
import  'datatables.net-buttons'
import 'datatables.net-bs5' // Bootstrap 5 integration
import 'datatables.net-responsive-bs5' // Responsive with BS5 styling
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css' // BS5 CSS
//END DATATABLES
import Swal from "sweetalert2";
import { notification } from 'ant-design-vue';

// Import French language file
import frenchLanguage from 'datatables.net-plugins/i18n/fr-FR.json'

DataTable.use(DataTablesCore)

export default {
  name: "FOList",
  components: {
    Layout,
    pageheader,
    DataTable
  },
  data() {
    return {
      items: [],
      columns: [
        { data: 'order_number', title: 'Numéro' },
        { data: 'tier.name', title: 'Client' },
        { data: 'order_date', title: 'Date de Cmde' },
        { data: 'requested_delivery_date', title: 'Date de Livraison' },
        { data: 'priority', title: 'Priorité' },
        { data: 'notes', title: 'Remarques' },
        {
          data: null,
          title: 'Produits - Qte',
          orderable: false,
          render: (data, type, row) => {
            const products = row.fabrication_order_details;
            let html = '<div class="products-container">';
            let displayText = products.map(product =>
                `<span class="text-primary">${product.product.title} - ${product.quantity}</span>`
            ).join('<br>');
            const allproductsText = products.map(product =>
                `${product.product.title}`
            ).join('\n');
            html += `<div class="products-display"
                  data-bs-toggle="tooltip"
                   data-bs-html="true"
                   data-placement="top" title="${allproductsText.replace(/"/g, '&quot;')}">${displayText}</div>`
            html += '</div>';
            return html;
          }
        },
        {
          data:null,
          title: 'Actions',
          orderable: false,
          render: (data, type, row) => {

              return `
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item">
                                        <a href="/fo/${row.id}/product-orders/new" class="avtar avtar-s btn-link-primary btn-pc-default" title="Générer les Numéro de series">
                                            <i class="fas fa-hashtag f-15"></i>
                                        </a>
                                    </li>
                                </ul>
                                `
          }
        }
      ],
      dtOptions: {
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[ 0, 'desc' ]],
        lengthMenu: [
          [50, 100, 150, 200, 250, 300, 400, 500, 1000, -1],
          [50, 100, 150, 200, 250, 300, 400, 500, 1000, "Tous"]
        ],
        language: frenchLanguage,
        createdRow: (row, data) => {
          row.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.btn-link-danger');
            if (deleteBtn) {
              e.preventDefault();
              this.deleteUnit(data.id);
            }
          });
        },

      }
    }
  },
  methods: {
    fetchData() {
      axios.get('/api/fabricationOrders/')
          .then(response => {
            this.items = response.data;
          })
          .catch(error => {
            console.error(error);
            alert("Erreur lors du chargement des données");
          });
    },
    deleteUnit(id) {
      Swal.fire({
        title: 'Supression',
        html: `Voulez-vous vraiment supprimer le Produit n°<strong>${id}</strong>
                `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(`/api/products/${id}`)
              .then(() => {
                notification.success({
                  message: 'Succès',
                  description: 'Produit supprimé avec succès',
                });
                this.fetchData(); // Refresh the data
              })
              .catch(error => {
                console.error(error);
                notification.error({
                  message: 'Erreur',
                  description: 'Erreur lors de la suppression du Produit',
                });
              });
        }
      });
    },
    openNotificationWithIcon(type) {
      notification[type]({
        message: type === 'success' ? 'Succès' : 'Erreur',
        description: type === 'success'
            ? 'Produit supprimé avec succès'
            : 'Erreur lors de la suppression du Produit',
      });
    },
  },
  mounted() {
    this.fetchData();
  }
}
</script>

<template>
  <Layout>
    <pageheader title="Liste des Ordres de Fabrication" pageTitle="Ordres de Fabrication" />

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
                  width="100%"
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