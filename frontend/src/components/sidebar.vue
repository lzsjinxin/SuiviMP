<script>
import { ref, onMounted, onUnmounted } from 'vue';
// import { ChevronDownIcon } from "@zhuowenli/vue-feather-icons";
import simplebar from "simplebar-vue";
import logoWhite from "@/assets/images/logo-white.svg";
import logoDark from "@/assets/images/logo-dark.svg";

export default {
    data() {
        return {
            logoDark: logoDark,
            logoWhite: logoWhite,
           }
    },
    setup() {
        const currentLogo = ref(logoDark);

        const updateLogo = () => {
            const isDarkTheme = document.body.getAttribute("data-pc-theme") === "dark";
            currentLogo.value = isDarkTheme ? logoWhite : logoDark;
        };
        

        onMounted(() => {
            updateLogo();

            const observer = new MutationObserver(() => {
                updateLogo();
            });

            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['data-pc-theme']
            });

            onUnmounted(() => {
                observer.disconnect();
            });
        });

        return { currentLogo };
    },
    components: {
            simplebar
    },
    methods: {
        changeLayoutType(layoutType) {
            // Update the layout type in the store
            this.$store.commit('changeLayoutType', { layoutType });
            // Set the layout attribute based on the layout type
            document.body.setAttribute('data-pc-layout', layoutType);
        },
    },
    computed: {
        layoutType: {
            get() {
                return this.$store.state.layout.layoutType;
            },
            set(layoutType) {
                this.$store.commit('changeLayoutType', { layoutType });
            },
        },
    },
    watch: {
        layoutType: {
            immediate: true,
            deep: true,
            handler(newVal, oldVal) {
                if (newVal !== oldVal) {
                    switch (newVal) {
                        case "horizontal":
                            document.body.setAttribute(
                                "data-pc-layout",
                                "horizontal"
                            );
                            break;
                        case "vertical":
                            document.body.setAttribute("data-pc-layout", "vertical");
                    }
                }
            },
        },
    },
    mounted() {
        const activeListItem = document.querySelector("li.active");
        if (activeListItem) {
            const parentElementOrSelf = activeListItem?.parentElement ? activeListItem.parentElement : activeListItem;
            if (parentElementOrSelf && !parentElementOrSelf.classList.contains("pc-navbar")) {
                const closestItem = parentElementOrSelf.parentElement.closest(".pc-item");
                if (closestItem) {
                    closestItem.classList.add("active");
                    closestItem.children[1].classList.add('show')
                }
            }
          
            /**
             * Sidebar menu collapse
             */
            if (document.querySelectorAll(".navbar-content .collapse")) {
                let collapses = document.querySelectorAll(".navbar-content .collapse");

                collapses.forEach((collapse) => {
                    // Hide sibling collapses on `show.bs.collapse`
                    collapse.addEventListener("show.bs.collapse", (e) => {
                        e.stopPropagation();
                        let closestCollapse = collapse.parentElement.closest(".collapse");
                        if (closestCollapse) {
                            let siblingCollapses =
                                closestCollapse.querySelectorAll(".collapse");
                            siblingCollapses.forEach((siblingCollapse) => {
                                if (siblingCollapse.classList.contains("show")) {
                                    siblingCollapse.classList.remove("show");
                                    siblingCollapse.parentElement.firstChild.setAttribute("aria-expanded", "false");
                                }
                            });
                        } else {
                            let getSiblings = (elem) => {
                                // Setup siblings array and get the first sibling
                                let siblings = [];
                                let sibling = elem.parentNode.firstChild;
                                // Loop through each sibling and push to the array
                                while (sibling) {
                                    if (sibling.nodeType === 1 && sibling !== elem) {
                                        siblings.push(sibling);
                                    }
                                    sibling = sibling.nextSibling;
                                }
                                return siblings;
                            };
                            let siblings = getSiblings(collapse.parentElement);
                            siblings.forEach((item) => {
                                if (item.childNodes.length > 2) {
                                    item.firstElementChild.setAttribute("aria-expanded", "false");
                                    item.firstElementChild.classList.remove("active");
                                }
                                let ids = item.querySelectorAll("*[id]");
                                ids.forEach((item1) => {
                                    item1.classList.remove("show");
                                    item1.parentElement.firstChild.setAttribute("aria-expanded", "false");
                                    item1.parentElement.firstChild.classList.remove("active");
                                    if (item1.childNodes.length > 2) {
                                        let val = item1.querySelectorAll("ul li a");

                                        val.forEach((subitem) => {
                                            if (subitem.hasAttribute("aria-expanded"))
                                                subitem.setAttribute("aria-expanded", "false");
                                        });
                                    }
                                });
                            });
                        }
                    });

                    // Hide nested collapses on `hide.bs.collapse`
                    collapse.addEventListener("hide.bs.collapse", (e) => {
                        e.stopPropagation();
                        let childCollapses = collapse.querySelectorAll(".collapse");
                        childCollapses.forEach((childCollapse) => {
                            let childCollapseInstance = childCollapse;
                            childCollapseInstance.classList.remove("show");
                            childCollapseInstance.parentElement.firstChild.setAttribute("aria-expanded", "false");
                        });
                    });
                });
            }


        } else {
            console.error("No list item with class 'active' found.");
        }
    }
};
</script>

<template>
    <div class="navbar-wrapper" id="navbar-wrapper">
        <div class="m-header">
            <router-link to="/" class="b-brand text-primary">
                <!-- ========  Logo   ============ -->
                <img v-if="currentLogo === logoDark" :src="logoDark" alt="logo image" class="logo-lg custom_logo">
                <img v-else :src="logoWhite" alt="logo image" class="logo-lg custom_logo">
                <img src="@/assets/images/favicon.svg" alt="" class="logo logo-sm"> <span class="badge bg-brand-color-2 rounded-pill ms-1 theme-version">v1.0</span>
            </router-link>
        </div>
        <simplebar data-simplebar class="navbar-content pc-trigger">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>logistique</label>
                </li>


                <li class="pc-item pc-hasmenu">
                    <BLink class="pc-link" data-bs-toggle="collapse" href="#arrivals" role="button" aria-expanded="false" aria-controls="arrivals">
                        <span class="pc-micon">
                            <PhTruck :size="32" />
                        </span>
                        <span class="pc-mtext">Arrivages</span><span class="pc-arrow">
                            <PhCaretDown :size="32" weight="fill" />
                        </span>
                    </BLink>
                    <div class="collapse" id="arrivals">
                        <ul class="pc-submenu">
                            <li class="pc-item" :class="{ 'active': $route.path === '/arrivals/new' }"><router-link class="pc-link" to="/arrivals/new"> <PhPlusCircle :size="17" weight="bold" />Ajouter un Arrivage</router-link>
                            </li>
                            <li class="pc-item" :class="{ 'active': $route.path === '/arrivals' }"><router-link class="pc-link" to="/arrivals">  <PhList :size="17" weight="bold" />Liste des Arrivages</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="pc-item pc-hasmenu">
                    <BLink class="pc-link" data-bs-toggle="collapse" href="#mp" role="button" aria-expanded="false" aria-controls="mp">
                        <span class="pc-micon">
                            <PhPackage :size="32" weight="duotone" />
                        </span>
                        <span class="pc-mtext">Matières Premieres</span><span class="pc-arrow">
                            <PhCaretDown :size="32" weight="fill" />
                        </span>
                    </BLink>
                    <div class="collapse" id="mp">
                        <ul class="pc-submenu">
                            <li class="pc-item" :class="{ 'active': $route.path === '/mp/new' }"><router-link class="pc-link" to="/mp/new"> <PhPlusCircle :size="17" weight="bold" />Ajouter des Matières premieres</router-link>
                            </li>
                            <li class="pc-item" :class="{ 'active': $route.path === '/mp' }"><router-link class="pc-link" to="/mp">  <PhList :size="17" weight="bold" />Liste des Matières premieres</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="pc-item pc-hasmenu">
                    <BLink class="pc-link" data-bs-toggle="collapse" href="#units" role="button" aria-expanded="false" aria-controls="units">
                        <span class="pc-micon">
                            <PhRuler :size="32" weight="duotone" />
                        </span>
                        <span class="pc-mtext">Unités</span><span class="pc-arrow">
                            <PhCaretDown :size="32" weight="fill" />
                        </span>
                    </BLink>
                    <div class="collapse" id="units">
                        <ul class="pc-submenu">
                            <li class="pc-item" :class="{ 'active': $route.path === '/units/new' }"><router-link class="pc-link" to="/units/new"> <PhPlusCircle :size="17" weight="bold" />Ajouter des Unités</router-link>
                            </li>
                            <li class="pc-item" :class="{ 'active': $route.path === '/units' }"><router-link class="pc-link" to="/units">  <PhList :size="17" weight="bold" />Liste des Unités</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="pc-item pc-hasmenu">
                    <BLink class="pc-link" data-bs-toggle="collapse" href="#location" role="button" aria-expanded="false" aria-controls="location">
                        <span class="pc-micon">
                            <PhWarehouse :size="32" weight="duotone" />
                        </span>
                        <span class="pc-mtext">Emplacements</span><span class="pc-arrow">
                            <PhCaretDown  :size="32" weight="fill" />
                        </span>
                    </BLink>
                    <div class="collapse" id="location">
                        <ul class="pc-submenu">
                            <li class="pc-item" :class="{ 'active': $route.path === '/locations/new' }"><router-link class="pc-link" to="/locations/new"> <PhPlusCircle :size="17" weight="bold" />Ajouter des Emplacements</router-link>
                            </li>
                            <li class="pc-item" :class="{ 'active': $route.path === '/locations' }"><router-link class="pc-link" to="/locations">  <PhList :size="17" weight="bold" />Liste des Emplacements</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="pc-item pc-hasmenu">
                    <BLink class="pc-link" data-bs-toggle="collapse" href="#tier" role="button" aria-expanded="false" aria-controls="tier">
                        <span class="pc-micon">
                            <PhHandshake :size="32" weight="duotone" />
                        </span>
                        <span class="pc-mtext">Tiers</span><span class="pc-arrow">
                            <PhCaretDown  :size="32" weight="fill" />
                        </span>
                    </BLink>
                    <div class="collapse" id="tier">
                        <ul class="pc-submenu">
                            <li class="pc-item" :class="{ 'active': $route.path === '/tiers/new' }"><router-link class="pc-link" to="/tiers/new"> <PhPlusCircle :size="17" weight="bold" />Ajouter des Tiers</router-link>
                            </li>
                            <li class="pc-item" :class="{ 'active': $route.path === '/tiers' }"><router-link class="pc-link" to="/tiers">  <PhList :size="17" weight="bold" />Liste des Tiers</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
                                <li class="pc-item pc-hasmenu">
                    <BLink class="pc-link" data-bs-toggle="collapse" href="#departments" role="button" aria-expanded="false" aria-controls="departments">
                        <span class="pc-micon">
                            <PhUsersThree :size="32" weight="duotone" />
                        </span>
                        <span class="pc-mtext">Departements</span><span class="pc-arrow">
                            <PhCaretDown :size="32" weight="fill" />
                        </span>
                    </BLink>
                    <div class="collapse" id="departments">
                        <ul class="pc-submenu">
                            <li class="pc-item" :class="{ 'active': $route.path === '/departments/new' }"><router-link class="pc-link" to="/departments/new"> <PhPlusCircle :size="17" weight="bold" />Ajouter des Departements</router-link>
                            </li>
                            <li class="pc-item" :class="{ 'active': $route.path === '/departments' }"><router-link class="pc-link" to="/departments">  <PhList :size="17" weight="bold" />Liste des Departements</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="pc-item pc-hasmenu">
                    <BLink class="pc-link" data-bs-toggle="collapse" href="#users" role="button" aria-expanded="false" aria-controls="users">
                        <span class="pc-micon">
                            <PhUser :size="32" weight="duotone" />
                        </span>
                        <span class="pc-mtext">Utilisateurs</span><span class="pc-arrow">
                            <PhCaretDown  :size="32" weight="fill" />
                        </span>
                    </BLink>
                    <div class="collapse" id="users">
                        <ul class="pc-submenu">
                            <li class="pc-item" :class="{ 'active': $route.path === '/users/new' }"><router-link class="pc-link" to="/users/new"> <PhPlusCircle :size="17" weight="bold" />Ajouter des Utilisateurs</router-link>
                            </li>
                            <li class="pc-item" :class="{ 'active': $route.path === '/users' }"><router-link class="pc-link" to="/users">  <PhList :size="17" weight="bold" />Liste des Utilisateurs</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </simplebar>
        <BCard no-body class="pc-user-card">
            <BCardBody>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img src="@/assets/images/user/avatar-1.jpg" alt="user-image" class="user-avtar wid-45 rounded-circle">
                    </div>
                    <div class="flex-grow-1 ms-3 me-2">
                        <h6 class="mb-0">User N°01</h6>
                        <small>Role</small>
                    </div>
                    <BDropdown variant="purple" dropup no-caret toggle-class="p-0">
                        <template v-slot:button-content>
                            <span class="btn btn-icon btn-link-secondary avtar arrow-none p-0 dropdown-toggle">
                                <i class="ph-duotone ph-windows-logo"></i>
                            </span>
                        </template>
                        <BRow >
                            <BCol >
                                <BDropdownItem class="pc-user-links p-0">
                                    <i class="ph-duotone ph-power"></i> <br>
                                    <span>Logout</span>
                                </BDropdownItem>
                                <BDropdownDivider />
                            </BCol>
                        </BRow>
                    </BDropdown>
                </div>
            </BCardBody>
        </BCard>
    </div>
    <a-back-top />
</template>

