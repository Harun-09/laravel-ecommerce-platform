const defaultCurrency = {
    code: 'BDT',
    symbol: 'BDT ',
    symbol_position: 'prefix',
    decimals: 2,
    rate: 1,
};

const storefrontConfig = window.storefrontConfig || {};
const storefrontCurrency = {
    ...defaultCurrency,
    ...(storefrontConfig.currency && typeof storefrontConfig.currency === 'object' ? storefrontConfig.currency : {}),
};
const storefrontEndpoints = storefrontConfig.endpoints || {};

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function convertStoreMoney(amount) {
    const numericAmount = Number(amount);
    if (!Number.isFinite(numericAmount)) return 0;

    return numericAmount * Number(storefrontCurrency.rate || 1);
}

function formatStoreMoney(amount, options = {}) {
    const decimals = Number.isInteger(options.decimals)
        ? options.decimals
        : Number(storefrontCurrency.decimals ?? 2);

    const converted = convertStoreMoney(amount);
    const number = converted.toLocaleString(undefined, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });

    const symbol = String(storefrontCurrency.symbol || `${storefrontCurrency.code} `);
    const position = String(storefrontCurrency.symbol_position || 'prefix');
    const withCode = Boolean(options.withCode);

    let result = position === 'suffix' ? `${number}${symbol}` : `${symbol}${number}`;
    result = result.replace(/\s{2,}/g, ' ').trim();

    return withCode ? `${storefrontCurrency.code} ${result}` : result;
}

function notifyUser(message, type = 'success') {
    if (!message) return;

    const container = document.getElementById('toast-container');
    if (!container) {
        console.log(message);
        return;
    }

    const normalizedType = ['success', 'error', 'warning'].includes(type) ? type : 'success';
    const toast = document.createElement('div');
    toast.className = `toast-notice toast-${normalizedType}`;

    const messageNode = document.createElement('span');
    messageNode.textContent = message;

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'toast-close';
    closeBtn.textContent = 'x';
    closeBtn.setAttribute('aria-label', 'Close notification');

    closeBtn.addEventListener('click', () => toast.remove());
    toast.appendChild(messageNode);
    toast.appendChild(closeBtn);
    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3500);
}

function ajaxHeaders() {
    const headers = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    return headers;
}

async function parseJsonResponse(response, fallbackMessage) {
    if (response.redirected) {
        window.location.href = response.url;
        return null;
    }

    const contentType = response.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
        throw new Error(fallbackMessage);
    }

    const payload = await response.json();
    if (!response.ok) {
        const firstError = payload?.errors ? Object.values(payload.errors)[0]?.[0] : null;
        throw new Error(firstError || payload?.message || fallbackMessage);
    }

    return payload;
}

function updateCartCount() {
    const cartCountUrl = storefrontEndpoints.cartCount;
    if (!cartCountUrl) return;

    fetch(cartCountUrl, {
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            const counter = document.getElementById('cart-count');
            if (!counter || typeof data?.count === 'undefined') return;

            counter.textContent = data.count;
        })
        .catch(() => {
            // Silently ignore badge sync failures.
        });
}

function initLiveSearch() {
    const searchForms = document.querySelectorAll('.js-live-search');
    if (!searchForms.length) return;

    searchForms.forEach((form) => {
        const input = form.querySelector('input[name="q"]');
        const suggestionsBox = form.querySelector('.search-suggestions');
        const suggestionsUrl = form.dataset.suggestionUrl;

        if (!input || !suggestionsBox || !suggestionsUrl) return;

        let results = [];
        let activeIndex = -1;
        let debounceTimer = null;
        let requestController = null;

        const closeSuggestions = () => {
            suggestionsBox.hidden = true;
            suggestionsBox.innerHTML = '';
            input.setAttribute('aria-expanded', 'false');
            results = [];
            activeIndex = -1;
        };

        const setActiveSuggestion = (index) => {
            const suggestionLinks = suggestionsBox.querySelectorAll('.search-suggestion-item');
            if (!suggestionLinks.length) {
                activeIndex = -1;
                return;
            }

            if (index < 0) {
                activeIndex = suggestionLinks.length - 1;
            } else if (index >= suggestionLinks.length) {
                activeIndex = 0;
            } else {
                activeIndex = index;
            }

            suggestionLinks.forEach((link, linkIndex) => {
                link.classList.toggle('is-active', linkIndex === activeIndex);
            });
        };

        const renderSuggestions = (items, query) => {
            suggestionsBox.innerHTML = '';
            activeIndex = -1;
            results = items;

            if (!items.length) {
                closeSuggestions();
                return;
            }

            items.forEach((item) => {
                const link = document.createElement('a');
                link.href = item.url;
                link.className = 'search-suggestion-item';

                const image = document.createElement('img');
                image.src = item.image;
                image.alt = item.name;
                image.className = 'search-suggestion-image';
                image.loading = 'lazy';

                const name = document.createElement('div');
                name.className = 'search-suggestion-name';
                name.textContent = item.name;

                const priceWrap = document.createElement('div');
                priceWrap.className = 'search-suggestion-price';

                const currentPrice = document.createElement('span');
                currentPrice.textContent = formatStoreMoney(item.price);
                priceWrap.appendChild(currentPrice);

                if (item.compare_price && Number(item.compare_price) > Number(item.price)) {
                    const oldPrice = document.createElement('span');
                    oldPrice.className = 'old';
                    oldPrice.textContent = formatStoreMoney(item.compare_price);
                    priceWrap.appendChild(oldPrice);
                }

                link.appendChild(image);
                link.appendChild(name);
                link.appendChild(priceWrap);
                suggestionsBox.appendChild(link);
            });

            const viewAllLink = document.createElement('a');
            viewAllLink.href = `${form.action}?q=${encodeURIComponent(query)}`;
            viewAllLink.className = 'search-suggestion-view-all';
            viewAllLink.textContent = `View all results for "${query}"`;
            suggestionsBox.appendChild(viewAllLink);

            suggestionsBox.hidden = false;
            input.setAttribute('aria-expanded', 'true');
        };

        const fetchSuggestions = (query) => {
            if (requestController) {
                requestController.abort();
            }

            requestController = new AbortController();
            const requestUrl = `${suggestionsUrl}?q=${encodeURIComponent(query)}`;

            fetch(requestUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: requestController.signal,
            })
                .then(async (response) => {
                    const contentType = response.headers.get('content-type') || '';
                    if (!response.ok || !contentType.includes('application/json')) {
                        throw new Error('Invalid suggestions response');
                    }

                    return response.json();
                })
                .then((data) => {
                    const currentQuery = input.value.trim();
                    if (currentQuery !== query) return;

                    const items = Array.isArray(data?.items) ? data.items : [];
                    renderSuggestions(items, query);
                })
                .catch((error) => {
                    if (error.name === 'AbortError') return;
                    closeSuggestions();
                });
        };

        input.addEventListener('input', () => {
            const query = input.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 1) {
                closeSuggestions();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetchSuggestions(query);
            }, 220);
        });

        input.addEventListener('focus', () => {
            const query = input.value.trim();
            if (query.length < 1) return;

            if (results.length) {
                suggestionsBox.hidden = false;
                input.setAttribute('aria-expanded', 'true');
                return;
            }

            fetchSuggestions(query);
        });

        input.addEventListener('keydown', (event) => {
            const suggestionLinks = suggestionsBox.querySelectorAll('.search-suggestion-item');
            if (!suggestionLinks.length) {
                if (event.key === 'Escape') {
                    closeSuggestions();
                }
                return;
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setActiveSuggestion(activeIndex + 1);
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActiveSuggestion(activeIndex - 1);
            }

            if (event.key === 'Enter' && activeIndex >= 0) {
                event.preventDefault();
                const activeLink = suggestionLinks[activeIndex];
                if (activeLink) {
                    window.location.href = activeLink.href;
                }
            }

            if (event.key === 'Escape') {
                closeSuggestions();
            }
        });

        input.addEventListener('blur', () => {
            setTimeout(() => {
                if (!form.contains(document.activeElement)) {
                    closeSuggestions();
                }
            }, 120);
        });

        document.addEventListener('click', (event) => {
            if (!form.contains(event.target)) {
                closeSuggestions();
            }
        });
    });
}

function initProductCardNavigation() {
    const cardSelector = '.product-card[data-product-url]';
    const interactiveSelector = 'a, button, input, select, textarea, label, [role="button"]';

    document.addEventListener('click', (event) => {
        const card = event.target.closest(cardSelector);
        if (!card) return;
        if (event.target.closest(interactiveSelector)) return;

        const destination = card.dataset.productUrl;
        if (destination) {
            window.location.href = destination;
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;

        const card = event.target.closest(cardSelector);
        if (!card) return;
        if (event.target.closest(interactiveSelector) && event.target !== card) return;

        event.preventDefault();
        const destination = card.dataset.productUrl;
        if (destination) {
            window.location.href = destination;
        }
    });
}

function addToCart(productId, quantity = 1, variationId = null) {
    const cartAddUrl = storefrontEndpoints.cartAdd;
    if (!cartAddUrl) {
        notifyUser('Cart service is unavailable.', 'error');
        return;
    }

    fetch(cartAddUrl, {
        method: 'POST',
        headers: ajaxHeaders(),
        body: JSON.stringify({
            product_id: productId,
            quantity,
            variation_id: variationId,
        }),
    })
        .then((response) => parseJsonResponse(response, 'Unexpected server response. Please refresh and try again.'))
        .then((data) => {
            if (!data) return;

            if (data.success) {
                updateCartCount();
            }

            notifyUser(data.message || 'Cart updated.', data.success ? 'success' : 'error');
        })
        .catch((error) => {
            notifyUser(error.message || 'Failed to add product to cart.', 'error');
        });
}

function toggleWishlist(productId, button) {
    const wishlistUrl = storefrontEndpoints.wishlistToggle;
    if (!wishlistUrl) {
        notifyUser('Wishlist service is unavailable.', 'error');
        return;
    }

    fetch(wishlistUrl, {
        method: 'POST',
        headers: ajaxHeaders(),
        body: JSON.stringify({ product_id: productId }),
    })
        .then((response) => parseJsonResponse(response, 'Please log in to manage wishlist.'))
        .then((data) => {
            if (!data || !data.success || !button) return;

            button.style.color = data.added ? '#ef4444' : '';
        })
        .catch((error) => {
            notifyUser(error.message || 'Wishlist action failed.', 'error');
        });
}

window.storefrontCurrency = storefrontCurrency;
window.convertStoreMoney = convertStoreMoney;
window.formatStoreMoney = formatStoreMoney;
window.notifyUser = notifyUser;
window.csrfToken = csrfToken;
window.updateCartCount = updateCartCount;
window.addToCart = addToCart;
window.toggleWishlist = toggleWishlist;

document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    initLiveSearch();
    initProductCardNavigation();
});
