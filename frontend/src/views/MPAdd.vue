<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import axios from "axios"
import { useVuelidate } from '@vuelidate/core'
import flatPickr from "vue-flatpickr-component"
import "flatpickr/dist/flatpickr.css"
import {French} from "flatpickr/dist/l10n/fr.js"
import Swal from "sweetalert2"
// import { required, helpers } from '@vuelidate/validators'
export default {
    name: "MPAdd",
    components: {
        Layout, pageheader, flatPickr
        // flatPickr,
    },
    setup() {
        return { v$: useVuelidate() }
    },
    data() {
        return {
            arrivals : [],
            batches : [],
            Selectbatches: [],
            selectedArrival: null,
            selectedBatch: null,
            batchModalOpen : false,
            batchModalConfirmLoading : false,
            newbatchValue : null,
            batchDate : null,
            enableBatchDatePicker : false,
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
        // return {
        //     arrivals: {
        //         date: { 
        //             required: helpers.withMessage('La date est requise', required) 
        //         }
        //     },
        //     selected: {
        //         required: helpers.withMessage('Le fournisseur est requis', required)
        //     },
        //     vehicleRegistration: {
        //         // Only validate if checkbox is checked
        //         required: helpers.withMessage('Le matricule est requis', 
        //             value => !this.enableVehicleInput || !!value)
        //     }
        // }
    },
    computed: {
        // vehicleRegistration: {
        //     get() {
        //         return this.arrivals.vehicule_registration || this.localVehicleReg
        //     },
        //     set(value) {
        //         if (this.arrivals.vehicule_registration !== null) {
        //             this.arrivals.vehicule_registration = value
        //         } else {
        //             this.localVehicleReg = value
        //         }
        //     }
        // }
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
        this.Selectbatches = response.data.map(item => ({
            value: item.id,
            label: item.batch_number
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
showbatchModal(){
   
},
addBatch(){
console.log(this.newbatchValue);
console.log(this.enableBatchDatePicker);
console.log(this.batchDate);

Swal.fire("aeazeza")

},
toggleBatchDatePicker(){
    if (!this.enableBatchDatePicker) {
                this.batchDate = ''
            }
}
//         async modifyArrival() {
//             // Trigger validation
//             const isValid = await this.v$.$validate()
            
//             if (!isValid) {
//                 // Show error messages
//                 return
//             }
            
//             Swal.fire({
//                 title: 'Ajout',
//                 html: `Voulez-vous vraiment Ajouter un Arrivage avec les données suivantes : 
//                 <div class="row mt-3">
//                 <div class="col">Date</div>
//                 <div class="col">${this.arrivals.date}</div>
//                 </div>
//                 <div class="row mt-3">
//                 <div class="col">Matricule</div>
//                 <div class="col">${!this.vehicleRegistration ? "N/A" : this.vehicleRegistration}</div>
//                 </div>
//                 <div class="row mt-3">
//                 <div class="col">Fournisseur</div>
//                 <div class="col">${this.selected.name}</div>
//                 </div>
//                 `,
//                 icon: 'question',
//                 showCancelButton: true,
//                 confirmButtonColor: '#3085d6',
//                 cancelButtonColor: '#d33',
//                 confirmButtonText: 'Oui',
//                 cancelButtonText: 'Annuler'
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     this.submitForm(); // Call the new submit function
//                 }
//             });
//         },
//         async submitForm() {
//     try {
//       // First validate the form
//       const isValid = await this.v$.$validate();
//       if (!isValid) return; // Stop if validation fails

//       // Prepare the payload
//       const payload = {
//         date: this.arrivals.date,
//         vehicule_registration: this.enableVehicleInput ? this.vehicleRegistration : null,
//         id_tier: this.selected.id,
//         user: 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
//       };

//       // Make the POST request
//       const response = await axios.post('/api/arrivals/', payload, {
//         headers: {
//           'Content-Type': 'application/json',
//           // Add any auth headers if needed
//           // 'Authorization': `Bearer ${yourToken}`
//         }
//       });

//       // Handle success
//       Swal.fire({
//         title: 'Succès!',
//         text: 'Arrivage modifié avec succès',
//         icon: 'success',
//         confirmButtonText: 'OK'
//       }).then(() => {
//         // Optional: redirect or reset form
//         this.$router.push('/arrivals');
//       });

//       return response.data;
//     } catch (error) {
//       // Handle errors
//       console.error('Error submitting form:', error);
      
//       let errorMessage = "Une erreur s'est produite";
//       if (error.response) {
//         // Server responded with error status
//         errorMessage = error.response.data.message || 
//                        `Erreur ${error.response.status}: ${error.response.statusText}`;
//       } else if (error.request) {
//         // Request was made but no response
//         errorMessage = "Pas de réponse du serveur";
//       }

//       Swal.fire({
//         title: 'Erreur',
//         text: errorMessage,
//         icon: 'error',
//         confirmButtonText: 'OK'
//       });
      
//       throw error; // Re-throw if you want to handle it elsewhere
//     }
//   },
//         fetchTiers() {
//             axios.get('/api/tiers/')
//                 .then(response => {
//                     this.tiers = response.data
//                 })
//                 .catch(error => {
//                     console.error(error)
//                 })
//         },
//         toggleVehicleInput() {
//             if (!this.enableVehicleInput) {
//                 this.vehicleRegistration = ''
//             }
//             // Reset validation when checkbox state changes
//             this.v$.vehicleRegistration.$reset()
//         },
    },
    mounted() {
        this.fetchArrivals();
        this.fetchBatches();
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
                            <div>
                                <label for="">Lot</label>
                                <a-select
                                v-model:value="selectedBatch"
                                show-search
                                placeholder="Selectionner un Lot"
                                style="width: 100%;text-align: center;"
                                :options="Selectbatches"
                                
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
                                                <a-input v-model:value="newbatchValue" placeholder="Numéro de Lot" />
                                                <a-checkbox v-model:checked="enableBatchDatePicker" class="mt-2" @change="toggleBatchDatePicker">Peut être expiré</a-checkbox>
                                                <flat-pickr 
                                                    :config="flatpickrConfig"
                                                    class="form-control" 
                                                    v-model="batchDate"
                                                    :disabled="!enableBatchDatePicker"
                                                    :placeholder="enableBatchDatePicker ? 'Entrez la date d\'expiration' : 'Cochez la case pour activer'"
                                                ></flat-pickr>
                                            </a-flex>
                                    </div>
                                    <div class="modal-footer pb-0">
                                        <BButton variant="secondary" @click="batchModalOpen = false">Annuler</BButton>
                                        <BButton variant="primary" @click="addBatch"><PhPlusCircle :size="15" />Créer</BButton>
                                    </div>
                                </BModal>
                        </div>
                            </div>
                            
                            </a-flex>
                        </a-flex>
                    </BCardBody>
                    <BCardFooter>
                        
                    </BCardFooter>
                    </form>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>