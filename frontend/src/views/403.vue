<script>

export default {
    name: "403",
    components: {
       
    },

}
</script>

<template>
    <div class="auth-main v1">
        <div class="auth-wrapper">
            <div class="auth-form">
                <div class="error-card">
                    <div class="card-body">
                        <div class="error-image-block">
                            <img class="img-fluid" src="@/assets/images/pages/403.png" alt="img">
                        </div>
                        <div class="text-center">
                            <h1 class="mt-2">Autorisation refusé</h1>
                            <p class="mt-2 mb-4 text-muted f-20">Vous n'avez pas accès a cette ressource</p>
                            <router-link class="btn btn-primary d-inline-flex align-items-center mb-3"
                                to="/"  ><i class="ph-duotone ph-arrow-left me-2"></i> Retour</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>