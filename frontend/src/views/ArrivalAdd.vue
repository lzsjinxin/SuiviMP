<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import axios from "axios"
import flatPickr from "vue-flatpickr-component"
import "flatpickr/dist/flatpickr.css"
import {French} from "flatpickr/dist/l10n/fr.js"
import Swal from "sweetalert2";
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'

export default {
    name: "ArrivalAdd",
    components: {
        Layout, pageheader, flatPickr,
    },
    setup() {
        return { v$: useVuelidate() }
    },
    data() {
        return {
            arrivals: {
                vehicule_registration: null
            },
            tiers: [],
            selected: null,
            localVehicleReg: '',
            enableVehicleInput: false,
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
            arrivals: {
                date: { 
                    required: helpers.withMessage('La date est requise', required) 
                }
            },
            selected: {
                required: helpers.withMessage('Le fournisseur est requis', required)
            },
            vehicleRegistration: {
                // Only validate if checkbox is checked
                required: helpers.withMessage('Le matricule est requis', 
                    value => !this.enableVehicleInput || !!value)
            }
        }
    },
    computed: {
        vehicleRegistration: {
            get() {
                return this.arrivals.vehicule_registration || this.localVehicleReg
            },
            set(value) {
                if (this.arrivals.vehicule_registration !== null) {
                    this.arrivals.vehicule_registration = value
                } else {
                    this.localVehicleReg = value
                }
            }
        }
    },
    methods: {
        async modifyArrival() {
            // Trigger validation
            const isValid = await this.v$.$validate()
            
            if (!isValid) {
                // Show error messages
                return
            }
            
            Swal.fire({
                title: 'Ajout',
                html: `Voulez-vous vraiment Ajouter un Arrivage avec les données suivantes : 
                <div class="row mt-3">
                <div class="col">Date</div>
                <div class="col">${this.arrivals.date}</div>
                </div>
                <div class="row mt-3">
                <div class="col">Matricule</div>
                <div class="col">${!this.vehicleRegistration ? "N/A" : this.vehicleRegistration}</div>
                </div>
                <div class="row mt-3">
                <div class="col">Fournisseur</div>
                <div class="col">${this.selected.name}</div>
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
                    this.submitForm(); // Call the new submit function
                }
            });
        },
        async submitForm() {
    try {
      // First validate the form
      const isValid = await this.v$.$validate();
      if (!isValid) return; // Stop if validation fails

      // Prepare the payload
      const payload = {
        date: this.arrivals.date,
        vehicule_registration: this.enableVehicleInput ? this.vehicleRegistration : null,
        id_tier: this.selected.id,
        user: 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
      };

      // Make the POST request
      const response = await axios.post('/api/arrivals/', payload, {
        headers: {
          'Content-Type': 'application/json',
          // Add any auth headers if needed
          // 'Authorization': `Bearer ${yourToken}`
        }
      });

      // Handle success
      Swal.fire({
        title: 'Succès!',
        text: 'Arrivage Ajouté avec succès',
        icon: 'success',
        confirmButtonText: 'OK'
      }).then(() => {
        // Optional: redirect or reset form
        this.$router.push({ path: '/mp/new', query: { arrival: response.data.id } });
      });

      return response.data;
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
        fetchTiers() {
            axios.get('/api/tiers/suppliers')
                .then(response => {
                    this.tiers = response.data
                })
                .catch(error => {
                    console.error(error)
                })
        },
        toggleVehicleInput() {
            if (!this.enableVehicleInput) {
                this.vehicleRegistration = ''
            }
            // Reset validation when checkbox state changes
            this.v$.vehicleRegistration.$reset()
        },
    },
    mounted() {
        this.fetchTiers()
    },
}
</script>

<template>
    <Layout>
        <pageheader title="Ajouter un Arrivage" :pageTitle="$t('Arrivals')" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <form @submit.prevent="modifyArrival">
                    <BCardBody>
                            <div class="form-group">
                                <label class="form-label">Date d'arrivage :</label>
                                <flat-pickr 
                                    :config="flatpickrConfig"
                                    class="form-control" 
                                    :class="{ 'is-invalid': v$.arrivals.date.$error }"
                                    v-model="arrivals.date"
                                    @blur="v$.arrivals.date.$touch()"
                                ></flat-pickr>
                                <div 
                                    v-for="error in v$.arrivals.date.$errors" 
                                    :key="error.$uid"
                                    class="invalid-feedback"
                                >
                                    {{ error.$message }}
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label class="form-label">Matricule de Vehicule : </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': v$.vehicleRegistration.$error }"
                                    :placeholder="enableVehicleInput ? 'Entrez le matricule' : 'Cochez la case pour activer'"
                                    v-model="vehicleRegistration"
                                    :disabled="!enableVehicleInput"
                                    @blur="v$.vehicleRegistration.$touch()"
                                >
                                <div 
                                    v-for="error in v$.vehicleRegistration.$errors" 
                                    :key="error.$uid"
                                    class="invalid-feedback"
                                >
                                    {{ error.$message }}
                                </div>
                                <div class="form-check form-check-inline mb-2">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        v-model="enableVehicleInput"
                                        id="flexCheckDefault"
                                        @change="toggleVehicleInput"
                                    >
                                    <label class="form-check-label" for="flexCheckDefault">
                                        2 Arrivages au même temps de même provenance
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                 <label class="form-label">Fournisseur : </label>
                                <v-select 
                                    v-model="selected" 
                                    :options="tiers" 
                                    :value="selected"
                                    label="name"
                                    :class="{ 'is-invalid': v$.selected.$error }"
                                    @blur="v$.selected.$touch()"
                                />
                                <div 
                                    v-for="error in v$.selected.$errors" 
                                    :key="error.$uid"
                                    class="invalid-feedback"
                                >
                                    {{ error.$message }}
                                </div>
                            </div>
                        </BCardBody>
                        <BCardFooter>
                            <div class="d-flex justify-content-center align-items-center">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary"
                                    :disabled="v$.$invalid"
                                >
                                    <PhPlusCircle :size="32" /> Ajouter
                                </button>
                            </div>
                        </BCardFooter>
                    </form>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>