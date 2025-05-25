<script>

export default {
    name: "404",
    components: {
       
    }
}
</script>

<template>
    <div class="auth-main v1">
        <div class="auth-wrapper">
            <div class="auth-form">
                <div class="error-card">
                    <div class="card-body">
                        <div class="error-image-block">
                            <img class="img-fluid" src="@/assets/images/pages/404.png" alt="img" style=" width:350px;
    height:350px;">
                        </div>
                        <div class="text-center">
                            <h1 class="mt-2">Ressource n'est pas trouvé</h1>
                            <p class="mt-2 mb-4 text-muted f-20">Nous n'avons pas trouvé la page que vous recherchiez. Pourquoi ne pas essayer
                                de revenir à la page d'accueil.</p>
                            <router-link class="btn btn-primary d-inline-flex align-items-center mb-3"
                                to="/"><i class="ph-duotone ph-house me-2"></i> Retour à l'accueil</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>