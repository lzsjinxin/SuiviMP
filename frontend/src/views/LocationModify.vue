<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import { ref } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";
export default {
    name: "LocationAdd",
    components: {
        Layout, pageheader
    },
    setup() {
        const location = ref(null)

        const address = ref(null)
        
        const rules = {
            location: {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },
            address : {
                required: helpers.withMessage('Adresse est requise', required)
            }
        }
        
        const v$ = useVuelidate(rules, { location, address })
        
        return { 
            location,
            address,
            v$ 
        }
    },
    methods: {
        async ValidateGlobalForm() {
            const isFormValid = await this.v$.$validate()
            this.v$.$touch() // Mark all fields as touched
            
            
            
            if (!isFormValid) {
                return;
            }
              Swal.fire({
                title: 'Ajout',
                html: `Voulez-vous vraiment Ajouter un emplacement avec le libellé suivant : 
                <div class="row mt-3">
                <div class="col">Emplacement</div>
                <div class="col">${this.location}</div>
                <div class="row mt-3">
                <div class="col">Adresse</div>
                <div class="col">${this.address}</div>
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
        async submitForm(){
            try {
                
            
            const payload = {
                name : this.location,
                adresse : this.address,
                user : 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
            }
            const response = await axios.patch('/api/locations/'+this.$route.params.id,payload,{
        headers: {
          'Content-Type': 'application/json',
          // Add any auth headers if needed
          // 'Authorization': `Bearer ${yourToken}`
        }
      });
      if (response.status === 200) {
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
                title: 'Emplacement Modifié',
            }).then(() => {
                //Redirect
                this.$router.push("/locations")
            })
            })()
      }
      } catch (error) {
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
        fetchData(){
             axios.get('/api/locations/'+this.$route.params.id)
                .then(response => {
                    this.location = response.data[0].name,
                    this.address = response.data[0].adresse
                })
                .catch(error => {
                    console.error(error)
                })
        }
    }, 
    mounted() {
        this.fetchData()
    }
}
</script>

<template>
    <Layout>
        <pageheader :title='"Modifier Emplacement N° " + this.$route.params.id' pageTitle="Emplacements" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                            <input class="form-control" v-model="location" :class="{ 'is-invalid': v$.location.$error }" placeholder="Emplacement" />
                            <div 
                                v-for="error in v$.location.$errors" 
                                :key="error.$uid"
                                class="invalid-feedback"
                            >
                                {{ error.$message }}
                            </div>
                            <input class="form-control" v-model="address" :class="{ 'is-invalid': v$.address.$error }" placeholder="Adresse" />
                            <div 
                                v-for="error in v$.address.$errors" 
                                :key="error.$uid"
                                class="invalid-feedback"
                            >
                                {{ error.$message }}
                            </div>
                        </a-flex>  
                    </BCardBody>
                    <BCardFooter>
                        <a-flex justify="center" align="center">
                            <a-button type="primary" @click="ValidateGlobalForm">
                                <PhNotePencil :size="23" /> Modifier
                            </a-button>
                        </a-flex>  
                    </BCardFooter>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>