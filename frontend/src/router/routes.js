export default [
    //Login
    {
        path: "/login/",
        name: "login",
        meta: { title: "Login"},
        component: () => import("../views/login.vue"),
    },

    //Home
    {
        path: '/',
        name: 'home',
        meta: { title: 'Accueil' },
        component: () => import('../views/HomeSwitcher.vue'),
    },

    //Arrivals
    {
        path: "/arrivals/",
        name: "arrivals",
        meta: {
            title: "Arrivages",
            departments: ['Administration','Logistics']
        },
        component: () => import("../views/ArrivalList.vue"),
    },
    {
        path: "/arrivals/new",
        name: "ArrivalAdd",
        meta: { title: "Ajouter un Arrivage",
            departments: ['Administration','Logistics']},
        component: () => import("../views/ArrivalAdd.vue"),
    },
    {
        path: "/arrivals/:id(\\d+)",
        name: "ArrivalModify",
        props: (route) => ({ id: Number(route.params.id) }),
        meta: { title: "Modifier Arrivage",
            departments: ['Administration','Logistics']},
        component: () => import("../views/ArrivalModify.vue"),
    },
    //Matières Premiere
    {
        path: "/mp/",
        name: "MPList",
        meta: { title: "Matières Premières",
            departments: ['Administration','Logistics']},
        component: () => import("../views/MPList.vue"),
    },
    {
        path: "/mp/new",
        name: "MPAdd",
        meta: { title: "Ajout Matière Première",
            departments: ['Administration','Logistics']},
        component: () => import("../views/MPAdd.vue"),
    },
    {
        path: "/mp/:id(\\d+)",
        name: "MPUpdate",
        meta: { title: "Modifier MP",
            departments: ['Administration','Logistics']},
        component: () => import("../views/MPModify.vue"),
    },
    //Unit
    {
        path: "/units/",
        name: "UnitList",
        meta: { title: "Unités",
            departments: ['Engineering','Administration']},
        component: () => import("../views/UnitList.vue"),
    },
    {
        path: "/units/new",
        name: "UnitAdd",
        meta: { title: "Ajouter Unités",
            departments: ['Engineering','Administration']},
        component: () => import("../views/UnitAdd.vue"),
    },
        {
        path: "/units/:id(\\d+)",
        name: "UnitModify",
        meta: { title: "Modifier Unité",
            departments: ['Engineering','Administration']},
        component: () => import("../views/UnitModify.vue"),
    },
     //Departement
    {
        path: "/departments/",
        name: "DepartementList",
        meta: { title: "Liste des Departements",
            departments: ['Administration']},
        component: () => import("../views/DepartmentList.vue"),
    },
     {
        path: "/departments/new",
        name: "DepartementAdd",
        meta: { title: "Ajouter un Departement",
            departments: ['Administration']},
        component: () => import("../views/DepartmentAdd.vue"),
    },
    {
        path: "/departments/:id(\\d+)",
        name: "DepartementModify",
        meta: { title: "Modifier Departement",
            departments: ['Administration']},
        component: () => import("../views/DepartmentModify.vue"),
    },
    //Location
    {
        path: "/locations/",
        name: "LocationList",
        meta: { title: "Liste des Emplacements",
            departments: ['Administration','Logistics']},
        component: () => import("../views/LocationList.vue"),
    },
    {
        path: "/locations/new",
        name: "LocationAdd",
        meta: { title: "Ajouter un Emplacement",
            departments: ['Administration','Logistics']},
        component: () => import("../views/LocationAdd.vue"),
    },
    {
        path: "/locations/:id(\\d+)",
        name: "LocationModify",
        meta: { title: "Modifier un Emplacement",
            departments: ['Administration','Logistics']},
        component: () => import("../views/LocationModify.vue"),
    },
         //User
    {
        path: "/users/",
        name: "UserList",
        meta: { title: "Liste des Utilisateurs",
            departments: ['Administration']},
        component: () => import("../views/UserList.vue"),
    },
    {
        path: "/users/new",
        name: "UserAdd",
        meta: { title: "Ajouter un Utilisateur",
            departments: ['Administration']},
        component: () => import("../views/UserAdd.vue"),
    },
    {
        path: "/users/:id(\\d+)",
        name: "UserModify",
        meta: { title: "Modifier Utilisateur",
            departments: ['Administration']},
        component: () => import("../views/UserModify.vue"),
    },
       //Tiers
    {
        path: "/tiers/",
        name: "TierList",
        meta: { title: "Liste des Tiers",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TierList.vue"),
    },
    {
        path: "/tiers/new",
        name: "TierAdd",
        meta: { title: "Ajouter un Tier",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TierAdd.vue"),
    },
    {
        path: "/tiers/:id(\\d+)",
        name: "TierModify",
        meta: { title: "Modifier un Tier",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TierModify.vue"),
    },
    {
        path: "/tiers/series/new",
        name: "TierSeriesAdd",
        meta: { title: "Ajouter une famille",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TierSeriesAdd.vue"),
    },
    {
        path: "/tiers/series",
        name: "TierSeriesList",
        meta: { title: "Lister les familles",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TierSeriesList.vue"),
    },
    {
        path: "/tiers/series/:id(\\d+)",
        name: "TierSeriesModify",
        meta: { title: "Modifier une Famille",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TierSeriesModify.vue"),
    },
           //Operations
    {
        path: "/operations/",
        name: "OperationDefinitionsAdd",
        meta: { title: "Definitions des operations",
            departments: ['Engineering','Administration']},
        component: () => import("../views/OperationsDefinitionsAdd.vue"),
    },
    {
        path: "/operations/allocation",
        name: "OperationDefinitionsAllocation",
        meta: { title: "Affecter les operations à des produits",
            departments: ['Engineering','Administration']},
        component: () => import("../views/OperationAllocation.vue"),
    },
    {
        path: "/operations/allocation/:id(\\d+)",
        name: "ModifyOperationDefinitionsAllocation",
        meta: { title: "Modifier les Affectations des operations pour le produit",
            departments: ['Engineering','Administration']},
        component: () => import("../views/OperationAllocationModify.vue"),
    },
           //Products
    {
        path: "/products/",
        name: "ProductList",
        meta: { title: "Liste des Produits",
            departments: ['Engineering','Administration']},
        component: () => import("../views/ProductList.vue"),
    },
        {
        path: "/products/new",
        name: "ProductAdd",
        meta: { title: "Ajouter des Produits",
            departments: ['Engineering','Administration']},
        component: () => import("../views/ProductAdd.vue"),
    },
    {
        path: "/products/:id(\\d+)",
        name: "ProductModify",
        meta: { title: "Modifier un Produit",
            departments: ['Engineering','Administration']},
        component: () => import("../views/ProductModify.vue"),
    },
    //Transfers
    {
        path:"/mp/transfers/transfer",
        name: "TransfersTransfer",
        meta: { title: "Transférer Une Matière Premiere",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TransfersTransfer.vue"),
    },
    {
        path:"/mp/transfers/receive",
        name: "TransfersReceive",
        meta: { title: "Réception de Matière Premiere",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TransfersReceive.vue"),
    },
    {
        path:"/mp/transfers/",
        name: "TransfersList",
        meta: { title: "Liste des Transferts",
            departments: ['Administration','Logistics']},
        component: () => import("../views/TransfersList.vue"),
    },
    //Fabrication Orders
    {
        path : "/fo/new",
        name : "FabricationOrdersNew",
        meta : {title:"Ajouter un Ordre de Fabrication",
            departments: ['Administration','Logistics']},
        component:()=>import("../views/fabricationOrdersAdd.vue")
    },
    {
        path : "/fo/",
        name : "FabricationOrdersList",
        meta : {title:"Liste des Ordres de Fabrication",
            departments: ['Administration','Logistics']},
        component:()=>import("../views/fabricationOrdersList.vue")
    },
    {
        path: '/fo/:id(\\d+)/product-orders/new',
        name: 'ProductOrderAdd',
        meta: { title: 'Ajouter les Produits A fabriquer' ,
            departments: ['Administration','Logistics']},
        component: () => import('../views/ProductOrderAdd.vue'),
    },

    //Operations
    {
        path: '/production/declare',
        name: 'OperationDeclare',
        meta: { title: 'Déclarer une opération', departments: ['Production','Administration'] },
        component: () => import('../views/OperationDeclare.vue')
    },

    //QrCodeService
    {
        path:"/qr",
        name:"qrCodeGenerate",
        meta: { title: "Qr Code"},
        component:()=>import("../views/qrCode.vue")
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