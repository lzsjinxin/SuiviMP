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
        const adresse = ref(null)

        const name = ref(null)

        const ice = ref(null)

        const city = ref(null)

        const selectTierTypes = ref(null)

        const selectedTierType = ref(null)

        const selectCountries = ref(null)

        const selectedCountry = ref(null)  

        const filterOption = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
            };
        
        const rules = {
            adresse: {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },
            name : {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },
            
            city : {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },

            selectedTierType : {
                required: helpers.withMessage('Le Type requis', required)
            }, 
            selectedCountry : {
                required: helpers.withMessage('Le Pays requis', required)
            },
            
            ice : {
                required: helpers.withMessage('Veuillez Remplir le champ', required)
            },
        }
        
        const v$ = useVuelidate(rules, { adresse, name, ice,city,selectCountries, selectedCountry, selectTierTypes, selectedTierType })
        
        return { 
            adresse,
            name,
            ice,
            city,
            selectCountries, 
            selectedCountry,
            selectTierTypes, 
            selectedTierType,
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
                html: `Voulez-vous vraiment Ajouter un Tier `,
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
                type : this.selectedTierType,
                name : this.name,
                country : this.selectedCountry,
                city : this.city,
                ice : this.ice,
                adresse : this.adresse,
                user : 1 /**TODO: Remove the 1 and add loggedin user ID**/ 
            }
            const response = await axios.post('/api/tiers/',payload,{
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
                title: 'Tier Crée',
            }).then(() => {
                //Redirect
                this.$router.push("/tiers")
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
        setTierTypes(){
            const options = ([
            { value: 'C', label: 'Client' },
            { value: 'S', label: 'Fournisseur' },
            { value: 'CS', label: 'Client / Fournisseur' },
            ]);
            this.selectTierTypes = options;
        },
        setCountries(){
            const options = [
    { value: 'Afghanistan', label: '🇦🇫 Afghanistan' },
    { value: 'Afrique du Sud', label: '🇿🇦 Afrique du Sud' },
    { value: 'Albanie', label: '🇦🇱 Albanie' },
    { value: 'Algérie', label: '🇩🇿 Algérie' },
    { value: 'Allemagne', label: '🇩🇪 Allemagne' },
    { value: 'Andorre', label: '🇦🇩 Andorre' },
    { value: 'Angola', label: '🇦🇴 Angola' },
    { value: 'Antigua-et-Barbuda', label: '🇦🇬 Antigua-et-Barbuda' },
    { value: 'Arabie saoudite', label: '🇸🇦 Arabie saoudite' },
    { value: 'Argentine', label: '🇦🇷 Argentine' },
    { value: 'Arménie', label: '🇦🇲 Arménie' },
    { value: 'Australie', label: '🇦🇺 Australie' },
    { value: 'Autriche', label: '🇦🇹 Autriche' },
    { value: 'Azerbaïdjan', label: '🇦🇿 Azerbaïdjan' },
    { value: 'Bahamas', label: '🇧🇸 Bahamas' },
    { value: 'Bahreïn', label: '🇧🇭 Bahreïn' },
    { value: 'Bangladesh', label: '🇧🇩 Bangladesh' },
    { value: 'Barbade', label: '🇧🇧 Barbade' },
    { value: 'Belgique', label: '🇧🇪 Belgique' },
    { value: 'Belize', label: '🇧🇿 Belize' },
    { value: 'Bénin', label: '🇧🇯 Bénin' },
    { value: 'Bhoutan', label: '🇧🇹 Bhoutan' },
    { value: 'Biélorussie', label: '🇧🇾 Biélorussie' },
    { value: 'Birmanie', label: '🇲🇲 Birmanie' },
    { value: 'Bolivie', label: '🇧🇴 Bolivie' },
    { value: 'Bosnie-Herzégovine', label: '🇧🇦 Bosnie-Herzégovine' },
    { value: 'Botswana', label: '🇧🇼 Botswana' },
    { value: 'Brésil', label: '🇧🇷 Brésil' },
    { value: 'Brunei', label: '🇧🇳 Brunei' },
    { value: 'Bulgarie', label: '🇧🇬 Bulgarie' },
    { value: 'Burkina Faso', label: '🇧🇫 Burkina Faso' },
    { value: 'Burundi', label: '🇧🇮 Burundi' },
    { value: 'Cambodge', label: '🇰🇭 Cambodge' },
    { value: 'Cameroun', label: '🇨🇲 Cameroun' },
    { value: 'Canada', label: '🇨🇦 Canada' },
    { value: 'Cap-Vert', label: '🇨🇻 Cap-Vert' },
    { value: 'Centrafrique', label: '🇨🇫 Centrafrique' },
    { value: 'Chili', label: '🇨🇱 Chili' },
    { value: 'Chine', label: '🇨🇳 Chine' },
    { value: 'Chypre', label: '🇨🇾 Chypre' },
    { value: 'Colombie', label: '🇨🇴 Colombie' },
    { value: 'Comores', label: '🇰🇲 Comores' },
    { value: 'Congo-Brazzaville', label: '🇨🇬 Congo-Brazzaville' },
    { value: 'Congo-Kinshasa', label: '🇨🇩 Congo-Kinshasa' },
    { value: 'Corée du Nord', label: '🇰🇵 Corée du Nord' },
    { value: 'Corée du Sud', label: '🇰🇷 Corée du Sud' },
    { value: 'Costa Rica', label: '🇨🇷 Costa Rica' },
    { value: 'Côte d\'Ivoire', label: '🇨🇮 Côte d\'Ivoire' },
    { value: 'Croatie', label: '🇭🇷 Croatie' },
    { value: 'Cuba', label: '🇨🇺 Cuba' },
    { value: 'Danemark', label: '🇩🇰 Danemark' },
    { value: 'Djibouti', label: '🇩🇯 Djibouti' },
    { value: 'Dominique', label: '🇩🇲 Dominique' },
    { value: 'Égypte', label: '🇪🇬 Égypte' },
    { value: 'Émirats arabes unis', label: '🇦🇪 Émirats arabes unis' },
    { value: 'Équateur', label: '🇪🇨 Équateur' },
    { value: 'Érythrée', label: '🇪🇷 Érythrée' },
    { value: 'Espagne', label: '🇪🇸 Espagne' },
    { value: 'Estonie', label: '🇪🇪 Estonie' },
    { value: 'Eswatini', label: '🇸🇿 Eswatini' },
    { value: 'États-Unis', label: '🇺🇸 États-Unis' },
    { value: 'Éthiopie', label: '🇪🇹 Éthiopie' },
    { value: 'Fidji', label: '🇫🇯 Fidji' },
    { value: 'Finlande', label: '🇫🇮 Finlande' },
    { value: 'France', label: '🇫🇷 France' },
    { value: 'Gabon', label: '🇬🇦 Gabon' },
    { value: 'Gambie', label: '🇬🇲 Gambie' },
    { value: 'Géorgie', label: '🇬🇪 Géorgie' },
    { value: 'Ghana', label: '🇬🇭 Ghana' },
    { value: 'Grèce', label: '🇬🇷 Grèce' },
    { value: 'Grenade', label: '🇬🇩 Grenade' },
    { value: 'Guatemala', label: '🇬🇹 Guatemala' },
    { value: 'Guinée', label: '🇬🇳 Guinée' },
    { value: 'Guinée équatoriale', label: '🇬🇶 Guinée équatoriale' },
    { value: 'Guinée-Bissau', label: '🇬🇼 Guinée-Bissau' },
    { value: 'Guyana', label: '🇬🇾 Guyana' },
    { value: 'Haïti', label: '🇭🇹 Haïti' },
    { value: 'Honduras', label: '🇭🇳 Honduras' },
    { value: 'Hongrie', label: '🇭🇺 Hongrie' },
    { value: 'Îles Cook', label: '🇨🇰 Îles Cook' },
    { value: 'Îles Marshall', label: '🇲🇭 Îles Marshall' },
    { value: 'Îles Salomon', label: '🇸🇧 Îles Salomon' },
    { value: 'Inde', label: '🇮🇳 Inde' },
    { value: 'Indonésie', label: '🇮🇩 Indonésie' },
    { value: 'Irak', label: '🇮🇶 Irak' },
    { value: 'Iran', label: '🇮🇷 Iran' },
    { value: 'Irlande', label: '🇮🇪 Irlande' },
    { value: 'Islande', label: '🇮🇸 Islande' },
    { value: 'Italie', label: '🇮🇹 Italie' },
    { value: 'Jamaïque', label: '🇯🇲 Jamaïque' },
    { value: 'Japon', label: '🇯🇵 Japon' },
    { value: 'Jordanie', label: '🇯🇴 Jordanie' },
    { value: 'Kazakhstan', label: '🇰🇿 Kazakhstan' },
    { value: 'Kenya', label: '🇰🇪 Kenya' },
    { value: 'Kirghizistan', label: '🇰🇬 Kirghizistan' },
    { value: 'Kiribati', label: '🇰🇮 Kiribati' },
    { value: 'Kosovo', label: '🇽🇰 Kosovo' },
    { value: 'Koweït', label: '🇰🇼 Koweït' },
    { value: 'Laos', label: '🇱🇦 Laos' },
    { value: 'Lesotho', label: '🇱🇸 Lesotho' },
    { value: 'Lettonie', label: '🇱🇻 Lettonie' },
    { value: 'Liban', label: '🇱🇧 Liban' },
    { value: 'Liberia', label: '🇱🇷 Liberia' },
    { value: 'Libye', label: '🇱🇾 Libye' },
    { value: 'Liechtenstein', label: '🇱🇮 Liechtenstein' },
    { value: 'Lituanie', label: '🇱🇹 Lituanie' },
    { value: 'Luxembourg', label: '🇱🇺 Luxembourg' },
    { value: 'Macédoine du Nord', label: '🇲🇰 Macédoine du Nord' },
    { value: 'Madagascar', label: '🇲🇬 Madagascar' },
    { value: 'Malaisie', label: '🇲🇾 Malaisie' },
    { value: 'Malawi', label: '🇲🇼 Malawi' },
    { value: 'Maldives', label: '🇲🇻 Maldives' },
    { value: 'Mali', label: '🇲🇱 Mali' },
    { value: 'Malte', label: '🇲🇹 Malte' },
    { value: 'Maroc', label: '🇲🇦 Maroc' },
    { value: 'Maurice', label: '🇲🇺 Maurice' },
    { value: 'Mauritanie', label: '🇲🇷 Mauritanie' },
    { value: 'Mexique', label: '🇲🇽 Mexique' },
    { value: 'Micronésie', label: '🇫🇲 Micronésie' },
    { value: 'Moldavie', label: '🇲🇩 Moldavie' },
    { value: 'Monaco', label: '🇲🇨 Monaco' },
    { value: 'Mongolie', label: '🇲🇳 Mongolie' },
    { value: 'Monténégro', label: '🇲🇪 Monténégro' },
    { value: 'Mozambique', label: '🇲🇿 Mozambique' },
    { value: 'Namibie', label: '🇳🇦 Namibie' },
    { value: 'Nauru', label: '🇳🇷 Nauru' },
    { value: 'Népal', label: '🇳🇵 Népal' },
    { value: 'Nicaragua', label: '🇳🇮 Nicaragua' },
    { value: 'Niger', label: '🇳🇪 Niger' },
    { value: 'Nigéria', label: '🇳🇬 Nigéria' },
    { value: 'Niue', label: '🇳🇺 Niue' },
    { value: 'Norvège', label: '🇳🇴 Norvège' },
    { value: 'Nouvelle-Zélande', label: '🇳🇿 Nouvelle-Zélande' },
    { value: 'Oman', label: '🇴🇲 Oman' },
    { value: 'Ouganda', label: '🇺🇬 Ouganda' },
    { value: 'Ouzbékistan', label: '🇺🇿 Ouzbékistan' },
    { value: 'Pakistan', label: '🇵🇰 Pakistan' },
    { value: 'Palaos', label: '🇵🇼 Palaos' },
    { value: 'Palestine', label: '🇵🇸 Palestine' },
    { value: 'Panama', label: '🇵🇦 Panama' },
    { value: 'Papouasie-Nouvelle-Guinée', label: '🇵🇬 Papouasie-Nouvelle-Guinée' },
    { value: 'Paraguay', label: '🇵🇾 Paraguay' },
    { value: 'Pays-Bas', label: '🇳🇱 Pays-Bas' },
    { value: 'Pérou', label: '🇵🇪 Pérou' },
    { value: 'Philippines', label: '🇵🇭 Philippines' },
    { value: 'Pologne', label: '🇵🇱 Pologne' },
    { value: 'Portugal', label: '🇵🇹 Portugal' },
    { value: 'Qatar', label: '🇶🇦 Qatar' },
    { value: 'République dominicaine', label: '🇩🇴 République dominicaine' },
    { value: 'République tchèque', label: '🇨🇿 République tchèque' },
    { value: 'Roumanie', label: '🇷🇴 Roumanie' },
    { value: 'Royaume-Uni', label: '🇬🇧 Royaume-Uni' },
    { value: 'Russie', label: '🇷🇺 Russie' },
    { value: 'Rwanda', label: '🇷🇼 Rwanda' },
    { value: 'Saint-Christophe-et-Niévès', label: '🇰🇳 Saint-Christophe-et-Niévès' },
    { value: 'Sainte-Lucie', label: '🇱🇨 Sainte-Lucie' },
    { value: 'Saint-Marin', label: '🇸🇲 Saint-Marin' },
    { value: 'Saint-Vincent-et-les-Grenadines', label: '🇻🇨 Saint-Vincent-et-les-Grenadines' },
    { value: 'Salvador', label: '🇸🇻 Salvador' },
    { value: 'Samoa', label: '🇼🇸 Samoa' },
    { value: 'Sao Tomé-et-Principe', label: '🇸🇹 Sao Tomé-et-Principe' },
    { value: 'Sénégal', label: '🇸🇳 Sénégal' },
    { value: 'Serbie', label: '🇷🇸 Serbie' },
    { value: 'Seychelles', label: '🇸🇨 Seychelles' },
    { value: 'Sierra Leone', label: '🇸🇱 Sierra Leone' },
    { value: 'Singapour', label: '🇸🇬 Singapour' },
    { value: 'Slovaquie', label: '🇸🇰 Slovaquie' },
    { value: 'Slovénie', label: '🇸🇮 Slovénie' },
    { value: 'Somalie', label: '🇸🇴 Somalie' },
    { value: 'Soudan', label: '🇸🇩 Soudan' },
    { value: 'Soudan du Sud', label: '🇸🇸 Soudan du Sud' },
    { value: 'Sri Lanka', label: '🇱🇰 Sri Lanka' },
    { value: 'Suède', label: '🇸🇪 Suède' },
    { value: 'Suisse', label: '🇨🇭 Suisse' },
    { value: 'Suriname', label: '🇸🇷 Suriname' },
    { value: 'Syrie', label: '🇸🇾 Syrie' },
    { value: 'Tadjikistan', label: '🇹🇯 Tadjikistan' },
    { value: 'Tanzanie', label: '🇹🇿 Tanzanie' },
    { value: 'Tchad', label: '🇹🇩 Tchad' },
    { value: 'Thaïlande', label: '🇹🇭 Thaïlande' },
    { value: 'Timor oriental', label: '🇹🇱 Timor oriental' },
    { value: 'Togo', label: '🇹🇬 Togo' },
    { value: 'Tonga', label: '🇹🇴 Tonga' },
    { value: 'Trinité-et-Tobago', label: '🇹🇹 Trinité-et-Tobago' },
    { value: 'Tunisie', label: '🇹🇳 Tunisie' },
    { value: 'Turkménistan', label: '🇹🇲 Turkménistan' },
    { value: 'Turquie', label: '🇹🇷 Turquie' },
    { value: 'Tuvalu', label: '🇹🇻 Tuvalu' },
    { value: 'Ukraine', label: '🇺🇦 Ukraine' },
    { value: 'Uruguay', label: '🇺🇾 Uruguay' },
    { value: 'Vanuatu', label: '🇻🇺 Vanuatu' },
    { value: 'Vatican', label: '🇻🇦 Vatican' },
    { value: 'Venezuela', label: '🇻🇪 Venezuela' },
    { value: 'Viêt Nam', label: '🇻🇳 Viêt Nam' },
    { value: 'Yémen', label: '🇾🇪 Yémen' },
    { value: 'Zambie', label: '🇿🇲 Zambie' },
    { value: 'Zimbabwe', label: '🇿🇼 Zimbabwe' }
            ];
            this.selectCountries = options;
        }

    },
    mounted(){
        this.setTierTypes()
        this.setCountries()
    }
}
</script>

<template>
    <Layout>
        <pageheader title="Ajouter un Tier" pageTitle="Tiers" />

        <BRow>
            <BCol sm="12">
                <BCard no-body>
                    <BCardBody style="width: 50%; margin: auto;">
                        <a-flex justify="center" align="center" wrap="wrap" gap="middle">
                            <a-divider><PhTag  :size="23" weight="duotone" />Type</a-divider>
                            <label for="">Type</label>
                                <a-select
                                v-model:value="selectedTierType"
                                show-search
                                placeholder="Selectionner un Type"
                                style="width: 100%;text-align: center;"
                                :status="v$.selectedTierType.$error ? 'error' : ''"
                                :options="selectTierTypes"
                                :filter-option="filterOption"
                                >
                                </a-select>
                                 <div v-if="v$.selectedTierType.$error" class="text-danger">
                                    {{ v$.selectedTierType.$errors[0].$message }}
                                </div>
                            <a-divider><PhHandshake :size="23" weight="duotone" /> Tier</a-divider>
                            <input class="form-control" v-model="name" :class="{ 'is-invalid': v$.name.$error }" placeholder="Nom de l'entreprise" />
                            <div 
                                v-for="error in v$.name.$errors" 
                                :key="error.$uid"
                                class="invalid-feedback"
                            >
                                {{ error.$message }}
                            </div>
                            <a-select
                                v-model:value="selectedCountry"
                                show-search
                                placeholder="Selectionner un Pays"
                                style="width: 100%;text-align: center;"
                                :status="v$.selectedCountry.$error ? 'error' : ''"
                                :options="selectCountries"
                                :filter-option="filterOption"
                                >
                            </a-select>
                                <div v-if="v$.selectedCountry.$error" class="text-danger">
                                {{ v$.selectedCountry.$errors[0].$message }}
                            </div>
                            <input class="form-control" v-model="city" :class="{ 'is-invalid': v$.city.$error }" placeholder="Ville" />
                            <div 
                                v-for="error in v$.city.$errors" 
                                :key="error.$uid"
                                class="invalid-feedback"
                            >
                                {{ error.$message }}
                            </div>
                            <input class="form-control" v-model="adresse" :class="{ 'is-invalid': v$.adresse.$error }" placeholder="Adresse" />
                            <div 
                                v-for="error in v$.adresse.$errors" 
                                :key="error.$uid"
                                class="invalid-feedback"
                            >
                                {{ error.$message }}
                            </div>
                             <input class="form-control" v-model="ice" :class="{ 'is-invalid': v$.ice.$error }" placeholder="ICE" />
                            <div 
                                v-for="error in v$.ice.$errors" 
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