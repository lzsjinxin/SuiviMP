<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import { ref } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";
export default {
    name: "TierAdd",
    components: {
        Layout, pageheader
    },
    setup() {
        const title = ref(null)
        
        const selectSeries = ref(null)

        const selectedSeries = ref(null)

        const selectMP = ref(null)

        const selectedMP = ref([])  

        const filterOption = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
            };
        
        const rules = {
            title: {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },
            selectedSeries : {
                required: helpers.withMessage('La Famille requis', required)
            }, 
            selectedMP : {
                required: helpers.withMessage('Les Matières Premieres sont requis', required)
            },
            
        }
        
        const v$ = useVuelidate(rules, { title,selectMP, selectedMP, selectSeries, selectedSeries })
        
        return { 
            title,
            selectMP, 
            selectedMP,
            selectSeries, 
            selectedSeries,
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
                html: `Voulez-vous vraiment Modifier ce Produit`,
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
                series : this.selectedSeries,
                title : this.title,
                materials : this.selectedMP,
                user : 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
            }
            const response = await axios.patch('/api/products/'+this.$route.params.id,payload,{
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
                title: 'Produit Modifié',
            }).then(() => {
                //Redirect
                this.$router.push("/products")
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
      
      throw error;
            }
        },
        fetchProduct(){
        axios.get('/api/products/'+this.$route.params.id)
                .then(response => {
                    
                    this.selectedSeries = response.data[0].id_series
                    this.title = response.data[0].title
                    this.selectedMP=  response.data[0].product_materials.map(material => material.id_material)
                })
                .catch(error => {
                    console.error(error)
                })
        },
        fetchSeries(){
            axios.get('/api/tiers/series')
                .then(response => {
                    this.selectSeries = response.data.map(item => ({
                        value: item.id,
                        label: item.series
                    }));
                })
                .catch(error => {
                    console.error(error)
                })
        },
        fetchMP(){
            //Get only available MP
        axios.get('/api/materials/available')
                .then(response => {
                    this.selectMP = response.data.map(item => ({
                        value: item.id,
                        label: item.material_batch.batch_number + " - " +item.num
                    }));
                })
                .catch(error => {
                    console.error(error)
                })
        }
    },
    mounted(){
        this.fetchSeries()
        this.fetchMP()
        this.fetchProduct()
    }
}
</script>
<template>
    <Layout>
        <pageheader :title='"Modifier Produit N° " + this.$route.params.id' pageTitle="Tiers" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                            <a-divider><PhTreeStructure  :size="23" weight="duotone" />Famille</a-divider>
                            <label for="">Famille</label>
                                <a-select
                                v-model:value="selectedSeries"
                                show-search
                                placeholder="Selectionner une Famille"
                                style="width: 100%;text-align: center;"
                                :status="v$.selectedSeries.$error ? 'error' : ''"
                                :options="selectSeries"
                                :filter-option="filterOption"
                                >
                                </a-select>
                                 <div v-if="v$.selectedSeries.$error" class="text-danger">
                                    {{ v$.selectedSeries.$errors[0].$message }}
                                </div>
                            <a-divider><PhPackage :size="23" weight="duotone" /> Produit</a-divider>
                            <input class="form-control text-center" v-model="title" :class="{ 'is-invalid': v$.title.$error }" placeholder="Libellé du Produit" />
                            <div 
                                v-for="error in v$.title.$errors" 
                                :key="error.$uid"
                                class="text-danger text-center"
                            >
                                {{ error.$message }}
                            </div>
                              <a-divider><PhAtom :size="23" weight="duotone" /> Matières Premieres</a-divider>
                            <a-select
                                v-model:value="selectedMP"
                                show-search
                                mode="multiple"
                                placeholder="Selectionner les Matières Premieres Associès avec le Produit"
                                style="width: 100%;text-align: center;"
                                :status="v$.selectedMP.$error ? 'error' : ''"
                                :options="selectMP"
                                :filter-option="filterOption"
                                >
                            </a-select>
                                <div v-if="v$.selectedMP.$error" class="text-danger">
                                {{ v$.selectedMP.$errors[0].$message }}
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