<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import {computed, ref} from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import axios from 'axios'
import Swal from "sweetalert2";
import flatPickr from "vue-flatpickr-component"
import "flatpickr/dist/flatpickr.css"
import {French} from "flatpickr/dist/l10n/fr.js"
export default {
    name: "FabricationOrdersNew",
    components: {
        Layout, pageheader, flatPickr
    },
    setup() {
        const loading = ref(false)

        const selectTiers = ref([])

        const selectedTier = ref(null)

        const orderDate = ref(null)

        const requestedDeliveryDate = ref(null)

        const priority = ref(null)

        const notes = ref(null)

        const orderNum = ref("OF")

        const products = ref([])

        const flatpickrConfig =  {
          dateFormat: "Y-m-d",
          locale: French,
          time_24hr: true,
          weekNumbers: true
        }

        const selectedRowKeys = ref([])

        const rowSelection = computed(() => {
        return {
          selectedRowKeys: selectedRowKeys.value,
          onChange: (newSelectedRowKeys) => {
            // Update products with selection status
            products.value = products.value.map(product => ({
              ...product,
              selected: newSelectedRowKeys.includes(product.id),
              qty: newSelectedRowKeys.includes(product.id) ? (product.qty || 1) : product.qty,
            }));
            selectedRowKeys.value = newSelectedRowKeys;
          },
          getCheckboxProps: (record) => ({
            disabled: record.name === 'Disabled User',
            name: record.name,
          }),
        };
      });
        const handleQtyChange = (record) => {
        const index = products.value.findIndex(p => p.id === record.id);
        if (index !== -1) {
          products.value[index].qty = record.qty;
        }
      };
        const filterOption = (input, option) => {
        return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0
        }

        const rules = {
          selectedTier: {
                required: helpers.withMessage('Veuillez Choisir le Tier', required)
            },
          orderDate : {
            required : helpers.withMessage('Champ est requis',required)
          },
          requestedDeliveryDate : {
            required : helpers.withMessage('Champ est requis',required)
          }

        }

        const v$ = useVuelidate(rules, { selectedTier,orderDate,requestedDeliveryDate })

        return {
            loading,
            selectTiers,
            selectedTier,
            flatpickrConfig,
            orderDate,
            requestedDeliveryDate,
            priority,
            notes,
            orderNum,
            filterOption,
            products,
            selectedRowKeys,
            rowSelection,
            handleQtyChange,
            v$
        }
    },
    methods: {
        padWithZeros(number, length = 4) {
        return number.toString().padStart(length, '0');
      },
        setOFNumDate(){
          const date = this.orderDate || new Date();
          const yymm = new Date(date).toISOString().slice(2, 7).replace('-', '');
          const oldOrderNum = this.orderNum
          this.orderNum = `OF${yymm}${oldOrderNum.substring(6)}`
        },
        async fetchTiers(){
        await axios.get("/api/tiers/clients").then(response =>{
          this.selectTiers = response.data.map(item =>({
            value : item.id,
            label : item.name
          }))
        }).catch(error => {
          console.error(error)
          alert("Erreur lors du chargement des données")
        })
      },
        async fetchLastOrder(){
          await axios.get("/api/fabricationOrders/latest").then(response =>{
            const yymm = new Date().toISOString().slice(2, 7).replace('-', '');
            if(response.data.length == 0){
              this.orderNum ="OF"
              this.orderNum += yymm
              this.orderNum += "-"
              this.orderNum += this.padWithZeros(1)
            }else{
              const str = response.data[0].order_number
              const numberPart = str.split("-")[1];
              const unpaddedNumber = parseInt(numberPart, 10);

              this.orderNum ="OF"
              this.orderNum += yymm
              this.orderNum += "-"
              this.orderNum += this.padWithZeros(unpaddedNumber+1)
            }
          }).catch(error => {
            console.error(error)
            alert("Erreur lors du chargement des données")
          })
        },
        async fetchProductsbyTier(){
          this.loading = true;
          await axios.get("/api/tiers/"+this.selectedTier+"/products").then(response =>{
            this.loading = false;
            this.products = response.data
          }).catch(error => {
            console.error(error)
            alert("Erreur lors du chargement des données")
          })
        },
        async ValidateGlobalForm() {
            this.v$.$touch() // Mark all fields as touched
            const isFormValid = await this.v$.$validate()
          // const payload = {
          //   id_tier: this.selectedTier,
          //   order_number: this.orderNum,
          //   order_date: this.orderDate,
          //   requested_date: this.requestedDeliveryDate,
          //   priority: this.priority,
          //   notes: this.notes,
          //   products: this.products.filter(p => p.selected),
          //   user: 1 /**TODO: Remove the 1 and add loggedin user ID**/
          // }
          //
          // console.log(payload)

            if (!isFormValid) {
                return;
            }
            if(this.selectedRowKeys.length===0){
              return;
            }
              Swal.fire({
                title: 'Ajout',
                html: `Voulez-vous vraiment Ajouter cet ordre de Fabrication`,
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


          const payload = {
            id_tier: this.selectedTier,
            order_number: this.orderNum,
            order_date: this.orderDate,
            requested_date: this.requestedDeliveryDate,
            priority: this.priority??1,
            notes: this.notes,
            products: this.products.filter(p => p.selected),
            user: 1 /**TODO: Remove the 1 and add loggedin user ID**/
          }
                const response = await axios.post('/api/fabricationOrders/',payload,{
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
                    title: 'Ordre de Fabrication Crée',
                }).then(() => {
                    //Redirect
                    this.$router.push("/fo")
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
            }
        },
  mounted() {
      this.fetchTiers()
      this.fetchLastOrder()
  }
}
</script>

<template>
    <Layout>
        <pageheader title="Ajouter un Ordre de Fabrication" pageTitle="Ordres de Fabrication" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                  <BCardHeader>
                    <a-flex justify="end" align="center" wrap="wrap" gap="middle">
                      <label>Ordre N°</label>
                      <input type="text" class="form-control form-control-sm font-bold" style="width: 10%" v-model="orderNum" readonly>
                    </a-flex>
                  </BCardHeader>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                          <a-select
                              v-model:value="selectedTier"
                              show-search
                              placeholder="Selectionner un tier"
                              style="width: 100%;text-align: center;margin-bottom: 40px;"
                              :status="v$.selectedTier.$error ? 'error' : ''"
                              :options="selectTiers"
                              :filter-option="filterOption"
                              @change="fetchProductsbyTier"
                          >
                          </a-select>
                          <div v-if="v$.selectedTier.$error" class="text-danger">
                            {{ v$.selectedTier.$errors[0].$message }}
                          </div>
                          <label for="">Date de Commande</label>
                          <flat-pickr
                              :config="flatpickrConfig"
                              class="form-control"
                              placeholder="Selectionner un tier"
                              :class="{ 'is-invalid': v$.orderDate.$error }"
                              v-model="orderDate"
                              @change="setOFNumDate"
                              @blur="v$.orderDate.$touch()"
                          ></flat-pickr>
                          <div
                              v-for="error in v$.orderDate.$errors"
                              :key="error.$uid"
                              class="invalid-feedback"
                          >
                            {{ error.$message }}
                          </div>
                          <label for="">Date de Livraison</label>
                          <flat-pickr
                              :config="flatpickrConfig"
                              class="form-control"
                              placeholder="Selectionner un tier"
                              :class="{ 'is-invalid': v$.requestedDeliveryDate.$error }"
                              v-model="requestedDeliveryDate"
                              @blur="v$.requestedDeliveryDate.$touch()"
                          ></flat-pickr>
                          <div
                              v-for="error in v$.requestedDeliveryDate.$errors"
                              :key="error.$uid"
                              class="invalid-feedback"
                          >
                            {{ error.$message }}
                          </div>
                          <label for="">Priorité</label>
                          <a-input-number v-model:value="priority" min="1" max="3" defaultValue="1"></a-input-number>
                          <label for="">Remarques</label>
                          <a-textarea v-model:value="notes" />

                          <a-divider><PhPackage  :size="23" weight="duotone" />Produits</a-divider>
                          <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                            <div v-if="loading" class="mt-3">
                              <a-flex justify="center" align="center" wrap="wrap" gap="mi ddle">
                                <BSpinner variant="primary" style="width: 3rem; height: 3rem" label="Chargement..." class="mx-1" />
                              </a-flex>
                            </div>

                            <a-table
                            v-if="this.products.length!==0"
                            :dataSource="products"
                            :row-selection="rowSelection"
                            :columns="[
                                { title: 'Famille', dataIndex: 'series', key: 'series' },
                                { title: 'Produit', dataIndex: 'title', key: 'title' },
                                { title: 'Quantités',  key: 'qty' },
                            ]"
                            :pagination="false"
                            size="large"
                            rowKey="id"
                            >
                              <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'qty'">
                                  <a-input-number
                                      :disabled="!selectedRowKeys.includes(record.id)"
                                      v-model:value="record.qty"
                                      :min="1"
                                      :default-value="1"
                                      @change="handleQtyChange(record)"
                                      style="width: 100%"
                                  />
                                </template>
                              </template>
                            </a-table>
                            <div v-if="this.selectedRowKeys.length  === 0 && this.products.length!==0" class="text-danger text-center">
                              Veuillez Choisir au moin un Produit
                            </div>
                          </a-flex>
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