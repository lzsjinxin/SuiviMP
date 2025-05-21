export default [
    //Arrivals
    {
        path: "/arrivals/",
        name: "arrivals",
        meta: { title: "Arrivages"},
        component: () => import("../views/ArrivalList.vue"),
    },
    {
        path: "/arrivals/new",
        name: "ArrivalAdd",
        meta: { title: "Ajouter un Arrivage"},
        component: () => import("../views/ArrivalAdd.vue"),
    },
    {
        path: "/arrivals/:id(\\d+)",
        name: "ArrivalModify",
        props: (route) => ({ id: Number(route.params.id) }),
        meta: { title: "Modifier Arrivage"},
        component: () => import("../views/ArrivalModify.vue"),
    },
    //Matières Premiere
    {
        path: "/mp/",
        name: "MPList",
        meta: { title: "Matières Premières"},
        component: () => import("../views/MPList.vue"),
    },
    {
        path: "/mp/new",
        name: "MPAdd",
        meta: { title: "Ajout Matière Première"},
        component: () => import("../views/MPAdd.vue"),
    },
    {
        path: "/mp/:id(\\d+)",
        name: "MPUpdate",
        meta: { title: "Modifier MP"},
        component: () => import("../views/MPModify.vue"),
    },
    //Unit
    {
        path: "/units/",
        name: "UnitList",
        meta: { title: "Unités"},
        component: () => import("../views/UnitList.vue"),
    },
    //Erreurs
    {
        path: "/404",
        name: "404",
        meta: { title: "Cette Page n'existe pas"},
        component: () => import("../views/404.vue"),
    },
    {
        path: "/403",
        name: "403",
        meta: { title: "Vous n'avez pas les droits pour accéder à cette page"},
        component: () => import("../views/403.vue"),
    },
    
    // 404 catch-all route - redirect to home or a dedicated 404 page
  {
    path: '/:pathMatch(.*)*',
    redirect: '/404' // or '/not-found' if you have a 404 page
  }
]