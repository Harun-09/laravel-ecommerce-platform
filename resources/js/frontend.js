import './bootstrap';
import './layouts/frontend-layout';

const routeName = document.body?.dataset.routeName || '';
const loadedModules = new Set();

function loadPageModule(key, loader) {
    if (loadedModules.has(key)) {
        return;
    }

    loadedModules.add(key);
    loader().catch((error) => {
        console.error(`Failed to load page module: ${key}`, error);
    });
}

const routeBasedModules = {
    home: () => import('./pages/home-page'),
    'products.show': () => import('./pages/product-show-page'),
    'category.show': () => import('./pages/category-page'),
    'cart.index': () => import('./pages/cart-page'),
    'checkout.index': () => import('./pages/checkout-page'),
};

if (routeName && routeBasedModules[routeName]) {
    loadPageModule(`route:${routeName}`, routeBasedModules[routeName]);
}

const domBasedModules = [
    { key: 'dom:home', selector: '[data-hero-slider]', loader: () => import('./pages/home-page') },
    { key: 'dom:product-show', selector: '#product-page-config', loader: () => import('./pages/product-show-page') },
    { key: 'dom:category', selector: '#category-sort', loader: () => import('./pages/category-page') },
    { key: 'dom:cart', selector: '#cart-page', loader: () => import('./pages/cart-page') },
    { key: 'dom:checkout', selector: '#checkout-form', loader: () => import('./pages/checkout-page') },
];

domBasedModules.forEach(({ key, selector, loader }) => {
    if (document.querySelector(selector)) {
        loadPageModule(key, loader);
    }
});
