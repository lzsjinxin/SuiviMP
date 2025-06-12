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
    {
        path: "/units/new",
        name: "UnitAdd",
        meta: { title: "Ajouter Unités"},
        component: () => import("../views/UnitAdd.vue"),
    },
        {
        path: "/units/:id(\\d+)",
        name: "UnitModify",
        meta: { title: "Modifier Unité"},
        component: () => import("../views/UnitModify.vue"),
    },
     //Departement
    {
        path: "/departments/",
        name: "DepartementList",
        meta: { title: "Liste des Departements"},
        component: () => import("../views/DepartmentList.vue"),
    },
     {
        path: "/departments/new",
        name: "DepartementAdd",
        meta: { title: "Ajouter un Departement"},
        component: () => import("../views/DepartmentAdd.vue"),
    },
    {
        path: "/departments/:id(\\d+)",
        name: "DepartementModify",
        meta: { title: "Modifier Departement"},
        component: () => import("../views/DepartmentModify.vue"),
    },
    //Location
    {
        path: "/locations/",
        name: "LocationList",
        meta: { title: "Liste des Emplacements"},
        component: () => import("../views/LocationList.vue"),
    },
    {
        path: "/locations/new",
        name: "LocationAdd",
        meta: { title: "Ajouter un Emplacement"},
        component: () => import("../views/LocationAdd.vue"),
    },
    {
        path: "/locations/:id(\\d+)",
        name: "LocationModify",
        meta: { title: "Modifier un Emplacement"},
        component: () => import("../views/LocationModify.vue"),
    },
         //User
    {
        path: "/users/",
        name: "UserList",
        meta: { title: "Liste des Utilisateurs"},
        component: () => import("../views/UserList.vue"),
    },
    {
        path: "/users/new",
        name: "UserAdd",
        meta: { title: "Ajouter un Utilisateur"},
        component: () => import("../views/UserAdd.vue"),
    },
    {
        path: "/users/:id(\\d+)",
        name: "UserModify",
        meta: { title: "Modifier Utilisateur"},
        component: () => import("../views/UserModify.vue"),
    },
       //Tiers
    {
        path: "/tiers/",
        name: "TierList",
        meta: { title: "Liste des Tiers"},
        component: () => import("../views/TierList.vue"),
    },
    {
        path: "/tiers/new",
        name: "TierAdd",
        meta: { title: "Ajouter un Tier"},
        component: () => import("../views/TierAdd.vue"),
    },
    {
        path: "/tiers/:id(\\d+)",
        name: "TierModify",
        meta: { title: "Modifier un Tier"},
        component: () => import("../views/TierModify.vue"),
    },
    {
        path: "/tiers/series/new",
        name: "TierSeriesAdd",
        meta: { title: "Ajouter une famille"},
        component: () => import("../views/TierSeriesAdd.vue"),
    },
    {
        path: "/tiers/series",
        name: "TierSeriesList",
        meta: { title: "Lister les familles"},
        component: () => import("../views/TierSeriesList.vue"),
    },
    {
        path: "/tiers/series/:id(\\d+)",
        name: "TierSeriesModify",
        meta: { title: "Modifier une Famille"},
        component: () => import("../views/TierSeriesModify.vue"),
    },
           //Operations
    {
        path: "/operations/",
        name: "OperationDefinitionsAdd",
        meta: { title: "Definitions des operations"},
        component: () => import("../views/OperationsDefinitionsAdd.vue"),
    },
    {
        path: "/operations/allocation",
        name: "OperationDefinitionsAllocation",
        meta: { title: "Affecter les operations à des produits"},
        component: () => import("../views/OperationAllocation.vue"),
    },
    {
        path: "/operations/allocation/:id(\\d+)",
        name: "ModifyOperationDefinitionsAllocation",
        meta: { title: "Modifier les Affectations des operations pour le produit"},
        component: () => import("../views/OperationAllocationModify.vue"),
    },
           //Products
    {
        path: "/products/",
        name: "ProductList",
        meta: { title: "Liste des Produits"},
        component: () => import("../views/ProductList.vue"),
    },
        {
        path: "/products/new",
        name: "ProductAdd",
        meta: { title: "Ajouter des Produits"},
        component: () => import("../views/ProductAdd.vue"),
    },
    {
        path: "/products/:id(\\d+)",
        name: "ProductModify",
        meta: { title: "Modifier un Produit"},
        component: () => import("../views/ProductModify.vue"),
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