<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import { ref } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";
export default {
    name: "UnitModify",
    components: {
        Layout, pageheader
    },
    setup() {
        const unit = ref(null)
        
        const rules = {
            unit: {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            }
        }
        
        const v$ = useVuelidate(rules, { unit })
        
        return { 
            unit,
            v$ 
        }
    },
    methods: {
        async ValidateGlobalForm() {
            this.v$.$touch() // Mark all fields as touched
            const isFormValid = await this.v$.$validate()
            
            
            
            if (!isFormValid) {
                return;
            }
              Swal.fire({
                title: 'Modification',
                html: `Voulez-vous vraiment Modifier cette unité avec le libellé suivant : 
                <div class="row mt-3">
                <div class="col">Libellé</div>
                <div class="col">${this.unit}</div>
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
                title : this.unit,
                user : 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
            }
            const response = await axios.patch('/api/units/'+this.$route.params.id,payload,{
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
                title: 'Unité Modifié',
            }).then(() => {
                //Redirect
                this.$router.push("/units")
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
             axios.get('/api/units/'+this.$route.params.id)
                .then(response => {
                    this.unit = response.data[0].title
                })
                .catch(error => {
                    console.error(error)
                })
        }
    },
    mounted() {
        this.fetchData()
    },
}
</script>

<template>
    <Layout>
        <pageheader :title="'Modifier Unité N° ' + this.$route.params.id" pageTitle="Unités" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                            <input class="form-control" v-model="unit" :class="{ 'is-invalid': v$.unit.$error }" placeholder="Libellé" />
                            <div 
                                v-for="error in v$.unit.$errors" 
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