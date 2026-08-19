function initCartPage() {
    const cartPage = document.getElementById('cart-page');
    if (!cartPage) return;

    const endpoints = {
        update: cartPage.dataset.updateUrl,
        remove: cartPage.dataset.removeUrl,
        applyCoupon: cartPage.dataset.applyCouponUrl,
        removeCoupon: cartPage.dataset.removeCouponUrl,
    };

    const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';

    function notify(message, type = 'error') {
        if (window.notifyUser) {
            window.notifyUser(message, type);
            return;
        }

        console.error(message);
    }

    function requestHeaders() {
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

    async function parseResponse(response, fallbackMessage) {
        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            throw new Error('Unexpected server response.');
        }

        const payload = await response.json();

        if (!response.ok) {
            const validationMessage = payload?.errors ? Object.values(payload.errors)[0]?.[0] : null;
            throw new Error(validationMessage || payload?.message || fallbackMessage);
        }

        return payload;
    }

    function postJson(url, payload = {}) {
        return fetch(url, {
            method: 'POST',
            headers: requestHeaders(),
            body: JSON.stringify(payload),
        });
    }

    function updateCartItem(itemId, quantity) {
        if (!endpoints.update) {
            notify('Cart update endpoint is missing.', 'error');
            return;
        }

        postJson(endpoints.update, {
            item_id: itemId,
            quantity,
        })
            .then((response) => parseResponse(response, 'Failed to update cart item.'))
            .then((data) => {
                if (data.success) {
                    window.location.reload();
                    return;
                }

                notify(data.message || 'Failed to update cart.', 'error');
            })
            .catch((error) => {
                notify(error.message || 'Failed to update cart item.', 'error');
            });
    }

    function removeCartItem(itemId) {
        if (!endpoints.remove) {
            notify('Cart remove endpoint is missing.', 'error');
            return;
        }

        if (!window.confirm('Remove this item from cart?')) {
            return;
        }

        postJson(endpoints.remove, { item_id: itemId })
            .then((response) => parseResponse(response, 'Failed to remove item.'))
            .then((data) => {
                if (data.success) {
                    window.location.reload();
                    return;
                }

                notify(data.message || 'Failed to remove item.', 'error');
            })
            .catch((error) => {
                notify(error.message || 'Failed to remove item.', 'error');
            });
    }

    function applyCoupon(code) {
        if (!endpoints.applyCoupon) {
            notify('Coupon endpoint is missing.', 'error');
            return;
        }

        if (!code) {
            notify('Please enter a coupon code.', 'error');
            return;
        }

        postJson(endpoints.applyCoupon, { coupon_code: code })
            .then((response) => parseResponse(response, 'Failed to apply coupon.'))
            .then((data) => {
                if (data.success) {
                    window.location.reload();
                    return;
                }

                notify(data.message || 'Failed to apply coupon.', 'error');
            })
            .catch((error) => {
                notify(error.message || 'Failed to apply coupon.', 'error');
            });
    }

    function removeCoupon() {
        if (!endpoints.removeCoupon) {
            notify('Coupon remove endpoint is missing.', 'error');
            return;
        }

        postJson(endpoints.removeCoupon)
            .then((response) => parseResponse(response, 'Failed to remove coupon.'))
            .then((data) => {
                if (data.success) {
                    window.location.reload();
                    return;
                }

                notify(data.message || 'Failed to remove coupon.', 'error');
            })
            .catch((error) => {
                notify(error.message || 'Failed to remove coupon.', 'error');
            });
    }

    cartPage.addEventListener('click', (event) => {
        const quantityButton = event.target.closest('.js-cart-qty');
        if (quantityButton) {
            event.preventDefault();

            const itemId = Number(quantityButton.dataset.itemId);
            const quantity = Number(quantityButton.dataset.quantity);
            if (!Number.isFinite(itemId) || !Number.isFinite(quantity)) return;

            updateCartItem(itemId, quantity);
            return;
        }

        const removeButton = event.target.closest('.js-cart-remove');
        if (removeButton) {
            event.preventDefault();

            const itemId = Number(removeButton.dataset.itemId);
            if (!Number.isFinite(itemId)) return;

            removeCartItem(itemId);
            return;
        }

        const removeCouponButton = event.target.closest('.js-remove-coupon');
        if (removeCouponButton) {
            event.preventDefault();
            removeCoupon();
        }
    });

    const couponForm = cartPage.querySelector('.js-coupon-form');
    couponForm?.addEventListener('submit', (event) => {
        event.preventDefault();

        const codeInput = cartPage.querySelector('#coupon-code');
        applyCoupon(codeInput?.value?.trim() || '');
    });

    // Keep compatibility for existing inline handlers on cached views.
    window.updateCartItem = updateCartItem;
    window.removeCartItem = removeCartItem;
    window.applyCoupon = (event) => {
        if (event?.preventDefault) event.preventDefault();
        const codeInput = cartPage.querySelector('#coupon-code');
        applyCoupon(codeInput?.value?.trim() || '');
    };
    window.removeCoupon = removeCoupon;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCartPage);
} else {
    initCartPage();
}
