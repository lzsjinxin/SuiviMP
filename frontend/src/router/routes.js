export default [
    {
        path: "/arrivals/",
        name: "arrivals",
        meta: { title: "Arrivages"},
        component: () => import("../views/ArrivalList.vue"),
    },
]