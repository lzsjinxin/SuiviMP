<template>
  <div class="auth-main v1">
    <div class="auth-wrapper">
      <div class="auth-form">

        <!-- Password modal -->
        <a-modal
            v-model:open="modalOpen"
            title="Mot de Passe"
            @ok="login"
        >
          <div class="d-flex flex-column justify-content-center align-items-center gap-2">
            <div class="d-flex flex-row justify-content-center align-items-center gap-2">
              <label for="username">Utilisateur</label>
              <input
                  id="username"
                  type="text"
                  class="form-control form-control-sm text-center font-bold"
                  :value="user ? user.fname + ' ' + user.name : ''"
                  readonly
              />
            </div>
            <input
                type="password"
                v-model="pwdvalue"
                class="form-control"
                placeholder="Mot de passe"
            />
          </div>
        </a-modal>

        <!-- QR form -->
        <div class="card my-5">
          <div class="card-body">
            <form @submit.prevent="fetchUser">
              <div class="text-center">
                <img
                    src="@/assets/images/authentication/img-auth-login.png"
                    alt="login illustration"
                    class="img-fluid mb-3"
                />
                <h4 class="f-w-500 mb-1">Veuillez scanner votre code QR</h4>
              </div>

              <div class="mb-3">
                <input
                    id="qr-input"
                    type="password"
                    class="form-control text-center"
                    v-model="qrCodeValue"
                    placeholder="Code QR"
                    autofocus
                />
              </div>

              <div class="d-grid mt-4">
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="loading"
                >
                  <span v-if="loading">
                    <BSpinner label="Chargement" class="mx-1" />
                  </span>
                  <span v-else>
                    <PhQrCode :size="32" /> Se Connecter
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios                         from 'axios';
import { notification }              from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h }                         from 'vue';

export default {
  name: 'LOGIN',

  /** ------------------------------------------------------------------
   *  Reactive state (Option API → data())
   * ----------------------------------------------------------------- */
  data() {
    return {
      qrCodeValue: '',
      modalOpen: false,
      loading: false,
      pwdvalue: '',
      user: null,            // will hold the fetched user object
    };
  },

  /** ------------------------------------------------------------------
   *  Methods
   * ----------------------------------------------------------------- */
  methods: {
    /* Fetch user by QR code ------------------------------------------------*/
    async fetchUser() {
      try {
        this.loading = true;
        const { data } = await axios.get(`/api/users/qr/${this.qrCodeValue}`);
        this.user      = data;
        this.modalOpen = true;
      } catch (err) {
        this.qrCodeValue = '';
        this.notify(err);
      } finally {
        this.loading = false;
      }
    },

    /* Submit password + login --------------------------------------------*/
    async login() {
      try {
        await this.$store.dispatch('auth/login', {
          id: this.user.id,
          password: this.pwdvalue,
        });
        this.$router.push('/');
      } catch (err) {
        this.pwdvalue = '';
        this.notify(err);
      }
    },

    /* Reusable notification helper ---------------------------------------*/
    notify(err) {
      notification.open({
        message: 'Erreur',
        description:
            err?.response?.data?.message ?? 'Une erreur est survenue',
        placement: 'top',
        duration: 2,
        icon: h(ExclamationCircleOutlined, { style: { color: '#ff0000' } }),
      });
    },
  },
};
</script>

<style scoped>
/* Add component-specific styles here if needed */
</style>
