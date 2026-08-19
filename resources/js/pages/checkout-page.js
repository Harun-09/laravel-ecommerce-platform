function initCheckoutPage() {
    const checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;

    const shippingMethodsContainer = document.getElementById('shipping-methods');
    const shippingCityField = document.getElementById('shipping_city');
    const deliveryZoneBadge = document.getElementById('delivery-zone-badge');
    const shippingCostNode = document.getElementById('shipping-cost');
    const shippingDiscountRowNode = document.getElementById('shipping-discount-row');
    const shippingDiscountNode = document.getElementById('shipping-discount');
    const codFeeRowNode = document.getElementById('cod-fee-row');
    const codFeeNode = document.getElementById('cod-fee');
    const orderTotalNode = document.getElementById('order-total');

    if (
        !shippingMethodsContainer ||
        !shippingCityField ||
        !deliveryZoneBadge ||
        !shippingCostNode ||
        !shippingDiscountRowNode ||
        !shippingDiscountNode ||
        !codFeeRowNode ||
        !codFeeNode ||
        !orderTotalNode
    ) {
        return;
    }

    const baseCartTotal = Number(checkoutForm.dataset.baseCartTotal || 0);
    const shippingEndpoint = checkoutForm.dataset.shippingEndpoint || '';
    const defaultSelectedMethodId = Number(checkoutForm.dataset.selectedShippingMethod || 0);

    function formatCheckoutMoney(amount) {
        if (window.formatStoreMoney) {
            return window.formatStoreMoney(amount);
        }

        return Number(amount || 0).toFixed(2);
    }

    function parseMoney(value) {
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function selectedPaymentMethod() {
        const selected = document.querySelector('[name="payment_method"]:checked');
        return selected ? selected.value : 'cod';
    }

    function fillAddress(address) {
        const shippingNameField = checkoutForm.querySelector('[name="shipping_name"]');
        const shippingPhoneField = checkoutForm.querySelector('[name="shipping_phone"]');
        const shippingAddressField = checkoutForm.querySelector('[name="shipping_address"]');
        const shippingPostalCodeField = checkoutForm.querySelector('[name="shipping_postal_code"]');

        if (shippingNameField) shippingNameField.value = address.name || '';
        if (shippingPhoneField) shippingPhoneField.value = address.phone || '';
        if (shippingAddressField) shippingAddressField.value = address.address_line_1 || '';
        if (shippingPostalCodeField) shippingPostalCodeField.value = address.postal_code || '';

        shippingCityField.value = address.city || '';
        loadShippingMethods();
    }

    function fillAddressFromRadio(radio) {
        if (!radio) return;

        fillAddress({
            name: radio.dataset.name || '',
            phone: radio.dataset.phone || '',
            address_line_1: radio.dataset.addressLine || '',
            city: radio.dataset.city || '',
            postal_code: radio.dataset.postalCode || '',
        });
    }

    function bindSavedAddressEvents() {
        checkoutForm.querySelectorAll('.js-saved-address').forEach((radio) => {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    fillAddressFromRadio(radio);
                }
            });
        });
    }

    function bindShippingMethodEvents() {
        checkoutForm.querySelectorAll('[name="shipping_method"]').forEach((radio) => {
            radio.addEventListener('change', updateSummaryFromSelectedMethod);
        });
    }

    function updateSummaryFromSelectedMethod() {
        const selected = checkoutForm.querySelector('[name="shipping_method"]:checked');

        if (!selected) {
            shippingCostNode.textContent = formatCheckoutMoney(0);
            shippingDiscountRowNode.style.display = 'none';
            shippingDiscountNode.textContent = `-${formatCheckoutMoney(0)}`;
            codFeeRowNode.style.display = 'none';
            codFeeNode.textContent = formatCheckoutMoney(0);
            orderTotalNode.textContent = formatCheckoutMoney(baseCartTotal);
            return;
        }

        const shippingCost = parseMoney(selected.dataset.shippingCost);
        const shippingDiscount = parseMoney(selected.dataset.shippingDiscount);
        const codFee = parseMoney(selected.dataset.codFee);

        shippingCostNode.textContent = formatCheckoutMoney(shippingCost);
        shippingDiscountNode.textContent = `-${formatCheckoutMoney(shippingDiscount)}`;
        shippingDiscountRowNode.style.display = shippingDiscount > 0 ? 'flex' : 'none';
        codFeeNode.textContent = formatCheckoutMoney(codFee);
        codFeeRowNode.style.display = codFee > 0 ? 'flex' : 'none';
        orderTotalNode.textContent = formatCheckoutMoney(baseCartTotal + shippingCost + codFee);
    }

    function renderMethods(methods, preferredMethodId = null) {
        if (!methods || methods.length === 0) {
            shippingMethodsContainer.innerHTML = '<div style="padding: 16px; border-radius: 8px; background: #fff7ed; color: #9a3412;">No shipping method is available for this delivery zone.</div>';
            updateSummaryFromSelectedMethod();
            return;
        }

        const preferredId = Number(preferredMethodId || 0);
        const selectedMethodId = preferredId > 0 ? preferredId : defaultSelectedMethodId;

        shippingMethodsContainer.innerHTML = methods
            .map((method, index) => {
                const checked = selectedMethodId > 0 ? Number(method.id) === selectedMethodId : index === 0;

                const codNote = !method.is_cod_available
                    ? '<p style="font-size: 12px; color: #dc2626; margin-top: 4px;">COD not available on this method</p>'
                    : '';

                const couponNote = method.is_free_shipping_applied
                    ? '<p style="font-size: 12px; color: #166534; margin-top: 4px;">Free shipping coupon applied</p>'
                    : '';

                const codFeeNote = method.cod_fee > 0
                    ? `<p style="font-size: 12px; color: #6b7280;">Includes COD fee ${formatCheckoutMoney(method.cod_fee)}</p>`
                    : '';

                const displayCost = method.total_cost > 0 ? formatCheckoutMoney(method.total_cost) : 'FREE';

                return `
                    <label style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 2px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <input
                                type="radio"
                                name="shipping_method"
                                value="${method.id}"
                                data-shipping-cost="${method.shipping_cost}"
                                data-shipping-discount="${method.shipping_discount || 0}"
                                data-cod-fee="${method.cod_fee}"
                                data-total-cost="${method.total_cost}"
                                data-cod-available="${method.is_cod_available ? '1' : '0'}"
                                ${checked ? 'checked' : ''}
                            >
                            <div>
                                <p style="font-weight: 600;">${method.name}</p>
                                <p style="font-size: 13px; color: #6b7280;">${method.description || `Est. delivery: ${method.estimated_days || 'N/A'}`}</p>
                                ${codNote}
                                ${couponNote}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-weight: 600; color: #6366f1;">${displayCost}</p>
                            ${codFeeNote}
                        </div>
                    </label>
                `;
            })
            .join('');

        bindShippingMethodEvents();
        updateSummaryFromSelectedMethod();
    }

    async function loadShippingMethods(preferredMethodId = null) {
        const city = shippingCityField.value;

        if (!city || !shippingEndpoint) {
            renderMethods([]);
            deliveryZoneBadge.textContent = 'Not Selected';
            return;
        }

        try {
            const paymentMethod = selectedPaymentMethod();
            const params = new URLSearchParams({
                city,
                payment_method: paymentMethod,
            });

            const response = await fetch(`${shippingEndpoint}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load shipping methods.');
            }

            const payload = await response.json();
            deliveryZoneBadge.textContent = payload.zone?.name || 'Not Selected';
            renderMethods(payload.methods || [], preferredMethodId);
        } catch (error) {
            shippingMethodsContainer.innerHTML = '<div style="padding: 16px; border-radius: 8px; background: #fee2e2; color: #991b1b;">Unable to load shipping methods right now.</div>';
            updateSummaryFromSelectedMethod();
        }
    }

    bindSavedAddressEvents();
    bindShippingMethodEvents();
    updateSummaryFromSelectedMethod();

    const checkedSavedAddress = checkoutForm.querySelector('.js-saved-address:checked');
    if (checkedSavedAddress) {
        fillAddressFromRadio(checkedSavedAddress);
    }

    const currentMethod = checkoutForm.querySelector('[name="shipping_method"]:checked');
    loadShippingMethods(currentMethod ? currentMethod.value : null);

    shippingCityField.addEventListener('change', () => loadShippingMethods());

    checkoutForm.querySelectorAll('[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            const selected = checkoutForm.querySelector('[name="shipping_method"]:checked');
            loadShippingMethods(selected ? selected.value : null);
        });
    });

    // Keep compatibility for any cached inline handlers.
    window.fillAddress = fillAddress;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCheckoutPage);
} else {
    initCheckoutPage();
}
