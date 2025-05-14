<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import axios from "axios"
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import flatPickr from "vue-flatpickr-component"
import "flatpickr/dist/flatpickr.css"
import {French} from "flatpickr/dist/l10n/fr.js"
import Swal from "sweetalert2"
import { ref } from 'vue'  // Add this with your other imports
export default {
    name: "MPAdd",
    components: {
        Layout, pageheader, flatPickr
        // flatPickr,
    },
    setup() {
        const batchForm = ref({
            newbatchValue: null,
            batchDate: null,
            enableBatchDatePicker : false
        })
        
        const typeForm = ref({
            newTypeValue: null
        })
        return { v$: useVuelidate(),
            batchForm,
            typeForm
         }
    },
    data() {
        return {
            arrivals : [],
            selectedArrival: null,
            // Batch
            batches : [],
            selectBatches: [],
            selectedBatch: null,
            batchModalOpen : false,
            batchModalConfirmLoading : false,
            newbatchValue : null,
            batchDate : null,
            enableBatchDatePicker : false,
            // Location
            locations : [],
            selectLocation : [],
            selectedLocation : null,
            // Type
            types : [],
            selectTypes: [],
            selectedType: null,
            typesModalOpen : false,
            newTypeValue : null,
            flatpickrConfig: {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minuteIncrement: 1,
                locale: French,
                time_24hr: true,
                weekNumbers: true
            },
        }
    },
    validations() {
    return {
        batchForm: {
            newbatchValue: { 
                required: helpers.withMessage('Le numéro de lot est requis', required) 
            },
            batchDate: {
                required: helpers.withMessage('La date d\'expiration est requise', 
                    value => !this.batchForm.enableBatchDatePicker || !!value)
            }
        },
        typeForm: {
            newTypeValue: {
                required: helpers.withMessage('Le Type est requis', required)
            }
        }
    }
},
    methods: {
fetchArrivals() {
    axios.get('/api/arrivals/inTransitorpartial')
        .then(response => {
            // Status mapping from English to French
            const statusMap = {
                'In Transit': 'En Transite',
                'Partially Received': 'Arrivé Partiellement',
                'Received': 'Reçu'
            };
            
            this.arrivals = (
                // First, group the data by tier name
                Object.entries(
                    response.data.reduce((acc, item) => {
                        const tierName = item.tier?.name || 'Autres';
                        if (!acc[tierName]) {
                            acc[tierName] = [];
                        }
                        const formattedDate = this.formatDateTime(item.date);
                        const frenchStatus = statusMap[item.status] || item.status;
                        
                        acc[tierName].push({
                            value: item.id.toString(), 
                            label: `${item.tier.name} | ${formattedDate} - ${frenchStatus}` 
                        });
                        return acc;
                    }, {})
                ).map(([label, options]) => ({
                    label,
                    options
                }))
            );
        })
        .catch(error => {
            console.error(error);
        });
},
fetchBatches(){
    axios.get('/api/batches')
        .then(response => {
        this.batches = response.data;
        this.selectBatches = response.data.map(item => ({
            value: item.id,
            label: item.batch_number
            }));
        })
        .catch(error => {
            console.error(error);
        });
},
fetchLocations(){
    axios.get('/api/locations')
        .then(response => {
        this.locations = response.data;
        this.selectLocation = response.data.map(item => ({
            value: item.id,
            label: item.name
            }));
        })
        .catch(error => {
            console.error(error);
        });
},
fetchTypes(){
    axios.get('/api/materialtypes')
        .then(response => {
        this.types = response.data;
        this.selectTypes = response.data.map(item => ({
            value: item.id,
            label: item.type
            }));
        })
        .catch(error => {
            console.error(error);
        });
},
formatDateTime(dateTimeString) {
        if (!dateTimeString) return '';
        
        const date = new Date(dateTimeString);
        
        // Pad with leading zeros
        const pad = num => num.toString().padStart(2, '0');
        
        const day = pad(date.getDate());
        const month = pad(date.getMonth() + 1); // Months are 0-based
        const year = date.getFullYear();
        const hours = pad(date.getHours());
        const minutes = pad(date.getMinutes());
        
        return `${day}/${month}/${year} ${hours}:${minutes}`;
},
toggleBatchDatePicker(){
    if (!this.batchForm.enableBatchDatePicker) {
                this.batchForm.batchDate = ''
            }
},
async addBatch() {
            // Trigger validation
            const isFormValid = await this.v$.batchForm.$validate()
            
            if (!isFormValid) {
                // Show validation errors
                const firstError = this.v$.batchForm.$errors[0]
                Swal.fire({
                    title: 'Erreur de validation',
                    text: firstError.$message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
                return
            }

            Swal.fire({
                title: 'Ajout',
                html: `Voulez-vous vraiment Ajouter un Lot avec les données suivantes : 
                <div class="row mt-3">
                <div class="col">Numéro de Lot :</div>
                <div class="col">${this.batchForm.newbatchValue}</div>
                </div>
                <div class="row mt-3">
                <div class="col">Peut être expiré : </div>
                <div class="col">${!this.batchForm.enableBatchDatePicker? "Non" : "Oui"}</div>
                </div>
                <div class="row mt-3">
                <div class="col">Date d'expiration</div>
                <div class="col">${!this.batchForm.batchDate? "N/A" : this.batchForm.batchDate}</div>
                </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitBatch();
                }
            });
        },
async submitBatch() {
    try {
    // Prepare the payload
    const payload = {
    batch_number: this.batchForm.newbatchValue,
    expiry_date: this.batchForm.enableBatchDatePicker ? this.batchForm.batchDate : null, 
    can_be_expired: this.batchForm.enableBatchDatePicker,
    user: 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
    };

    // Make the POST request
    const response = await axios.post('/api/batches/', payload, {
    headers: {
    'Content-Type': 'application/json',
    // Add any auth headers if needed
    // 'Authorization': `Bearer ${yourToken}`
    }
    });

    // Handle success
    Swal.fire({
    title: 'Succès!',
    text: 'Lot Ajouté avec succès',
    icon: 'success',
    confirmButtonText: 'OK'
    }).then(() => {
        this.batchForm.newbatchValue = null;
        this.batchForm.enableBatchDatePicker = null;
        this.batchForm.batchDate = null;
        this.batchModalOpen = false;
        this.selectedBatch = response.data.id
        this.fetchBatches();
    });

    } catch (error) {
    // Handle errors
    console.error('Error submitting form:', error);

    let errorMessage = "Une erreur s'est produite";
    if (error.response) {
    // Server responded with error status
    errorMessage = error.response.data.message || 
                `Erreur ${error.response.status}: ${error.response.statusText}`;
    } else if (error.request) {
    // Request was made but no response
    errorMessage = "Pas de réponse du serveur";
    }

    Swal.fire({
    title: 'Erreur',
    text: errorMessage,
    icon: 'error',
    confirmButtonText: 'OK'
    });

    throw error; // Re-throw if you want to handle it elsewhere
    }
},
async addType(){
  // Trigger validation
  const isFormValid = await this.v$.typeForm.$validate()
            
            if (!isFormValid) {
                // Show validation errors
                const firstError = this.v$.typeForm.$errors[0]
                Swal.fire({
                    title: 'Erreur de validation',
                    text: firstError.$message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
                return;
            }

            Swal.fire({
                title: 'Ajout',
                html: `Voulez-vous vraiment Ajouter un Type avec les données suivantes : 
                <div class="row mt-3">
                <div class="col">Type :</div>
                <div class="col">${this.typeForm.newTypeValue}</div>
                </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitType();
                }
            });
},
async submitType(){
    try {
    // Prepare the payload
    const payload = {
    type : this.typeForm.newTypeValue,
    user: 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
    };

    // Make the POST request
    const response = await axios.post('/api/materialtypes/', payload, {
    headers: {
    'Content-Type': 'application/json',
    // Add any auth headers if needed
    // 'Authorization': `Bearer ${yourToken}`
    }
    });

    // Handle success
    Swal.fire({
    title: 'Succès!',
    text: 'Type Ajouté avec succès',
    icon: 'success',
    confirmButtonText: 'OK'
    }).then(() => {
        this.typeForm.newTypeValue = null;
        this.typesModalOpen = false;
        this.selectedType = response.data.id
        this.fetchTypes();
    });

    } catch (error) {
    // Handle errors
    console.error('Error submitting form:', error);

    let errorMessage = "Une erreur s'est produite";
    if (error.response) {
    // Server responded with error status
    errorMessage = error.response.data.message || 
                `Erreur ${error.response.status}: ${error.response.statusText}`;
    } else if (error.request) {
    // Request was made but no response
    errorMessage = "Pas de réponse du serveur";
    }

    Swal.fire({
    title: 'Erreur',
    text: errorMessage,
    icon: 'error',
    confirmButtonText: 'OK'
    });

    throw error; // Re-throw if you want to handle it elsewhere
    }
}
    },
    mounted() {
        this.fetchArrivals();
        this.fetchBatches();
        this.fetchLocations();
        this.fetchTypes();
    },
}
</script>

<template>
    <Layout>
        <pageheader title="Ajouter Matières Premieres" pageTitle="Matières Premieres" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <form>
                    <BCardBody>
                        <a-flex vertical="vertical">
                            <a-divider><PhTruck :size="23" weight="duotone" /> Arrivage</a-divider>
                            <a-flex  justify="space-around" align="center">
                                <a-select
                                v-model:value="selectedArrival"
                                show-search
                                placeholder="Selectionner un arrivage"
                                style="width: 100%;text-align: center;"
                                :options="arrivals"
                                ></a-select>
                                
                            </a-flex>
                            <a-divider><PhPackage :size="23" weight="duotone" />Matières Premieres</a-divider>
                            <a-flex  justify="space-between" align="center">
                            <!-- Lot -->
                            <div>
                                <label for="">Lot</label>
                                <a-select
                                v-model:value="selectedBatch"
                                show-search
                                placeholder="Selectionner un Lot"
                                style="width: 100%;text-align: center;"
                                :options="selectBatches"
                                >
                                </a-select>
                                <a-tooltip title="Ajouter un Lot" class="mt-2">
                                    <a-button type="primary" shape="circle" @click="batchModalOpen = !batchModalOpen">
                                        <PhPlus :size="15" />  
                                    </a-button>
                                </a-tooltip>
                                <div>
                                <BModal v-model="batchModalOpen" hide-footer title="Ajout d'un Nouveau Lot" class="v-modal-custom" title-class="varyingcontentModal">
                                    <div class="modal-body p-0 pb-4">
                                            <a-flex vertical="vertical" align="center">
                                                <a-input 
                                                    v-model:value="batchForm.newbatchValue" 
                                                    placeholder="Numéro de Lot" 
                                                    :class="{ 'is-invalid': v$.batchForm.newbatchValue.$error }"
                                                />
                                                <div v-if="v$.batchForm.newbatchValue.$error" class="text-danger">
                                                    {{ v$.batchForm.newbatchValue.$errors[0].$message }}
                                                </div>
                                                
                                                <a-checkbox v-model:checked="batchForm.enableBatchDatePicker" class="mt-2" @change="toggleBatchDatePicker">Peut être expiré</a-checkbox>
                                                
                                                <flat-pickr 
                                                    :config="flatpickrConfig"
                                                    class="form-control" 
                                                    :class="{ 'is-invalid': v$.batchForm.batchDate.$error }"
                                                    v-model="batchForm.batchDate"
                                                    :disabled="!batchForm.enableBatchDatePicker"
                                                    :placeholder="batchForm.enableBatchDatePicker ? 'Entrez la date d\'expiration' : 'Cochez la case pour activer'"
                                                ></flat-pickr>
                                                <div v-if="v$.batchForm.batchDate.$error" class="text-danger">
                                                    {{ v$.batchForm.batchDate.$errors[0].$message }}
                                                </div>
                                            </a-flex>
                                    </div>
                                    <div class="modal-footer pb-0">
                                        <BButton variant="secondary" @click="batchModalOpen = false">Annuler</BButton>
                                        <BButton variant="primary" @click="addBatch"><PhPlusCircle :size="15" />Créer</BButton>
                                    </div>
                                </BModal>
                                </div>
                            </div>
                            <!-- Location-->
                            <div>
                                <label for="">Emplacement</label>
                                <a-select
                                v-model:value="selectedLocation"
                                show-search
                                placeholder="Selectionner un Emplacement"
                                style="width: 100%;text-align: center;"
                                :options="selectLocation"
                                >
                                </a-select>
                            </div>
                            <!-- Type -->
                            <div>
                                <label for="">Type</label>
                                <a-select
                                v-model:value="selectedType"
                                show-search
                                placeholder="Selectionner un Lot"
                                style="width: 100%;text-align: center;"
                                :options="selectTypes"
                                >
                                </a-select>
                                <a-tooltip title="Ajouter un Type" class="mt-2">
                                    <a-button type="primary" shape="circle" @click="typesModalOpen = !typesModalOpen">
                                        <PhPlus :size="15" />  
                                    </a-button>
                                </a-tooltip>
                                <div>
                                <BModal v-model="typesModalOpen" hide-footer title="Ajout d'un Nouveau Type" class="v-modal-custom" title-class="varyingcontentModal">
                                    <div class="modal-body p-0 pb-4">
                                            <a-flex vertical="vertical" align="center">
                                                <a-input 
                                                    v-model:value="typeForm.newTypeValue" 
                                                    placeholder="Type" 
                                                    :class="{ 'is-invalid': v$.typeForm.newTypeValue.$error }"
                                                />
                                                <div v-if="v$.typeForm.newTypeValue.$error" class="text-danger">
                                                    {{ v$.typeForm.newTypeValue.$errors[0].$message }}
                                                </div>
                                                
                                            </a-flex>
                                    </div>
                                    <div class="modal-footer pb-0">
                                        <BButton variant="secondary" @click="typesModalOpen = false">Annuler</BButton>
                                        <BButton variant="primary" @click="addType"><PhPlusCircle :size="15" />Créer</BButton>
                                    </div>
                                </BModal>
                                </div>
                            </div>
                            </a-flex>
                        </a-flex>
                    </BCardBody>
                    <BCardFooter>
                        <a-flex  justify="space-between" align="center" gap="middle">
                            <a-button type="primary" style="background-color: var(--bs-form-valid-color);"><PhLock :size="23" weight="duotone" />Arrivage entierement Reçu</a-button>
                            <a-button type="primary"> <PhDropboxLogo :size="23" weight="duotone" />Receptionner</a-button>
                            
                        </a-flex>
                    </BCardFooter>
                    </form>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>