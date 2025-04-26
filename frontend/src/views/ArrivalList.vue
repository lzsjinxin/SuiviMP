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
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr  v-for="item in items" :key="item.id" style="cursor: pointer;">
                                        <td class="text-end">{{ item.id }}</td>
                                        <td> {{ item.date }} </td>
                                        <td>{{ item.tier }}</td>
                                        <td>{{ item.vehicule_registration }}</td>
                                        <td class="text-center">
                                            <router-link :to="`users/${item.useradd}`" class="b-brand text-primary"> {{ item.user }} </router-link>
                                            <!-- <div class="prod-action-links">
                                                <ul class="list-inline me-auto mb-0">
                                                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip"
                                                        title="View">
                                                        <a href="#"
                                                            class="avtar avtar-xs btn-link-secondary btn-pc-default"
                                                            data-bs-toggle="offcanvas"
                                                            data-bs-target="#productOffcanvas">
                                                            <i class="ti ti-eye f-18"></i>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <router-link to="/arrivals"
                                                            class=" avtar avtar-xs btn-link-success btn-pc-default">
                                                            <i class="ti ti-edit-circle f-18"></i>
                                                        </router-link>
                                                    </li>
                                                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip"
                                                        title="Delete">
                                                        <a href="#"
                                                            class="avtar avtar-xs btn-link-danger btn-pc-default">
                                                            <i class="ti ti-trash f-18"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div> -->
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