export default [
    {
        path: "/arrivals/",
        name: "arrivals",
        meta: { title: "Arrivages"},
        component: () => import("../views/ArrivalList.vue"),
    },
    {
        path: "/arrivals/:id(\\d+)",
        name: "ArrivalModify",
        props: (route) => ({ id: Number(route.params.id) }),
        meta: { title: "Modifier Arrivage"},
        component: () => import("../views/ArrivalModify.vue"),
    },
]