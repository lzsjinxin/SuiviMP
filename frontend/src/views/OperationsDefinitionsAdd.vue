<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import { ref } from 'vue'
import { useVuelidate } from '@vuelidate/core'
// import { required, helpers } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";
export default {
    name: "OperationDefinitionsAdd",
    components: {
        Layout, pageheader
    },
    setup() {

        const materialTypes = ref([])
        const operations = ref([])
        const globalLoading = ref(true)
        const saving = ref(false)
        const rules = {
        }
        
        const v$ = useVuelidate(rules)
        
        return { 
            materialTypes,
            globalLoading,
            operations,
            saving,
            v$ 
        }
    },
    methods: {
        fetchData(){
            axios.get('/api/materialtypes/operationdefs')
        .then(response => {
        this.materialTypes = response.data;
        // Convert time from seconds to minutes for display
        this.materialTypes.forEach(materialType => {
            materialType.operation_definition.forEach(operation => {
                if (operation.time_expected) {
                    operation.time_expected = Math.round(operation.time_expected / 60);
                }
            });
        });
        this.globalLoading = false
        })
        .catch(error => {
            console.error(error);
        });
        },
        // Remove operation definition (from database or frontend only for new items)
        async removeSeriesRow(operationId) {
            try {
                // Find the operation to check if it's new
                let operationToDelete = null;
                let isNewOperation = false;

                this.materialTypes.forEach(materialType => {
                    const operation = materialType.operation_definition.find(op => op.id === operationId);
                    if (operation) {
                        operationToDelete = operation;
                        isNewOperation = operation.isNew || String(operation.id).startsWith('temp_');
                    }
                });

                if (!operationToDelete) {
                    console.error('Operation not found');
                    return;
                }
                if (isNewOperation) {
                        // For new operations, just remove from frontend
                        this.materialTypes.forEach(materialType => {
                            materialType.operation_definition = materialType.operation_definition.filter(
                                op => op.id !== operationId
                            );
                        });
                        return;
                    }

                const result = await Swal.fire({
                    title: 'Confirmation',
                    text: 'Voulez-vous vraiment supprimer cette opération ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Supprimer',
                    cancelButtonText: 'Annuler'
                });

                if (result.isConfirmed) {
                        // For existing operations, delete from database
                        const response = await axios.delete(`/api/operationdef/${operationId}`);
                        
                        if (response.status === 200 || response.status === 204) {
                            // Remove from local state
                            this.materialTypes.forEach(materialType => {
                                materialType.operation_definition = materialType.operation_definition.filter(
                                    op => op.id !== operationId
                                );
                            });

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'Opération supprimée avec succès'
                            });
                    }
                }
            } catch (error) {
                console.error('Error deleting operation:', error);
                
                let errorMessage = "Erreur lors de la suppression";
                if (error.response) {
                    errorMessage = error.response.data.message || 
                                 `Erreur ${error.response.status}: ${error.response.data}`;
                }

                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        // Add new operation definition row
        addNewOperationRow(materialTypeId) {
            const materialType = this.materialTypes.find(mt => mt.id === materialTypeId);
            if (materialType) {
                const newOperation = {
                    id: `temp_${Date.now()}`, // Temporary ID for new rows
                    name: '',
                    qty_needed: 1,
                    time_expected: 1, // Default 1 minute
                    material_type_id: materialTypeId,
                    isNew: true // Flag to identify new rows
                };
                materialType.operation_definition.push(newOperation);
            }
        },
        // Validate operation data
        validateOperationData() {
            let isValid = true;
            const errors = [];

            this.materialTypes.forEach(materialType => {
                materialType.operation_definition.forEach(operation => {
                    if (!operation.name || operation.name.trim() === '') {
                        isValid = false;
                        errors.push('Tous les noms d\'opération sont requis');
                    } else if (operation.name.trim().length < 2) {
                        isValid = false;
                        errors.push('Les noms d\'opération doivent contenir au moins 2 caractères');
                    }

                    if (!operation.qty_needed || operation.qty_needed <= 0) {
                        isValid = false;
                        errors.push('Toutes les quantités sont requises et doivent être supérieures à 0');
                    }

                    if (!operation.time_expected || operation.time_expected <= 0) {
                        isValid = false;
                        errors.push('Toutes les durées sont requises et doivent être supérieures à 0');
                    }
                });
            });

            return { isValid, errors: [...new Set(errors)] }; // Remove duplicates
        },
        // Save all operation definitions
        async saveOperationDefinitions() {
            const validation = this.validateOperationData();
            
            if (!validation.isValid) {
                Swal.fire({
                    title: 'Erreurs de validation',
                    html: validation.errors.map(error => `• ${error}`).join('<br>'),
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            try {
                this.saving = true;

                // Collect all operations to save (new and updated)
                const operationsToSave = [];
                const operationsToUpdate = [];

                this.materialTypes.forEach(materialType => {
                    materialType.operation_definition.forEach(operation => {
                        const operationData = {
                            name: operation.name.trim(),
                            qty_needed: operation.qty_needed,
                            time_expected : Math.round(operation.time_expected*60),
                            material_type_id: materialType.id,
                            user: 1 // TODO: Replace with logged-in user ID
                        };

                        if (operation.isNew) {
                            operationsToSave.push(operationData);
                        } else {
                            operationsToUpdate.push({
                                ...operationData,
                                id: operation.id
                            });
                        }
                    });
                });

                // Save new operations
                const savePromises = operationsToSave.map(operation => 
                    axios.post('/api/operationdef/', operation)
                );

                // Update existing operations
                const updatePromises = operationsToUpdate.map(operation => 
                    axios.patch(`/api/operationdef/${operation.id}`, operation)
                );

                await Promise.all([...savePromises, ...updatePromises]);

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'center',
                    iconColor: 'white',
                    customClass: {
                        popup: 'colored-toast',
                    },
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });

                await Toast.fire({
                    icon: 'success',
                    title: 'Définitions d\'opérations sauvegardées avec succès'
                });

                // Refresh data to get updated IDs
                this.fetchData();

            } catch (error) {
                console.error('Error saving operation definitions:', error);
                
                let errorMessage = "Erreur lors de la sauvegarde";
                if (error.response) {
                    errorMessage = error.response.data.message || 
                                 `Erreur ${error.response.status}: ${error.response.statusText}`;
                }

                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } finally {
                this.saving = false;
            }
        }
    },
    mounted(){
        this.fetchData();
    }
}
</script>

<template>
    <Layout>
        <pageheader title="Ajouter des definitons d'operations" pageTitle="Operations" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody>
                        <a-flex justify="center" align="center" v-if="globalLoading"> <a-spin size="large"  /></a-flex>
                        
                        <div  v-if="!globalLoading" >
                            <a-divider><PhTag  :size="23" weight="duotone" />Type de Matière Premiere</a-divider>
                            <BTabs content-class="mt-3" pills justify  justified>
                                <a-divider><PhHammer  :size="23" weight="duotone" />Liste des Operations</a-divider>
                                <BTab v-for="materialtype in materialTypes" :key="materialtype.id" :title="materialtype.type">
                                    <div class="mb-3">
                                        <a-button 
                                            type="primary" 
                                            @click="addNewOperationRow(materialtype.id)"
                                        >
                                            <PhPlus  :size="16" weight="duotone" />Ajouter une nouvelle opération
                                        </a-button>
                                    </div>
                                    
                                    <a-table 
                            :dataSource="materialtype.operation_definition" 
                            :columns="[
                                { title: 'Operation', dataIndex: 'operation', key: 'operation' },
                                { title: 'Durée prévue', dataIndex: 'time', key: 'time' },
                                { title: 'Qté Requise', dataIndex: 'qty', key: 'qty' },
                                { title: 'Action', key: 'action' },
                            ]" 
                            :pagination="false"
                            size="small"
                            :rowKey="record => record.id"
                        ><template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'operation'">
                                <div>
                                    <a-input 
                                        v-model:value="record.name" 
                                        placeholder="Operation" 
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
                            <template v-if="column.key === 'time'">
                                <div>
                                    <a-input-number v-model:value="record.time_expected" 
                                    min="0" 
                                    defaultValue="1" 
                                    placeholder="Durée prévue"
                                    :status="!record.time_expected ? 'error' : ''"
                                    @input="v$.$touch()"
                                    addonAfter="Minutes"></a-input-number>
                                        <div v-if="!record.time_expected " class="text-danger small mt-1">
                                            La Durée est requise
                                        </div>
                                    </div>
                            </template>
                             <template v-if="column.key === 'qty'">
                                <div>
                                    <a-input-number v-model:value="record.qty_needed" 
                                    min="0" 
                                    max="100" 
                                    defaultValue="1" 
                                    placeholder="Qté"
                                    :status="!record.qty_needed ? 'error' : ''"
                                    @input="v$.$touch()"></a-input-number>
                                        <div v-if="!record.qty_needed " class="text-danger small mt-1">
                                            La quantité est requise
                                        </div>
                                    </div>
                            </template>
                            <template v-if="column.key === 'action'">
                                    <a-button 
                                        type="primary" 
                                        danger 
                                        shape="circle" 
                                        @click="removeSeriesRow(record.id)"
                                    >
                                        <template #icon><PhTrash :size="15" /></template>
                                    </a-button>
                                </template>
                        </template>
                        </a-table>
                                </BTab>
                            </BTabs>
                        </div>
                        
                    </BCardBody>
                    <BCardFooter>
                        <div class="d-flex justify-content-end">
                            <a-button 
                                type="primary" 
                                size="large"
                                :loading="saving"
                                @click="saveOperationDefinitions"
                            >
                                <div v-if="saving">
                                    <BSpinner small />
                                    Sauvegarde en cours
                                </div>
                                <div v-else>
                                     <PhFloppyDisk  :size="18" weight="duotone" />Sauvegarde
                                </div>
                                
                            </a-button>
                        </div>
                    </BCardFooter>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>