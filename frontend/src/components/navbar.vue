<script>
// import simplebar from "simplebar-vue";
// import rightBar from "./right-bar.vue";

export default {
    name: "NAVBAR",
    components: {
        // simplebar
    },
    data() {
        return {
            isFullscreen: false,
            isSidebarHidden: false,
            currentMode: 'light',
            show: false,
            languages: [
                {
                    language: "en",
                    title: "English",
                    subtitle: "UK"
                },
                {
                    language: "fr",
                    title: "français",
                    subtitle: "French"
                },
                {
                    language: "ro",
                    title: "Română",
                    subtitle: "Romanian"
                },
                {
                    language: "cn",
                    title: "中国人",
                    subtitle: "Chinese"
                },
            ],
            current_language: this.$i18n.locale,
            text: null,
            subtitle: null,
            value: null,
        };
    },
    mounted() {
        // Add event listener for keydown events
        document.addEventListener('keydown', this.handleKeyDown);

        this.value = this.languages.find((x) => x.language === this.$i18n.locale);
        this.text = this.value.title;
        this.subtitle = this.value.subtitle;
    },
    beforeUnmount() {
        // Remove event listener when component is unmounted
        document.removeEventListener('keydown', this.handleKeyDown);
    },
    methods: {
        handleSearch(event) {
            // console.log('handleSearch called');
            // Prevent the default action (e.g., browser search)
            event.preventDefault();
        },
        handleKeyDown(event) {
            // console.log('handleKeyDown called', event.key, event.ctrlKey);
            // Check if Ctrl+K is pressed
            if (event.ctrlKey && event.key === 'k') {
                // Prevent the default action (e.g., browser search)
                event.preventDefault();
                // console.log('Ctrl+K detected');
                // Focus on the search input field
                const searchInput = document.querySelector('#serchFildid');
                if (searchInput) {
                    // console.log('Search input found, focusing');
                    searchInput.focus();
                } else {
                    // console.log('Search input not found');
                }
            }
        },
        setLanguage(locale, country) {
            this.$i18n.locale = locale;
            this.current_language = locale;
            this.text = country;
        },
        changeMode(mode) {
            this.currentMode = mode;
            if (mode === "dark") {
                document.body.setAttribute("data-pc-theme", "dark");
                document.body.setAttribute("data-topbar", "dark");
                document.body.classList.remove("mode-auto");
            } else if (mode === "auto") {
                document.body.setAttribute("data-pc-theme", "light");
                document.body.setAttribute("data-topbar", "light");
                document.body.classList.add("mode-auto");
            } else {
                document.body.setAttribute("data-pc-theme", "light");
                document.body.setAttribute("data-topbar", "light");
                document.body.classList.remove("mode-auto");
            }
        },
        toggleSidebar() {
            this.$store.commit('toggleSidebar');
        },
        toggleMobileSidebar() {
            this.$store.commit('toggleMobileSidebar');
        },
        toggleRightSidebar() {
            this.$emit('toggleRightSidebar'); // Emit an event to toggle the right sidebar
        }
    },
};
</script>

<template>
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide" @click="toggleSidebar">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse" @click="toggleMobileSidebar">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <!-- <li class="dropdown pc-h-item d-inline-flex d-md-none">
                    <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ph-duotone ph-magnifying-glass"></i>
                    </a>
                    <div class="dropdown-menu pc-h-dropdown drp-search">
                        <form class="px-3">
                            <div class="form-group mb-0 d-flex align-items-center">
                                <input type="search" class="form-control border-0 shadow-none"
                                    placeholder="Search here. . ." />
                                <button class="btn btn-light-secondary btn-search"><kbd>ctrl+k</kbd></button>
                            </div>
                        </form>
                    </div>
                </li> -->

                <!-- <li class="pc-h-item d-none d-md-inline-flex">
                    <form class="form-search">
                        <i class="ph-duotone ph-magnifying-glass icon-search"></i>
                        <input type="search" ref="searchInput" class="form-control" placeholder="Search. . ."
                            id="serchFildid">
                        <button class="btn btn-light-secondary btn-search" style="padding: 0"
                            @click.prevent="handleSearch"><kbd>ctrl+k</kbd></button>
                    </form>
                </li> -->
            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                <!-- <BDropdown variant="transparent" class="pc-h-item d-none d-md-inline-flex"
                    toggle-class="text-reset pc-head-link dropdown-btn arrow-none dropdown p-0"
                    menu-class="dropdown-menu-end dropdown-qta p-0 mt-3" aria-haspopup="true">
                    <template #button-content><span class="text-muted pc-head-link"><i
                                class="ph-duotone ph-circles-four"></i></span>
                    </template>
                    <div class="overflow-hidden dropdown">
                        <div class="qta-links m-n1">
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-shopping-cart"></i>
                                <span>E-commerce</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-lifebuoy"></i>
                                <span>Helpdesk</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-scroll"></i>
                                <span>Invoice</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-books"></i>
                                <span>Online Courses</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-envelope-open"></i>
                                <span>Mail</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-identification-badge"></i>
                                <span>Membership</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-chats-circle"></i>
                                <span>Chat</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-currency-circle-dollar"></i>
                                <span>Plans</span>
                            </a>
                            <a href="#!" class="dropdown-item">
                                <i class="ph-duotone ph-user-circle"></i>
                                <span>Users</span>
                            </a>
                        </div>
                    </div>
                </BDropdown>
                <BDropdown variant="transparent" class="pc-h-item d-none d-md-inline-flex"
                    toggle-class="text-reset dropdown-btn pc-head-link arrow-none me-0" menu-class="dropdown-menu-end"
                    aria-haspopup="true" :offset="{ alignmentAxis: -150, crossAxis: 0, mainAxis: 20 }">
                    <template #button-content><span class="text-muted pc-head-link"><i
                                :class="currentMode === 'dark' ? 'ph-duotone ph-moon' : (currentMode === 'light' ? 'ph-duotone ph-sun-dim' : 'ph-duotone ph-cpu')"></i></span>
                    </template>
                    <a href="#!" class="dropdown-item" @click="changeMode('dark')">
                        <i class="ph-duotone ph-moon"></i>
                        <span>Dark</span>
                    </a>
                    <a href="#!" class="dropdown-item" @click="changeMode('light')">
                        <i class="ph-duotone ph-sun-dim"></i>
                        <span>Light</span>
                    </a>
                    <a href="#!" class="dropdown-item" @click="changeMode('auto')">
                        <i class="ph-duotone ph-cpu"></i>
                        <span>Default</span>
                    </a>
                </BDropdown> -->
                <!-- <li class="dropdown pc-h-item d-none d-md-inline-flex">
                    <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ph-duotone ph-translate"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown lng-dropdown">
                        <a href="#!" class="dropdown-item" data-lng="en" v-for="(entry, i) in languages"
                            :key="`Lang${i}`" :value="entry"
                            @click="setLanguage(entry.language, entry.title, entry.subtitle)"
                            :link-class="{ active: entry.language === current_language }">
                            <span>
                                {{ entry.title }}
                                <small>({{ entry.subtitle }})</small>
                            </span>
                        </a>
                    </div>
                </li> -->
                <!-- <rightBar /> -->
                <!-- <BDropdown variant="transparent" auto-close="outside" class="pc-h-item card-header-dropdown pb-0"
                    toggle-class="text-reset dropdown-btn pc-head-link arrow-none me-0" menu-class="dropdown-menu-end"
                    aria-haspopup="true" :offset="{ alignmentAxis: -140, crossAxis: 0, mainAxis: 20 }">
                    <template #button-content><span class="text-muted pc-head-link"><i
                                class="ph-duotone ph-diamonds-four"></i></span>
                    </template>
                    <a href="#!" class="dropdown-item">
                        <i class="ph-duotone ph-user"></i>
                        <span>My Account</span>
                    </a>
                    <a href="#!" class="dropdown-item">
                        <i class="ph-duotone ph-gear"></i>
                        <span>Settings</span>
                    </a>
                    <a href="#!" class="dropdown-item">
                        <i class="ph-duotone ph-lifebuoy"></i>
                        <span>Support</span>
                    </a>
                    <a href="#!" class="dropdown-item">
                        <i class="ph-duotone ph-lock-key"></i>
                        <span>Lock Screen</span>
                    </a>
                    <a href="#!" class="dropdown-item">
                        <i class="ph-duotone ph-power"></i>
                        <span>Logout</span>
                    </a>
                </BDropdown> -->
<!--                <BDropdown v-model="show" variant="transparent" auto-close="outside"-->
<!--                    class="pc-h-item card-header-dropdown pb-0"-->
<!--                    toggle-class="text-reset dropdown-btn pc-head-link arrow-none me-0"-->
<!--                    menu-class="dropdown-menu-end dropdown-notification pc-h-dropdown" aria-haspopup="true"-->
<!--                    :offset="{ alignmentAxis: -145, crossAxis: 0, mainAxis: 20 }">-->
<!--                    <template #button-content>-->
<!--                        <span class="text-muted pc-head-link"><i class="ph-duotone ph-bell"></i>-->
<!--                            <span class="badge bg-success pc-h-badge">3</span>-->
<!--                        </span>-->
<!--                    </template>-->
<!--                    <BDropdownHeader class=" align-items-center justify-content-between">-->
<!--                        <BRow class="align-items-center justify-content-between">-->
<!--                            <BCol xl="8" sm="6">-->
<!--                                <h5 class="m-0">Notifications</h5>-->
<!--                            </BCol>-->
<!--                            <BCol xl="4" sm="6" class="text-end">-->
<!--                                <ul class="list-inline ms-auto mb-0">-->
<!--                                    <li class="list-inline-item">-->
<!--                                        <router-link to="/mail" class="avtar avtar-s btn-link-hover-primary">-->
<!--                                            <i class="ti ti-link f-18"></i>-->
<!--                                        </router-link>-->
<!--                                    </li>-->
<!--                                </ul>-->
<!--                            </BCol>-->
<!--                        </BRow>-->

<!--                    </BDropdownHeader>-->
<!--                    <div>-->
<!--                        <simplebar class="dropdown-body text-wrap header-notification-scroll position-relative"-->
<!--                            style="max-height: calc(100vh - 235px)">-->
<!--                            <ul class="list-group list-group-flush">-->
<!--                                <li class="list-group-item">-->
<!--                                    <p class="text-span">Today</p>-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="flex-shrink-0">-->
<!--                                            <img src="@/assets/images/user/avatar-2.jpg" alt="user-image"-->
<!--                                                class="user-avtar avtar avtar-s">-->
<!--                                        </div>-->
<!--                                        <div class="flex-grow-1 ms-3">-->
<!--                                            <div class="d-flex">-->
<!--                                                <div class="flex-grow-1 me-3 position-relative">-->
<!--                                                    <h6 class="mb-0 text-truncate">Keefe Bond added new tags to 💪-->
<!--                                                        Design system</h6>-->
<!--                                                </div>-->
<!--                                                <div class="flex-shrink-0">-->
<!--                                                    <span class="text-sm">2 min ago</span>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <p class="position-relative mt-1 mb-2"><br><span class="text-truncate">Lorem-->
<!--                                                    Ipsum-->
<!--                                                    has been-->
<!--                                                    the industry's standard dummy text ever since the 1500s.</span></p>-->
<!--                                            <span class="badge bg-light-primary border border-primary me-1 mt-1">web-->
<!--                                                design</span>-->
<!--                                            <span-->
<!--                                                class="badge bg-light-warning border border-warning me-1 mt-1">Dashobard</span>-->
<!--                                            <span class="badge bg-light-success border border-success me-1 mt-1">Design-->
<!--                                                System</span>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </li>-->
<!--                                <li class="list-group-item">-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="flex-shrink-0">-->
<!--                                            <div class="avtar avtar-s bg-light-primary">-->
<!--                                                <i class="ph-duotone ph-chats-teardrop f-18"></i>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="flex-grow-1 ms-3">-->
<!--                                            <div class="d-flex">-->
<!--                                                <div class="flex-grow-1 me-3 position-relative">-->
<!--                                                    <h6 class="mb-0 text-truncate">Message</h6>-->
<!--                                                </div>-->
<!--                                                <div class="flex-shrink-0">-->
<!--                                                    <span class="text-sm text-muted">1 hour ago</span>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <p class="position-relative text-muted mt-1 mb-2"><br><span-->
<!--                                                    class="text-truncate">Lorem-->
<!--                                                    Ipsum-->
<!--                                                    has been the industry's standard dummy text ever since the-->
<!--                                                    1500s.</span></p>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </li>-->
<!--                                <li class="list-group-item">-->
<!--                                    <p class="text-span">Yesterday</p>-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="flex-shrink-0">-->
<!--                                            <div class="avtar avtar-s bg-light-danger">-->
<!--                                                <i class="ph-duotone ph-user f-18"></i>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="flex-grow-1 ms-3">-->
<!--                                            <div class="d-flex">-->
<!--                                                <div class="flex-grow-1 me-3 position-relative">-->
<!--                                                    <h5 class="mb-0 text-truncate">Challenge invitation</h5>-->
<!--                                                </div>-->
<!--                                                <div class="flex-shrink-0">-->
<!--                                                    <span class="text-sm text-muted">12 hour ago</span>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <p class="position-relative text-muted mt-1 mb-2"><br><span-->
<!--                                                    class="text-truncate"><strong>-->
<!--                                                        Jonny aber-->
<!--                                                    </strong> invites to join the challenge</span></p>-->
<!--                                            <button-->
<!--                                                class="btn btn-sm rounded-pill btn-outline-secondary me-2">Decline</button>-->
<!--                                            <button class="btn btn-sm rounded-pill btn-primary">Accept</button>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </li>-->
<!--                                <li class="list-group-item">-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="flex-shrink-0">-->
<!--                                            <div class="avtar avtar-s bg-light-info">-->
<!--                                                <i class="ph-duotone ph-notebook f-18"></i>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="flex-grow-1 ms-3">-->
<!--                                            <div class="d-flex">-->
<!--                                                <div class="flex-grow-1 me-3 position-relative">-->
<!--                                                    <h5 class="mb-0 text-truncate">Forms</h5>-->
<!--                                                </div>-->
<!--                                                <div class="flex-shrink-0">-->
<!--                                                    <span class="text-sm text-muted">2 hour ago</span>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <p class="position-relative text-muted mt-1 mb-2">Lorem Ipsum is simply-->
<!--                                                dummy text of the-->
<!--                                                printing and-->
<!--                                                typesetting industry. Lorem Ipsum has been the industry's standard-->
<!--                                                dummy text ever since the 1500s.</p>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </li>-->
<!--                                <li class="list-group-item">-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="flex-shrink-0">-->
<!--                                            <img src="@/assets/images/user/avatar-2.jpg" alt="user-image"-->
<!--                                                class="user-avtar avtar avtar-s">-->
<!--                                        </div>-->
<!--                                        <div class="flex-grow-1 ms-3">-->
<!--                                            <div class="d-flex">-->
<!--                                                <div class="flex-grow-1 me-3 position-relative">-->
<!--                                                    <h5 class="mb-0 text-truncate">Keefe Bond <span class="text-body">-->
<!--                                                            added new tags to </span>-->
<!--                                                        💪-->
<!--                                                        Design system</h5>-->
<!--                                                </div>-->
<!--                                                <div class="flex-shrink-0">-->
<!--                                                    <span class="text-sm text-muted">2 min ago</span>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <p class="position-relative text-muted mt-1 mb-2"><br><span-->
<!--                                                    class="text-truncate">Lorem-->
<!--                                                    Ipsum-->
<!--                                                    has been-->
<!--                                                    the industry's standard dummy text ever since the 1500s.</span></p>-->
<!--                                            <button-->
<!--                                                class="btn btn-sm rounded-pill btn-outline-secondary me-2">Decline</button>-->
<!--                                            <button class="btn btn-sm rounded-pill btn-primary">Accept</button>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </li>-->
<!--                                <li class="list-group-item">-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="flex-shrink-0">-->
<!--                                            <div class="avtar avtar-s bg-light-success">-->
<!--                                                <i class="ph-duotone ph-shield-checkered f-18"></i>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="flex-grow-1 ms-3">-->
<!--                                            <div class="d-flex">-->
<!--                                                <div class="flex-grow-1 me-3 position-relative">-->
<!--                                                    <h5 class="mb-0 text-truncate">Security</h5>-->
<!--                                                </div>-->
<!--                                                <div class="flex-shrink-0">-->
<!--                                                    <span class="text-sm text-muted">5 hour ago</span>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                            <p class="position-relative text-muted mt-1 mb-2">Lorem Ipsum is simply-->
<!--                                                dummy text of the-->
<!--                                                printing and-->
<!--                                                typesetting industry. Lorem Ipsum has been the industry's standard-->
<!--                                                dummy text ever since the 1500s.</p>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </simplebar>-->
<!--                    </div>-->
<!--                    <div class="dropdown-footer">-->
<!--                        <BRow class="g-3">-->
<!--                            <BCol Cols="6">-->
<!--                                <div class="d-grid"><button class="btn btn-primary">Archive all</button></div>-->
<!--                            </BCol>-->
<!--                            <BCol Cols="6">-->
<!--                                <div class="d-grid"><button class="btn btn-outline-secondary">Mark all as read</button>-->
<!--                                </div>-->
<!--                            </BCol>-->
<!--                        </BRow>-->
<!--                    </div>-->
<!--                </BDropdown>-->
                <!-- <BDropdown variant="transparent" auto-close="outside"
                    class="pc-h-item header-user-profile card-header-dropdown py-0"
                    toggle-class="text-reset dropdown-btn pc-head-link arrow-none me-0"
                    menu-class="dropdown-menu-end dropdown-user-profile dropdown-menu-end pc-h-dropdown"
                    aria-haspopup="true" :offset="{ alignmentAxis: -145, crossAxis: 0, mainAxis: 20 }">
                    <template #button-content><span class="text-muted"> <img src="@/assets/images/user/avatar-2.jpg"
                                alt="user-image" class="user-avtar">
                        </span>
                    </template>
                    <div class="dropdown-header d-flex align-items-center justify-content-between">
                        <h4 class="m-0">Profile</h4>
                    </div>
                    <div class="dropdown-body">
                        <simplebar data-simplebar class="profile-notification-scroll position-relative"
                            style="max-height: calc(100vh - 235px)">
                            <ul class="list-group list-group-flush w-100">
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img src="@/assets/images/user/avatar-2.jpg" alt="user-image"
                                                class="wid-50 rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 mx-3">
                                            <h5 class="mb-0">Carson Darrin</h5>
                                            <a class="link-primary"
                                                href="mailto:carson.darrin@company.io">carson.darrin@company.io</a>
                                        </div>
                                        <span class="badge bg-primary">PRO</span>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-key"></i>
                                            <span>Change password</span>
                                        </span>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-envelope-simple"></i>
                                            <span>Recently mail</span>
                                        </span>
                                        <div class="user-group">
                                            <img src="@/assets/images/user/avatar-1.jpg" alt="user-image" class="avtar">
                                            <img src="@/assets/images/user/avatar-2.jpg" alt="user-image" class="avtar">
                                            <img src="@/assets/images/user/avatar-3.jpg" alt="user-image" class="avtar">
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-calendar-blank"></i>
                                            <span>Schedule meetings</span>
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-heart"></i>
                                            <span>Favorite</span>
                                        </span>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-arrow-circle-down"></i>
                                            <span>Download</span>
                                        </span>
                                        <span class="avtar avtar-xs rounded-circle bg-danger text-white">10</span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <div class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-globe-hemisphere-west"></i>
                                            <span>Languages</span>
                                        </span>
                                        <span class="flex-shrink-0">
                                            <select
                                                class="form-select bg-transparent form-select-sm border-0 shadow-none">
                                                <option value="1">English</option>
                                                <option value="2">Spain</option>
                                                <option value="3">Arbic</option>
                                            </select>
                                        </span>
                                    </div>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-flag"></i>
                                            <span>Country</span>
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-user-circle"></i>
                                            <span>Edit profile</span>
                                        </span>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-star text-warning"></i>
                                            <span>Upgrade account</span>
                                            <span class="badge bg-light-success border border-success ms-2">NEW</span>
                                        </span>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-bell"></i>
                                            <span>Notifications</span>
                                        </span>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-gear-six"></i>
                                            <span>Settings</span>
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-plus-circle"></i>
                                            <span>Add account</span>
                                        </span>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <span class="d-flex align-items-center">
                                            <i class="ph-duotone ph-power"></i>
                                            <span>Logout</span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </simplebar>
                    </div>
                </BDropdown> -->
            </ul>
        </div>
    </div>
</template>