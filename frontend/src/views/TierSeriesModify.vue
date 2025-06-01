<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import { ref, reactive } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers, minLength } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";

export default {
    name: "TierSeriesModify",
    components: {
        Layout, pageheader
    },
    setup() {
        const series = ref([])
        const selectTiers = ref(null)   
        const selectedTier = ref(null)
        const lignes = ref(1)

        const filterOption = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        // Create reactive state object for validation
        const state = reactive({
            selectedTier,
            series
        });

        const rules = {
            selectedTier: {
                required: helpers.withMessage('Le Tier est requis', required)
            },
            series: {
                required: helpers.withMessage('Au moins une série est requise', required),
                minLength: helpers.withMessage('Au moins une série est requise', minLength(1)),
                $each: {
                    name: {
                        required: helpers.withMessage('Le libellé est requis', required),
                        minLength: helpers.withMessage('Le libellé doit contenir au moins 2 caractères', minLength(2))
                    }
                }
            }
        }
        
        const v$ = useVuelidate(rules, state)
        
        return { 
            series,
            selectTiers, 
            selectedTier,
            lignes,
            filterOption,
            v$ 
        }
    },
    methods: {
        fetchTiers() {
            axios.get('/api/tiers/clients')
                .then(response => {
                    this.selectTiers = response.data.map(item => ({
                        value: item.id,
                        label: item.name
                    }));
                })
                .catch(error => {
                    console.error(error)
                })
        },

        addSeriesRows(count = 1) {
            for (let i = 0; i < count; i++) {
                this.series.push({
                    id: Date.now() + Math.random(), // Add unique ID for each row
                    name: null,
                });
            }
        },

        removeSeriesRow(id) {
            const index = this.series.findIndex(m => m.id === id);
            if (index !== -1) {
                this.series.splice(index, 1);
            }
        },

        // Helper method to get validation state for a specific series row
        getSeriesValidationState(index, field) {
            try {
                if (this.v$.series.$each && this.v$.series.$each.$response && this.v$.series.$each.$response.$data) {
                    const itemValidation = this.v$.series.$each.$response.$data[index];
                    if (itemValidation && itemValidation[field]) {
                        return itemValidation[field].$invalid ? 'error' : '';
                    }
                }
                return '';
            } catch (error) {
                console.log('Validation state error:', error);
                return '';
            }
        },

        // Helper method to get validation message for a specific series row
        getSeriesValidationMessage(index, field) {
            try {
                if (this.v$.series.$each && this.v$.series.$each.$response && this.v$.series.$each.$response.$data) {
                    const itemValidation = this.v$.series.$each.$response.$data[index];
                    if (itemValidation && itemValidation[field] && itemValidation[field].$invalid) {
                        return itemValidation[field].$errors && itemValidation[field].$errors.length > 0 
                            ? itemValidation[field].$errors[0].$message 
                            : 'Erreur de validation';
                    }
                }
                return '';
            } catch (error) {
                console.log('Validation message error:', error);
                return '';
            }
        },

        // Check if a specific series row has validation errors
        hasSeriesRowError(index) {
            try {
                if (this.v$.series.$each && this.v$.series.$each.$response && this.v$.series.$each.$response.$data) {
                    const itemValidation = this.v$.series.$each.$response.$data[index];
                    return itemValidation && itemValidation.$invalid;
                }
                return false;
            } catch (error) {
                return false;
            }
        },
        fetchData(){
                axios.get('/api/tiers/series/'+this.$route.params.id)
                .then(response => {
                    this.selectedTier = response.data[0].id_tier
                    this.series.push({
                    id: Date.now() + Math.random(), // Add unique ID for each row
                    name: response.data[0].series
                    })
                })
                .catch(error => {
                    console.error(error)
                })
        },

        async ValidateGlobalForm() {
            console.log('Starting validation...');
            
            // Validate all fields
            const isFormValid = await this.v$.$validate();
            
            console.log('Form validation result:', isFormValid);
            console.log('All validation errors:', this.v$.$errors);
            
            if (!isFormValid) {
                // Show specific error messages
                const errorMessages = [];
                
                if (this.v$.selectedTier.$error) {
                    errorMessages.push('- ' + this.v$.selectedTier.$errors[0].$message);
                }
                
                if (this.v$.series.$error) {
                    errorMessages.push('- Erreurs dans le tableau des séries');
                }
                
                if (errorMessages.length > 0) {
                    Swal.fire({
                        title: 'Erreur de validation',
                        html: 'Veuillez corriger les erreurs suivantes:<br/>' + errorMessages.join('<br/>'),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                return;
            }

            // Check if series array is empty
            if (this.series.length === 0) {
                Swal.fire({
                    title: 'Erreur',
                    text: 'Veuillez ajouter au moins une série.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Check if all series have names
            const hasEmptyNames = this.series.some(item => !item.name || item.name.trim() === '');
            if (hasEmptyNames) {
                Swal.fire({
                    title: 'Erreur',
                    text: 'le libellé doit être renseigné.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            console.log('All validations passed, showing confirmation...');
            
            Swal.fire({
                title: 'Modification',
                html: `Voulez-vous vraiment Modifier cette Famille `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitForm();
                }
            });
        },

        async submitForm() {
            try {
                const payload = {
                    idTier: this.selectedTier,
                    series: this.series, // Include series data in payload
                    user: 1 /**TODO: Remove the 1 and add loggedin user ID**/
                }

                const response = await axios.patch('/api/tiers/series/'+this.$route.params.id,payload,{
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
                            title: 'Famille Modifié',
                        }).then(() => {
                                 this.$router.push("/tiers/series")
                        })
                    })()
                }
            } catch (error) {
                console.error('Error submitting form:', error);

                let errorMessage = "Une erreur s'est produite";
                if (error.response) {
                    errorMessage = error.response.data.message || 
                                 `Erreur ${error.response.status}: ${error.response.statusText}`;
                } else if (error.request) {
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
    },
    mounted() {
        this.fetchTiers()
        this.fetchData()
    }
}
</script>

<template>
    <Layout>
        <pageheader :title="'Modifier la Famille N°'+this.$route.params.id" secondaryPageHeader="Familles" pageTitle="Tiers" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                             <a-divider><PhHandshake :size="23" weight="duotone" /> Tier</a-divider>
                            <label for="">Tier</label>
                                <a-select
                                v-model:value="selectedTier"
                                show-search
                                placeholder="Selectionner un Type"
                                style="width: 100%;text-align: center;"
                                :status="v$.selectedTier.$error ? 'error' : ''"
                                :options="selectTiers"
                                :filter-option="filterOption"
                                >
                                </a-select>
                                 <div v-if="v$.selectedTier.$error" class="text-danger">
                                    {{ v$.selectedTier.$errors[0].$message }}
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
        <BRow>
            <BCol sm="12">
                <BCard no-body style="width: 60%; margin: auto;">
                    <BCardBody>
                        <!-- Simplified error display -->
                        <div v-if="v$.series.$error && v$.$dirty" class="text-danger mb-3">
                            <strong>Veuillez corriger les erreurs dans le tableau.</strong>
                        </div>

                        <a-table 
                            :dataSource="series" 
                            :columns="[
                                { title: 'Libellé', dataIndex: 'name', key: 'name', className:'text-center' },
                            ]" 
                            :pagination="false"
                            size="small"
                            :rowKey="record => record.id"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'name'">
                                    <div>
                                        <a-input 
                                            v-model:value="record.name" 
                                            placeholder="Libellé"
                                            class="text-center" 
                                            :status="!record.name || record.name.trim() === '' ? 'error' : ''"
                                            @input="v$.$touch()"
                                        />
                                        <div v-if="!record.name || record.name.trim() === ''" class="text-danger small mt-1">
                                            Le libellé est requis
                                        </div>
                                        <div v-else-if="record.name && record.name.trim().length < 2" class="text-danger small mt-1">
                                            Le libellé doit contenir au moins 2 caractères
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </a-table>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>

<style scoped>
.text-danger {
    color: #ff4d4f;
}
.small {
    font-size: 12px;
}
</style>