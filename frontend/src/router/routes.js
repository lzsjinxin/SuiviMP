export default [
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
    // {
    //     path: "*",
    //     name: "404",
    //     meta: { title: "Cette Page n'existe pas"},
    //     component: () => import("../views/404.vue"),
    //   }
]