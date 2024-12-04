import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import Main from "./Layouts/Main.vue";
import { setThemeOnLoad } from "./theme";

setThemeOnLoad();

const meta = document.createElement('meta');
meta.name = 'color-scheme';
meta.content = 'dark light';
document.head.appendChild(meta);

createInertiaApp({
    title: (title) => `Meliorae ${title}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        let page = pages[`./Pages/${name}.vue`];
        
        page.default.layout = page.default.layout || Main;
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el);
    },
    progress: {
        color: "#DB1414",
        showSpinner: false,
    },
});

// Add this to handle cart updates after checkout
router.on('finish', (event) => {
    if (event.detail.page.response.headers['x-trigger-cart-update']) {
        document.dispatchEvent(new CustomEvent('cart-updated'));
    }
});