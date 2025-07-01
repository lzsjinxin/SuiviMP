<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import { ref, computed } from 'vue'
import axios from 'axios'
import Swal from "sweetalert2";
import {FormWizard, TabContent} from 'vue3-form-wizard'
import 'vue3-form-wizard/dist/style.css'
import draggable from 'vuedraggable'

export default {
  name: "ModifyOperationDefinitionsAllocation",
  components: {
    Layout,
    pageheader,
    FormWizard,
    TabContent,
    draggable
  },
  setup() {
    const loading = ref(true)
    const errorMsg = ref("")
    const fetchedOperations = ref([])
    const operations = ref([])
    const selectedRowKeys = ref([])

    const selectedOperations = ref([])

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

    return {
      filterOption,
      loading,
      errorMsg,
      operations,
      selectedRowKeys,
      rowSelection,
      selectedOperations,
      fetchedOperations
    }
  },
  methods: {
    async fetchProductOperations() {
      //Get All Operations
      await axios.get("/api/operationdef/materialtype/"+this.$route.params.id)
          .then(response => {
            //Get Selected Operations
            this.operations = response.data
            this.fetchSelectedProductOperations()

          })
          .catch(error => {
            console.error(error)
            alert("Erreur lors du chargement des données")
          })
    },
    async fetchSelectedProductOperations(){
      await axios.get('/api/productoperations/product/'+this.$route.params.id)
          .then(response => {
            const ProductStatus = response.data[0].product.product_status.status
            if(ProductStatus != "Operations Affected"){
              this.$router.push("/403");
            }
            //Get Selected Operations
            this.fetchedOperations = response.data
            this.selectedRowKeys = this.fetchedOperations.map(op =>op.id_operations)
            this.loading = false
          })
          .catch(error => {
            console.error(error)
            alert("Erreur lors du chargement des données")
          })
    },
    setLoading(value){
      this.loading = value
      // console.log("loading is : "+this.loading)
    },
    async ValidateGlobalForm() {
      return Swal.fire({ // Return the Promise
        title: 'Modification',
        html: `Voulez-vous vraiment Modifier l'Affectation de ces Operations a ce Produit`,
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
          id_product : this.$route.params.id,
          operations : this.selectedOperations,
          user : 1 /**TODO: Remove the 1 and add loggedin user ID**/
        }
        const response = await axios.patch('/api/productoperations/'+this.$route.params.id,payload,{
          headers: {
            'Content-Type': 'application/json',
          }
        })
        if (response.status === 200) {
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
            title: 'Operations Modifiés',
          }).then(() => {
            this.$router.push("/products")
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
    async wizardLogic() {
      const currentTabIndex = this.$refs.wizard.activeTabIndex

      switch(currentTabIndex){
        case 0:
          if(this.selectedRowKeys.length !== 0){
            this.errorMsg = ""
            this.selectedOperations = this.operations.filter(op =>
                this.selectedRowKeys.includes(op.id)
            );
            return true;
          }else{
            this.errorMsg = "Veuillez Choisir au moin une operation"
         return false ;
          }
        case 1:
          return await this.ValidateGlobalForm();
      }
    }
  },
  mounted(){
    this.fetchProductOperations()
  }
}
</script>

<template>
  <Layout>
    <pageheader :title="'Modifier les Affectations des operations pour le Produit N° ' +this.$route.params.id" pageTitle="Operations" />

    <BRow>
      <BCol sm="12">
        <BCard no-body>
          <BCardBody style="width: 50%; margin: auto;">
            <form-wizard
                ref="wizard"
                @on-loading="setLoading"
                color="#04A9F5"
                back-button-text="Précédent"
                next-button-text="Suivant"
                finish-button-text="Finir">
              <tab-content title="Affectation des Operations" icon="fa fa-book-open"  :before-change="wizardLogic">
                <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                  <a-table
                      :dataSource="operations"
                      :row-selection="rowSelection"
                      :columns="[
                                { title: 'Operation', dataIndex: 'name', key: 'name' },
                                { title: 'Durée prévue', dataIndex: 'time_expected', key: 'time_expected' },
                                { title: 'Qté Requise', dataIndex: 'qty_needed', key: 'qty_needed' },
                            ]"
                      :pagination="false"
                      size="small"
                      rowKey="id"
                  >
                    <template #bodyCell="{ column, record }">
                      <template v-if="column.key === 'time_expected'">
                        {{Math.round(parseInt(record.time_expected)/60)}} min
                      </template>
                    </template>
                  </a-table>
                </a-flex>
              </tab-content>
              <tab-content title="Ordre des Operations" icon="fa fa-list-ol"  :before-change="wizardLogic">
                <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                  <table class="table">
                    <thead>
                    <tr>
                      <th>Operation</th>
                      <th>Ordre</th>
                    </tr>
                    </thead>
                    <draggable
                        v-model="selectedOperations"
                        tag="tbody"
                        group="people"
                        @start="drag=true"
                        @end="drag=false"
                        item-key="id">
                      <template #item="{element, index}">
                        <tr style="cursor: grab">
                          <td>
                            {{element.name}}
                          </td>
                          <td>
                            {{index+1}}
                            <i class="fas fa-arrows-alt-v" style="margin-left:90%" aria-hidden="true"></i>
                          </td>
                        </tr>
                      </template>
                    </draggable>
                  </table>

                </a-flex>
              </tab-content>
              <div v-if="loading" class="mt-3">
                <a-flex justify="center" align="center" wrap="wrap" gap="mi ddle">
                  <BSpinner variant="primary" style="width: 3rem; height: 3rem" label="Chargement..." class="mx-1" />
                </a-flex>
              </div>
              <div v-if="errorMsg">
                <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                  <span class="text-danger text-center">{{ errorMsg }}</span>
                </a-flex>
              </div>
            </form-wizard>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>