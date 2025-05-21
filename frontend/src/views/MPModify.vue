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

        const globalForm = ref({
            selectedArrival : null,
            selectedBatch : null,
            selectedLocation : null,
            selectedUnit : null,
            selectedType : null
        })
        return { v$: useVuelidate(),
            batchForm,
            typeForm,
            globalForm,
         }
    },
    data() {
        return {
            arrivals : [],
            // Batch
            batches : [],
            selectBatches: [],
            batchModalOpen : false,
            batchModalConfirmLoading : false,
            // Location
            locations : [],
            selectLocation : [],
            //units
            units : [],
            selectUnit : [],
            // Type
            types : [],
            selectTypes: [],
            typesModalOpen : false,
            //Materials
            materials: [], 
            lignes : 1,
            qtyHeaderValue: '',
            materialRowsError : false,
            materialRowsErrorRow : 0,
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
        },
        globalForm : {
            selectedArrival :{
                required: helpers.withMessage('L\'arrivage est requis', required)
            },
            selectedBatch : {
                required : helpers.withMessage('Le Lot est requis',required)
            },
            selectedLocation :{ 
                required: helpers.withMessage('L\'emplacement est requis', required)
            },
            selectedUnit : {
                required: helpers.withMessage('L\'unité est requise', required)
            },
            selectedType : {
                required: helpers.withMessage('Le Type est requis', required)
            }
        }
    }
},
    methods: {
fetchArrivals() {
    axios.get('/api/arrivals/')
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
                        const tierName = item.tier || 'Autres';
                        if (!acc[tierName]) {
                            acc[tierName] = [];
                        }
                        const formattedDate = this.formatDateTime(item.date);
                        const frenchStatus = statusMap[item.status] || item.status;
                        
                        acc[tierName].push({
                            value: item.id.toString(), 
                            label: `${item.tier} | ${formattedDate} - ${frenchStatus}` 
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
fetchUnits(){
    axios.get('/api/units')
        .then(response => {
        this.units = response.data;
        this.selectUnit = response.data.map(item => ({
            value: item.id,
            label: item.title
            }));
        })
        .catch(error => {
            console.error(error);
        });
},
fetchMaterial(){
    axios.get('/api/materials/'+this.$route.params.id)
        .then(response => {
        this.materials = response.data;
        this.globalForm.selectedArrival = ""+response.data[0].id_arrival+"";
        this.globalForm.selectedBatch = response.data[0].id_batch;
        this.globalForm.selectedLocation = response.data[0].id_current_location;
        this.globalForm.selectedUnit = response.data[0].id_unit;
        this.globalForm.selectedType = response.data[0].id_type;
        console.log();
        })
        .catch(error => {
            console.log(error);
            this.$router.push("/404")
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
enableAfterArrivalSelect(){
    if(this.globalForm.selectedArrival!==null){
        return false;
    }else{
        return true;
    }
},
addMaterialRows(count = 1) {
        for (let i = 0; i < count; i++) {
            this.materials.push({
                num: null,
                qty: null
            });
        }
    },
removeMaterialRow(id) {
    const index = this.materials.findIndex(m => m.id === id);
    if (index !== -1) {
        this.materials.splice(index, 1);
    }
},
fillAllQty() {
    this.materials.forEach((record) => {
      record.qty = this.qtyHeaderValue
    })
  },
async addBatch() {
            // Trigger validation
            const isFormValid = await this.v$.batchForm.$validate()
            
            if (!isFormValid) {
                return;
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
        this.globalForm.selectedBatch = response.data.id
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
        this.globalForm.selectedType = response.data.id
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
},
async ValidateGlobalForm(){
    const isFormValid = await this.v$.globalForm.$validate()
            
            if (!isFormValid) {
                return;
            }
            try{
            this.materialRowsError = false;
            this.materials.forEach((record) => {
                if(record.num == null || record.qty == null){
                    throw "Veuillez verifier que tous les donné sont valables";
                }
            })
            Swal.fire({
                title: 'Modification',
                html: `Voulez-vous vraiment Modifier cette MP ?:`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitMaterials();
                }
            });
            Swal
        }catch (e) {
            this.materialRowsErrorRow = e;
            this.materialRowsError = true;
            }
},
async submitMaterials(){
     try {
    // Prepare the payload
    const payload = {
    idArrival : this.globalForm.selectedArrival,
    idBatch : this.globalForm.selectedBatch,
    idLocation : this.globalForm.selectedLocation,
    idUnit : this.globalForm.selectedUnit,
    idType : this.globalForm.selectedType,
    materials : this.materials,
    user: 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
    };
            await axios.patch('/api/materials/'+this.$route.params.id,payload);
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                iconColor: 'white',
                customClass: {
                    popup: 'colored-toast',
                },
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
            })

            ;(async () => {
            await Toast.fire({
                icon: 'success',
                title: 'MP Modifié',
            }).then(() => {
                //Redirect
                this.$router.push("/mp")
            })
            })()
        } catch (error) {
            console.error('Error updating status:', error.response?.data || error.message);
        }
},
async setArrivalToReceived(id) {
        try {
            await axios.patch(`/api/arrivals/${id}/status/Received`);
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                iconColor: 'white',
                customClass: {
                    popup: 'colored-toast',
                },
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
            })

            ;(async () => {
            await Toast.fire({
                icon: 'success',
                title: 'Arrivage Récéptionné',
            }).then(() => {
                //Reload Arrivals
                this.globalForm.selectedArrival = null;
                this.fetchArrivals();
                this.materials = [];
                this.addMaterialRows(1);
            })
            })()
        } catch (error) {
            console.error('Error updating status:', error.response?.data || error.message);
        }
    }
    },
    mounted() {
        this.fetchArrivals();
        this.fetchBatches();
        this.fetchLocations();
        this.fetchTypes();
        this.addMaterialRows(1);
        this.fetchUnits();
        this.fetchMaterial();
    },
}
</script>

<template>
    <Layout>
        <pageheader :title="'Modification de Matière Premiere N° '+this.$route.params.id" pageTitle="Matières Premieres" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <form>
                    <BCardBody>
                        <a-flex vertical="vertical">
                            <a-divider><PhTruck :size="23" weight="duotone" /> Arrivage</a-divider>
                            <a-flex  justify="space-around" align="center" wrap="wrap">
                                <a-select
                                v-model:value="globalForm.selectedArrival"
                                show-search
                                placeholder="Selectionner un arrivage"
                                style="width: 100%;text-align: center;"
                                :status="v$.globalForm.selectedArrival.$error ? 'error' : ''"
                                :options="arrivals"
                                ></a-select>
                                <div v-if="v$.globalForm.selectedArrival.$error" class="text-danger">
                                    {{ v$.globalForm.selectedArrival.$errors[0].$message }}
                                </div>
                            </a-flex>
                            <a-divider><PhPackage :size="23" weight="duotone" />Matières Premieres</a-divider>
                            <a-flex  justify="space-between" align="center" wrap="wrap">
                            <!-- Lot -->
                            <div>
                                <label for="">Lot</label>
                                <a-select
                                v-model:value="globalForm.selectedBatch"
                                show-search
                                placeholder="Selectionner un Lot"
                                style="width: 100%;text-align: center;"
                                :status="v$.globalForm.selectedBatch.$error ? 'error' : ''"
                                :options="selectBatches"
                                >
                                </a-select>
                                <a-tooltip title="Ajouter un Lot" class="mt-2">
                                    <a-button type="primary" shape="circle" @click="batchModalOpen = !batchModalOpen">
                                        <PhPlus :size="15" />  
                                    </a-button>
                                </a-tooltip>
                                <div v-if="v$.globalForm.selectedBatch.$error" class="text-danger">
                                    {{ v$.globalForm.selectedBatch.$errors[0].$message }}
                                </div>
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
                                v-model:value="globalForm.selectedLocation"
                                show-search
                                placeholder="Selectionner un Emplacement"
                                style="width: 100%;text-align: center;margin-bottom: 40px;"
                                :status="v$.globalForm.selectedLocation.$error ? 'error' : ''"
                                :options="selectLocation"
                                >
                                </a-select>
                                <div v-if="v$.globalForm.selectedLocation.$error" class="text-danger">
                                    {{ v$.globalForm.selectedLocation.$errors[0].$message }}
                                </div>
                            </div>
                            <!-- Unit-->
                            <div>
                                <label for="">Unité</label>
                                <a-select
                                v-model:value="globalForm.selectedUnit"
                                show-search
                                placeholder="Selectionner une unité"
                                style="width: 100%;text-align: center;margin-bottom: 40px;"
                                :status="v$.globalForm.selectedUnit.$error ? 'error' : ''"
                                :options="selectUnit"
                                >
                                </a-select>
                                <div v-if="v$.globalForm.selectedUnit.$error" class="text-danger">
                                    {{ v$.globalForm.selectedUnit.$errors[0].$message }}
                                </div>
                            </div>
                            <!-- Type -->
                            <div>
                                <label for="">Type</label>
                                <a-select
                                v-model:value="globalForm.selectedType"
                                show-search
                                placeholder="Selectionner un Lot"
                                style="width: 100%;text-align: center;"
                                :status="v$.globalForm.selectedType.$error ? 'error' : ''"
                                :options="selectTypes"
                                >
                                </a-select>
                                
                                <a-tooltip title="Ajouter un Type" class="mt-2">
                                    <a-button type="primary" shape="circle" @click="typesModalOpen = !typesModalOpen">
                                        <PhPlus :size="15" />  
                                    </a-button>
                                </a-tooltip>
                                <div v-if="v$.globalForm.selectedType.$error" class="text-danger">
                                    {{ v$.globalForm.selectedType.$errors[0].$message }}
                                </div>
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
                        <a-flex  justify="center" align="center" gap="middle" wrap="wrap">
                            <a-button type="primary" @click = "ValidateGlobalForm"> <PhNotePencil :size="23" /> Modifier</a-button>
                            
                        </a-flex>
                    </BCardFooter>
                    </form>
                </BCard>
            </BCol>
        </BRow>
        <BRow>
            <BCol sm="12">
                <BCard no-body style="width: 60%; margin: auto;">
                    <BCardBody>
                        <div v-if="materialRowsError" class="text-danger">
                                    {{ materialRowsErrorRow }}
                                </div>
                        <a-table 
                    :dataSource="materials" 
                    :columns="[
                        { title: 'Numéro', dataIndex: 'num', key: 'num' },
                        { title: 'Quantité', dataIndex: 'qty', key: 'qty' },
                    ]" 
                    :pagination="false"
                    size="small"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key === 'num'">
                            <a-input v-model:value="record.num" placeholder="Numéro" />
                        </template>
                        <template v-if="column.key === 'qty'">
                            <a-input-number 
                                v-model:value="record.qty" 
                                :min="0" 
                                :step="0.01" 
                                :precision="2" 
                                placeholder="Quantité"
                                style="width: 100%"
                            />
                        </template>
                    </template>
                </a-table>
                    </BCardBody>
                </BCard>
            </BCol>
</BRow>
</Layout>
</template>