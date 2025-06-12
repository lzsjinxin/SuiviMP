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
import Swal from "sweetalert2";
import { notification } from 'ant-design-vue';

// Import French language file
import frenchLanguage from 'datatables.net-plugins/i18n/fr-FR.json'

DataTable.use(DataTablesCore)

export default {
    name: "TierSeriesList",
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
                { data: 'series', title: 'Nom de la Famille' },
                { 
                    data: 'tier.name', 
                    title: 'Client',
                    render: (data) => {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'products', 
                    title: 'Produits',
                    orderable: false,
                    render: (data) => {
                        if (!data || data.length === 0) {
                            return '<span class="text-muted">-</span>';
                        }
                        
                        const products = data;
                        const displayProducts = products.slice(0, 10);
                        const hasMore = products.length > 10;
                        let html = '<div class="products-container">';
                        
                        // Create the display text
                        let displayText = displayProducts.map(product => 
                            `<a href="/products/${product.id}" class="product-link text-primary text-decoration-none">${product.title}</a>`
                        ).join('<br>');
                        
                        if (hasMore) {
                            displayText += '<br><span class="text-muted">...</span>';
                        }
                        
                        // If there are more than 10 products, create tooltip with all products
                        if (hasMore) {
                            const allProductsText = products.map(product =>
                                `<a href="/products/${product.id}" class="product-link text-primary text-decoration-none">${product.title}</a>`
                            ).join('\n');
                            
                            html += `
                                <div class="products-display" 
                                     data-bs-toggle="tooltip"
                                     data-bs-html="true"
                                     title="${allProductsText.replace(/"/g, '&quot;')}"
                                     style="cursor: pointer;">
                                    ${displayText}
                                </div>
                            `;
                        } else {
                            html += `<div class="products-display">${displayText}</div>`;
                        }
                        
                        html += '</div>';
                        return html;
                    }
                },
                { 
                    data:null,
                    title: 'Actions',
                    orderable: false,
                    render: (data, type, row) => {
                            if (row.products.length != 0) {
                                 return `
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item">
                                        <a href="/tiers/series/${row.id}" class="avtar avtar-s btn-link-primary btn-pc-default">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                    </li>
                                </ul>
                                `
                            }else{
                                return `
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item">
                                        <a href="/tiers/series/${row.id}" class="avtar avtar-s btn-link-primary btn-pc-default">
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
                            this.deleteUnit(data.id);
                        }
                    });
                },
                drawCallback: () => {
                    // Initialize tooltips after each draw
                    this.$nextTick(() => {
                        this.initializeTooltips();
                    });
                }
            }
        }
    },
    methods: {
        fetchData() {
            axios.get('/api/tiers/series')
                .then(response => {
                    this.items = response.data;
                })
                .catch(error => {
                    console.error(error);
                    alert("Erreur lors du chargement des données");
                });
        },
        initializeTooltips() {
            // Initialize Bootstrap tooltips for elements with data-toggle="tooltip"
            const tooltipElements = document.querySelectorAll('[data-toggle="tooltip"]');
            tooltipElements.forEach(element => {
                if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Tooltip) {
                    new window.bootstrap.Tooltip(element, {
                        html: true,
                        sanitize: false
                    });
                }
            });
        },
        deleteUnit(id) {
            Swal.fire({
                title: 'Supression',
                html: `Voulez-vous vraiment supprimer la Famille n°<strong>${id}</strong> 
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/tiers/series/${id}`)
                    .then(() => {
                        notification.success({
                            message: 'Succès',
                            description: 'Famille supprimé avec succès',
                        });
                        this.fetchData(); // Refresh the data
                    })
                    .catch(error => {
                        console.error(error);
                        notification.error({
                            message: 'Erreur',
                            description: 'Erreur lors de la suppression de la Famille',
                        });
                    });
                }
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
        <pageheader title="Liste des Familles" secondaryPageHeader="Familles" pageTitle="Tiers" />

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