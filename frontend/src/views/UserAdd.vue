<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import { ref } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";
export default {
    name: "UserAdd",
    components: {
        Layout, pageheader
    },
    setup() {
        const fname = ref(null)

        const name = ref(null)

        const password = ref(null)

        const selectDepartments = ref(null)

        const selectedDepartment = ref(null)

        const filterOption = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
            };
        
        const rules = {
            fname: {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },
            name : {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },
          password : {
            required: helpers.withMessage('Veuillez Remplir le champ', required)
          },
            selectedDepartment : {
                required: helpers.withMessage('Le Departement requis', required)
            }
        }
        
        const v$ = useVuelidate(rules, { fname, name, selectDepartments, selectedDepartment, password  })
        
        return { 
            fname,
            name,
            password,
            selectDepartments, 
            selectedDepartment,
            filterOption,
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
                html: `Voulez-vous vraiment Ajouter un Utilisateur `,
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
                fname : this.fname,
                name : this.name,
                id_dept : this.selectedDepartment,
                password : this.password,
                user : 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
            }
            const response = await axios.post('/api/users/',payload,{
        headers: {
          'Content-Type': 'application/json',
          // Add any auth headers if needed
          // 'Authorization': `Bearer ${yourToken}`
        }
      });
      if (response.status === 201) {
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
                title: 'Utilisateur Crée',
            }).then(() => {
                //Redirect
                this.$router.push("/users")
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
        fetchDepartment(){
            axios.get('/api/departments')
        .then(response => {
        this.selectDepartments = response.data.map(item => ({
            value: item.id,
            label: item.name
            }));
        })
        .catch(error => {
            console.error(error);
        });
        },
    },
    mounted(){
        this.fetchDepartment()
    }
}
</script>

<template>
    <Layout>
        <pageheader title="Ajouter un Emplacement" pageTitle="Emplacements" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                            <a-divider><PhUsersThree  :size="23" weight="duotone" /> Departement</a-divider>
                            <label for="">Departement</label>
                                <a-select
                                v-model:value="selectedDepartment"
                                show-search
                                placeholder="Selectionner un Department"
                                style="width: 100%;text-align: center;"
                                :status="v$.selectedDepartment.$error ? 'error' : ''"
                                :options="selectDepartments"
                                :filter-option="filterOption"
                                >
                                </a-select>
                                 <div v-if="v$.selectedDepartment.$error" class="text-danger">
                                    {{ v$.selectedDepartment.$errors[0].$message }}
                                </div>
                            <a-divider><PhUser :size="23" weight="duotone" /> Utilisateur</a-divider>
                            <input class="form-control" v-model="fname" :class="{ 'is-invalid': v$.fname.$error }" placeholder="Prénom" />
                            <div 
                                v-for="error in v$.fname.$errors" 
                                :key="error.$uid"
                                class="invalid-feedback"
                            >
                                {{ error.$message }}
                            </div>
                            <input class="form-control" v-model="name" :class="{ 'is-invalid': v$.name.$error }" placeholder="Nom" />
                            <div 
                                v-for="error in v$.name.$errors" 
                                :key="error.$uid"
                                class="invalid-feedback"
                            >
                                {{ error.$message }}
                            </div>
                          <a-input-password visibilityToggle   v-model:value="password" :class="{ 'is-invalid': v$.password.$error }" placeholder="Mot de Passe" ></a-input-password>
                          <div
                              v-for="error in v$.password.$errors"
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
                                <PhPlusCircle :size="23" /> Ajouter
                            </a-button>
                        </a-flex>  
                    </BCardFooter>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>