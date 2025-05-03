<script>
import Layout from "@/layout/main.vue"
import pageheader from "@/components/page-header.vue"
import axios from "axios"
export default {
    name: "ArrivalList",
    components: {
        Layout, pageheader
    },
    data(){
    return{
        items : []
    }
    },
    methods: {
    fetchData() {
      axios.get('/api/arrivals/')
        .then(response => {
          this.items = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    }
  },
  mounted() {
    this.fetchData();
  },
}
</script>

<template>
    <Layout>
        <pageheader title="Liste des Arrivages" :pageTitle="$t('Arrivals')" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody>
                        <div class="table-responsive">
                            <table class="table table-hover tbl-product" id="pc-dt-simple">
                                <thead>
                                    <tr > 
                                        <th class="text-end">#</th>
                                        <th>Date</th>
                                        <th>Provenance</th>
                                        <th >Matricule de Vehicule</th>
                                        <th >Receptionné Par</th> 
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr  v-for="item in items" :key="item.id" style="cursor: pointer;">
                                        <td class="text-end">{{ item.id }}</td>
                                        <td> {{ item.date }} </td>
                                        <td>{{ item.tier }}</td>
                                        <td>{{ item.vehicule_registration }}</td>
                                        <td class="text-center">
                                            {{ item.user }}
                                            <!-- <router-link :to="`users/${item.useradd}`" class="b-brand text-primary"> {{ item.user }} </router-link> -->
                                        </td>
                                        <td class="text-end">
                                                    <ul class="list-inline mb-0">
                                                        <!-- <li class="list-inline-item"><a href="#" class="avtar avtar-s btn-link-info btn-pc-default"><i class="ti ti-eye f-20"></i></a></li> -->
                                                        <li class="list-inline-item"><router-link :to="`/arrivals/${item.id}`" class="avtar avtar-s btn-link-primary btn-pc-default"><i class="ti ti-edit f-20"></i></router-link></li>
                                                        <li class="list-inline-item"><a href="#" class="avtar avtar-s btn-link-danger btn-pc-default"><i class="ti ti-trash f-20"></i></a></li>
                                                    </ul>
                                                </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>