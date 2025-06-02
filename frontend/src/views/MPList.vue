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
    name: "MPList",
    components: {
        Layout, 
        pageheader,
        DataTable
    },
    data() {
        return {
            items: [],
            columns: [
               {data:"id",title:"#"},
               {data:"num",title:"Numéro MP"},
               {data:"material_type.type",title:"Type MP"},
               {data:null,title:"Qte",
               render: (data, type, row) => {
                return row.qty + " " + row.unit.title
                    }
               },
               {data:"material_batch.batch_number",title:"Num Lot"},
               {data:"material_batch.can_be_expired",title:"<small>Peut être expiré</small>",
               render: (data, type, row) => {
                return row.material_batch.can_be_expired === true ? '<i class="fas fa-check-circle fw-bold text-success"></i>' : '<i class="fas fa-times-circle fw-bold text-danger"></i>';
                    }
               },
               {data:null,title:"Date d'expiration",
               render: (data, type, row) => {
                return row.material_batch.can_be_expired === true ? row.material_batch.expiry_date : 'N/A';
                    }
               },
               {data:"location.name",title:"Emplacement Actuel"},
               {data:"material_status.status",title:"Status"},
               { 
                    data:null,
                    title: 'Actions',
                    orderable: false,
                    render: (data, type, row) => {
                            if(row.id_status == 1 && row.arrival.status !="Received"){
                            return `
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="/mp/${row.id}" class="avtar avtar-s btn-link-primary btn-pc-default">
                                        <i class="ti ti-edit f-20"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript:void(0)" onclick="event.preventDefault();" class="avtar avtar-s btn-link-danger btn-pc-default" data-id="${row.id}">
                                        <i class="ti ti-trash f-20"></i>
                                    </a>
                                </li>
                            </ul>
                        `
                            }else{
                                 return `
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="/mp/${row.id}" class="avtar avtar-s btn-link-primary btn-pc-default">
                                        <i class="ti ti-edit f-20"></i>
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
                        this.deleteMP(data.id);
                    }
                });
                },
                
            }
        }
    },
    methods: {
        fetchData() {
            axios.get('/api/materials/')
                .then(response => {
                    this.items = response.data;
                })
                .catch(error => {
                    console.error(error);
                    alert("Erreur lors du chargement des données");
                });
        },
        deleteMP(id) {
            Swal.fire({
                title: 'Supression',
                html: `Voulez-vous vraiment supprimer la MP n°<strong>${id}</strong> 
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/materials/${id}`)
                    .then(() => {
                        notification.success({
                            message: 'Succès',
                            description: 'MP supprimé avec succès',
                        });
                        this.fetchData(); // Refresh the data
                    })
                    .catch(error => {
                        console.error(error);
                        notification.error({
                            message: 'Erreur',
                            description: 'Erreur lors de la suppression de la MP',
                        });
                    });
                }
            });
        },
        openNotificationWithIcon(type) {
        notification[type]({
            message: type === 'success' ? 'Succès' : 'Erreur',
            description: type === 'success' 
                ? 'MP supprimé avec succès' 
                : 'Erreur lors de la suppression de la MP',
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
        <pageheader title="Liste des Matières Premieres" pageTitle="Matières Premieres" />

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