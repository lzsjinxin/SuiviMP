<!-- TODO: Fix Tooltip -->
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
// import Swal from "sweetalert2";
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
    name: "TransfersList",
    components: {
        Layout, 
        pageheader,
        DataTable,
    },
    data() {
        return {
            items: [],
            columns: [
                { data: 'id', title: '#', className: 'text-end' },
                {data:'material.num',title : 'Matière Premiere',className:'text-center'},
                {data:'location_from.name',title : 'lieu d\'origine',className:'text-center'},
                {data:'location_to.name',title : 'lieu de destination',className:'text-center'},
                {data:'mouvement_date',title : 'Date',className:'text-center'},
                {data:'movement_type.definition',title : 'Type',className:'text-center'}
            ],
            dtOptions: {
              responsive: true,
              dom: 'Bfrtip',
              buttons: [{
                                extend: "copy", //Button type
                                text: "Copier", //Button title on screen
                                title: null, //Title before data is shown in output
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                },
                            },
                            {
                                extend: "csv",
                                text: "CSV",
                                title: null,
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                },
                            },
                            {
                                extend: "print",
                                text: "Imprimer",
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                },
                            },
                        ],
              order: [[0, 'desc']],
              lengthMenu: [
                [50, 100, 150, 200, 250, 300, 400, 500, 1000, -1],
                [50, 100, 150, 200, 250, 300, 400, 500, 1000, "Tous"]
              ],
              language: frenchLanguage,
            }
        }
    },
    methods: {
        fetchData() {
            axios.get('/api/materials/transfers')
                .then(response => {
                    this.items = response.data;
                })
                .catch(error => {
                    console.error(error);
                    alert("Erreur lors du chargement des données");
                });
        },
        openNotificationWithIcon(type) {
            notification[type]({
                message: type === 'success' ? 'Succès' : 'Erreur',
                description: type === 'success' 
                    ? 'Famille supprimé avec succès' 
                    : 'Erreur lors de la suppression de la Famille',
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
        <pageheader title="Liste des Transferts" secondaryPageHeader="Transféres" pageTitle="Matières Premieres" />

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

.products-container {
    max-width: 200px;
}

.products-display {
    line-height: 1.4;
}

.product-link {
    font-size: 0.875rem;
    display: inline-block;
    margin-bottom: 2px;
}

.product-link:hover {
    text-decoration: underline !important;
}

/* Custom tooltip styling */
.tooltip {
    font-size: 0.875rem;
}

.tooltip-inner {
    max-width: 300px;
    text-align: left;
}
</style>