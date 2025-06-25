import { createWebHistory, createRouter } from 'vue-router';
import routes    from './routes';
import appConfig from '../../app.config';
import store     from '@/state/store';      // Vuex root store (auth module lives inside)

/* --------------------------------------------------------------------------
 * Router instance
 * ------------------------------------------------------------------------ */
const router = createRouter({
    history: createWebHistory('/'),
    routes,
});

/* --------------------------------------------------------------------------
 * 1) Global beforeEach — department / login guard
 * ------------------------------------------------------------------------ */
router.beforeEach((to, from, next) => {
    const allowed = to.meta.departments;              // e.g. ['Warehouse','Quality']

    // Route is public → continue.
    if (!allowed) return next();

    // Not logged in → send to login, remember intended destination.
    if (!store.getters['auth/isLogged']) {
        return next({ path: '/login', query: { redirect: to.fullPath } });
    }

    // Logged in — check department.
    const userDept = store.getters['auth/department'];

    if (allowed.includes(userDept)) return next();    // authorised
    return next({ name: '403' });                     // forbidden
});

/* --------------------------------------------------------------------------
 * 2) Existing beforeResolve (kept as-is)
 * ------------------------------------------------------------------------ */
router.beforeResolve(async (routeTo, routeFrom, next) => {
    try {
        for (const route of routeTo.matched) {
            await new Promise((resolve, reject) => {
                if (route.meta?.beforeResolve) {
                    route.meta.beforeResolve(routeTo, routeFrom, (...args) => {
                        if (args.length) {
                            next(...args);            // redirected inside hook
                            return reject(new Error('Redirected'));
                        }
                        resolve();
                    });
                } else {
                    resolve();
                }
            });
        }
    } catch {
        return;                              // a redirect already happened
    }

    // Set page title after guards/meta hooks have passed
    document.title = `${routeTo.meta.title} | ${appConfig.title}`;
    next();
});

export default router;
