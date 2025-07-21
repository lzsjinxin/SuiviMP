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

/* NEW ↓↓↓ */
import Buttons from 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';

DataTable.use(DataTablesCore)
DataTable.use(Buttons);

export default {
    name: "ProductList",
    components: {
        Layout, 
        pageheader,
        DataTable
    },
    data() {
        return {
            items: [],
            columns: [
                { data: 'id', title: '#', className: 'text-end' },
                { data: 'title', title: 'Libellé' },
                { data: 'product_series.series', title: 'Famille' },
                { data: 'product_status.status', title: 'Statut' },
              {
                data: null,
                title: 'Operations - Ordre',
                orderable: false,
                render: (data, type, row) => {
                  if (row.product_operations.length === 0) {
                    return `N/A`;
                  }
                  const operations = row.product_operations;
                  let html = '<div class="products-container">';

                  if(row.product_status.status != "Created" && row.product_status.status != "Operations Affected"){


                    let displayText = operations.map(operation =>
                        `<span class="text-primary">${operation.operation_definition.name} - ${operation.operation_order}</span>`
                    ).join('<br>');
                    const allOperationsText = operations.map(operation =>
                        `${operation.operation_definition.name}`
                    ).join('\n');
                    html += `<div class="products-display"
                    data-bs-toggle="tooltip"
                     data-bs-html="true"
                     data-placement="top" title="${allOperationsText.replace(/"/g, '&quot;')}">${displayText}</div>`
                    html += '</div>';
                    return html;
                  }else{
                    let displayText = operations.map(operation =>
                        `<a href="/operations/allocation/${row.id}" class="text-primary">${operation.operation_definition.name} - ${operation.operation_order}</a>`
                    ).join('<br>');
                    const allOperationsText = operations.map(operation =>
                        `${operation.operation_definition.name}`
                    ).join('\n');
                    html += `<div class="products-display"
                    data-bs-toggle="tooltip"
                     data-bs-html="true"
                     data-placement="top" title="${allOperationsText.replace(/"/g, '&quot;')}">${displayText}</div>`

                    html += '</div>';
                    return html;

                  }
                }
              },
                { 
                    data:null,
                    title: 'Actions',
                    orderable: false,
                    render: (data, type, row) => {
                            if ( row.product_status.status != "Created" && row.product_status.status != "Operations Affected"){
                                 return `
                                `
                            }else{
                                return `
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item">
                                        <a href="/products/${row.id}" class="avtar avtar-s btn-link-primary btn-pc-default">
                                            <i class="fa fa-edit f-15"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="javascript:void(0)" onclick="event.preventDefault();" class="avtar avtar-s btn-link-danger btn-pc-default" data-id="${row.id}">
                                            <i class="fa fa-trash f-15"></i>
                                        </a>
                                    </li>
                                </ul>
                                `
                            }
                            
                    }
                }
            ],
            dtOptions: {
                responsive: true,
                dom: 'Bfrtip',
                buttons: [{
                                extend: "copy", //Button type
                                text: "Copier", //Button title on screen
                                title: null, //Title before data is shown in output
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                },
                            },
                            {
                                extend: "csv",
                                text: "CSV",
                                title: null,
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                },
                            },
                            {
                                extend: "print",
                                text: "Imprimer",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                },
                            },
                        ],
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
            axios.get('/api/products/')
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
        <pageheader title="Liste des Produits" pageTitle="Produits" />

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