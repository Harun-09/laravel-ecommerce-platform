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
    home: () => import('./Pages/home-page'),
    'products.show': () => import('./Pages/product-show-page'),
    'category.show': () => import('./Pages/category-page'),
    'cart.index': () => import('./Pages/cart-page'),
    'checkout.index': () => import('./Pages/checkout-page'),
};

if (routeName && routeBasedModules[routeName]) {
    loadPageModule(`route:${routeName}`, routeBasedModules[routeName]);
}

const domBasedModules = [
    { key: 'dom:home', selector: '[data-hero-slider]', loader: () => import('./Pages/home-page') },
    { key: 'dom:product-show', selector: '#product-page-config', loader: () => import('./Pages/product-show-page') },
    { key: 'dom:category', selector: '#category-sort', loader: () => import('./Pages/category-page') },
    { key: 'dom:cart', selector: '#cart-page', loader: () => import('./Pages/cart-page') },
    { key: 'dom:checkout', selector: '#checkout-form', loader: () => import('./Pages/checkout-page') },
];

domBasedModules.forEach(({ key, selector, loader }) => {
    if (document.querySelector(selector)) {
        loadPageModule(key, loader);
    }
});
