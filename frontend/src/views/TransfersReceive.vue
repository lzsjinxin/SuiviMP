<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import {computed, ref} from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";
export default {
    name: "TransfersTransfer",
    components: {
        Layout, pageheader
    },
    setup() {
        const selectLocations = ref([])

        const selectedLocation = ref(null)

        const materials = ref([])

        const loading = ref(false)

        const noDataFound = ref(false)

        const selectedRowKeys = ref([])

        const selectedMaterials = ref([])

        const rowSelection = computed(() => {
          return {
            selectedRowKeys: selectedRowKeys.value,
            onChange: (newSelectedRowKeys) => {
              // console.log('selectedRowKeys changed: ', newSelectedRowKeys)
              selectedRowKeys.value = newSelectedRowKeys
            },
            getCheckboxProps: (record) => ({
              // Column configuration not to be checked
              disabled: record.name === 'Disabled User',
              name: record.name,
            }),
          }
        })


        const filterOption = (input, option) => {
          return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0
        }

        const rules = {
            selectedLocation: {
                required: helpers.withMessage('Veuillez Selectionner l\'emplacement', required)
            },
        }

        const v$ = useVuelidate(rules, { selectedLocation })

        return {
          selectLocations,
          selectedLocation,
          loading,
          filterOption,
          materials,
          noDataFound,
          selectedMaterials,
          rowSelection,
          selectedRowKeys,
          v$
        }
    },
    methods: {
        async fetchLocations(){
          await axios.get("/api/locations/").then(response =>{
            this.selectLocations = response.data.map(item =>({
              value : item.id,
              label : item.name+' - '+item.adresse
            }))
          }).catch(error => {
            console.error(error)
            alert("Erreur lors du chargement des données")
          })
        },
        async fetchMaterials(){
        this.loading = true;
        await axios.get("/api/materials/location/"+this.selectedLocation+"/transfered").then(response =>{
          this.materials = response.data
          this.loading = false;
        }).catch(error => {
          console.error(error)
          alert("Erreur lors du chargement des données")
        })
      },
        async ValidateGlobalForm() {
        console.log(this.selectedRowKeys)
          return Swal.fire({ // Return the Promise
          title: 'Récéption',
          html: `Voulez-vous vraiment Réceptionner ces MP`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Oui',
          cancelButtonText: 'Annuler'
        }).then(async (result) => { // Make the callback async
          if (result.isConfirmed) {
            await this.submitForm(); // Await the submission
          }
          return result.isConfirmed; // Return whether the user confirmed
        });
      },
        async submitForm(){
        try {
          const payload = {
            materials : this.selectedRowKeys,
            user : 1 /**TODO: Remove the 1 and add loggedin user ID**/
          }
          const response = await axios.post('/api/materials/receive',payload,{
            headers: {
              'Content-Type': 'application/json',
            }
          })
          if (response.status === 201) {
            this.loading = false
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

            await Toast.fire({
              icon: 'success',
              title: 'Materiaux Réceptionnés',
            }).then(() => {
              this.$router.push("/mp/transfers")
            })
          }
        } catch (error) {
          console.error('Error submitting form:', error)

          let errorMessage = "Une erreur s'est produite"
          if (error.response) {
            errorMessage = error.response.data.message ||
                `Erreur ${error.response.status}: ${error.response.statusText}`
          } else if (error.request) {
            errorMessage = "Pas de réponse du serveur"
          }

          Swal.fire({
            title: 'Erreur',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'OK'
          })

          throw error
        }
      },
    },
  mounted() {
      this.fetchLocations()
  }
}
</script>

<template>
    <Layout>
        <pageheader title="Réceptionner MP" secondaryPageHeader="Transféres" pageTitle="Matières Premieres" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                          <a-select
                              v-model:value="selectedLocation"
                              show-search
                              placeholder="Selectionner la Provenance du MP"
                              style="width: 100%;text-align: center;"
                              @change="fetchMaterials"
                              :status="v$.selectedLocation.$error ? 'error' : ''"
                              :options="selectLocations"
                              :filter-option="filterOption"
                          >
                          </a-select>
                          <div v-if="v$.selectedLocation.$error" class="text-danger">
                            {{ v$.selectedLocation.$errors[0].$message }}
                          </div>
                          <div v-if="loading" class="mt-3">
                            <a-flex justify="center" align="center" wrap="wrap" gap="mi ddle">
                              <BSpinner variant="primary" style="width: 3rem; height: 3rem" label="Chargement..." class="mx-1" />
                            </a-flex>
                          </div>
                          <a-table
                              :dataSource="materials"
                              :row-selection="rowSelection"
                              :columns="[
                                { title: 'Lot', dataIndex: 'batch', key: 'batch' },
                                { title: 'Type', dataIndex: 'type', key: 'type' },
                                { title: 'Date Arrivage', dataIndex: 'arrivalDate', key: 'arrivalDate' },
                                { title: 'Num', dataIndex: 'num', key: 'num' },
                                { title: 'Qté Réstante', dataIndex: 'qty', key: 'qty' },
                            ]"
                              :pagination="false"
                              rowKey="id"
                          >
                            <template #bodyCell="{ column, record }">
                              <template v-if="column.key === 'batch'">
                              {{record.material_batch.batch_number}}
                              </template>
                              <template v-if="column.key === 'type'">
                                {{record.material_type.type}}
                              </template>
                              <template v-if="column.key === 'arrivalDate'">
                                {{record.arrival.date}}
                              </template>
                              <template v-if="column.key === 'qty'">
                                {{record.qty}} {{record.unit.title}}
                              </template>
                            </template>
                          </a-table>
                        </a-flex>  
                    </BCardBody>
                  <BCardFooter v-if="this.selectedRowKeys.length!=0">
                    <a-flex justify="center" align="center" wrap="wrap" gap="mi ddle">
                      <a-button type="primary" @click="ValidateGlobalForm">
                        <PhArrowLeft :size="23" /> Réceptionner
                      </a-button>
                    </a-flex>
                  </BCardFooter>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>