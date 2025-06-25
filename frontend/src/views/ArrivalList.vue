<!--TODO Add batches that are in each arrival -->
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
    name: "ArrivalList",
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
                { data: 'date', title: 'Date' },
                { data: 'tier', title: 'Provenance' },
                { data: 'vehicule_registration', title: 'Matricule de Vehicule <br /> <small><small>(En cas de 2 arrivages en même temps du meme provenance)</small></small>',
                render: (data, type, row) => {
                        return row.vehicule_registration ? row.vehicule_registration : 'N/A'
                        }
                 },
                { data: 'user', title: 'Receptionné Par'},
                { data: 'status', title: 'Status',
                render: (data, type, row) => {
                        switch(row.status){
                            case 'In Transit':
                                return ` <h5> <span class="badge bg-primary">En Transite <i class="ph-duotone ph-truck fw-bold" ></i></span> <i class="ph-duotone ph-lock-open fw-bold text-success"></i>  </h5>`;
                            case 'Partially Received' : 
                                return `<h5> <span class="badge bg-warning">Arrivé Partiellement <i class="ph-duotone ph-circle-half-tilt"></i></span> <i class="ph-duotone ph-lock fw-bold text-danger"></i>  </h5>`;
                            case 'Received' : 
                            return `<h5> <span class="badge bg-success">Reçu <i class="ph-duotone ph-package fw-bold"></i></span> <i class="ph-duotone ph-lock fw-bold text-danger"></i> </h5>`;
                        }
                    }
                },
                { 
                    data:null,
                    title: 'Actions',
                    orderable: false,
                    render: (data, type, row) => {
                        if (row.status === "In Transit"){
                            return `
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="/arrivals/${row.id}" class="avtar avtar-s btn-link-primary btn-pc-default">
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
                        }else{
                            return `
                        `
                        }
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
                        this.deleteArrival(data.id);
                    }
                });
                },
                
            }
        }
    },
    methods: {
        fetchData() {
            axios.get('/api/arrivals/')
                .then(response => {
                    this.items = response.data;
                })
                .catch(error => {
                    console.error(error);
                    alert("Erreur lors du chargement des données");
                });
        },
        deleteArrival(id) {
            Swal.fire({
                title: 'Supression',
                html: `Voulez-vous vraiment supprimer l'arrivage n°<strong>${id}</strong> 
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/arrivals/${id}`)
                    .then(() => {
                        notification.success({
                            message: 'Succès',
                            description: 'Arrivage supprimé avec succès',
                        });
                        this.fetchData(); // Refresh the data
                    })
                    .catch(error => {
                        console.error(error);
                        notification.error({
                            message: 'Erreur',
                            description: 'Erreur lors de la suppression de l\'arrivage',
                        });
                    });
                }
            });
        },
        openNotificationWithIcon(type) {
        notification[type]({
            message: type === 'success' ? 'Succès' : 'Erreur',
            description: type === 'success' 
                ? 'Arrivage supprimé avec succès' 
                : 'Erreur lors de la suppression de l\'arrivage',
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
        <pageheader title="Liste des Arrivages" :pageTitle="$t('Arrivals')" />

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