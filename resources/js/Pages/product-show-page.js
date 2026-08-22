function initProductShowPage() {
    const configNode = document.getElementById('product-page-config');
    if (!configNode) return;

    let config = null;
    try {
        config = JSON.parse(configNode.textContent || '{}');
    } catch (error) {
        console.error('Invalid product page config payload.', error);
        return;
    }

    const data = config?.data || {};
    const variations = Array.isArray(config?.variations) ? config.variations : [];
    const valueToAttr = config?.valueToAttr || {};
    const selected = { ...(config?.defaultSelection || {}) };
    const endpoints = config?.endpoints || {};
    const aiConfig = config?.aiAssistant || {};

    const mainImage = document.getElementById('main-image');
    const hero = mainImage?.closest('.hero') || null;
    const skuNode = document.getElementById('sku-node');
    const priceNode = document.getElementById('price-node');
    const oldNode = document.getElementById('old-price-node');
    const saveNode = document.getElementById('save-node');
    const stockNode = document.getElementById('stock-node');
    const selectedColorNode = document.getElementById('selected-color-node');
    const qtyNode = document.getElementById('qty-node');
    const addBtn = document.getElementById('add-btn');
    const buyBtn = document.getElementById('buy-btn');
    const shareProductBtn = document.getElementById('share-product-btn');
    const assistActions = document.querySelectorAll('[data-buy-action]');
    const followStoreBtn = document.getElementById('follow-store-btn');
    const followerCountNode = document.getElementById('store-follower-count');
    const dealsEmailInput = document.getElementById('deals-email-input');
    const dealsSignupBtn = document.getElementById('deals-signup-btn');
    const aiQuestionInput = document.getElementById('ai-question-input');
    const aiAskBtn = document.getElementById('ai-ask-btn');
    const aiAnswerBox = document.getElementById('ai-answer-box');
    const aiAnswerText = document.getElementById('ai-answer-text');

    if (!priceNode || !stockNode) {
        return;
    }

    let active = null;
    const supportsHoverZoom = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    let zoomFrame = null;
    let pendingZoomPoint = null;

    const clearZoom = () => {
        if (!hero) return;

        hero.classList.remove('is-zooming');
        hero.style.setProperty('--zoom-x', '50%');
        hero.style.setProperty('--zoom-y', '50%');
        pendingZoomPoint = null;

        if (zoomFrame) {
            window.cancelAnimationFrame(zoomFrame);
            zoomFrame = null;
        }
    };

    const isFallbackImage = () => {
        const src = (mainImage?.currentSrc || mainImage?.src || '').toLowerCase();
        return src.includes('/images/no-product-image.svg');
    };

    const applyZoomPoint = () => {
        zoomFrame = null;

        if (!hero || !pendingZoomPoint) {
            return;
        }

        const rect = hero.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) {
            return;
        }

        const x = ((pendingZoomPoint.x - rect.left) / rect.width) * 100;
        const y = ((pendingZoomPoint.y - rect.top) / rect.height) * 100;
        const clampedX = Math.max(0, Math.min(100, x));
        const clampedY = Math.max(0, Math.min(100, y));

        hero.style.setProperty('--zoom-x', `${clampedX.toFixed(2)}%`);
        hero.style.setProperty('--zoom-y', `${clampedY.toFixed(2)}%`);
    };

    const queueZoomPoint = (event) => {
        if (!hero || !supportsHoverZoom || isFallbackImage()) {
            return;
        }

        pendingZoomPoint = { x: event.clientX, y: event.clientY };
        if (!zoomFrame) {
            zoomFrame = window.requestAnimationFrame(applyZoomPoint);
        }
    };

    if (hero && supportsHoverZoom) {
        hero.classList.add('is-zoomable');

        hero.addEventListener('mouseenter', (event) => {
            if (isFallbackImage()) {
                clearZoom();
                return;
            }

            hero.classList.add('is-zooming');
            queueZoomPoint(event);
        });

        hero.addEventListener('mousemove', queueZoomPoint);
        hero.addEventListener('mouseleave', clearZoom);
    }

    mainImage?.addEventListener('load', clearZoom);
    mainImage?.addEventListener('error', clearZoom);

    const money = (value) => {
        if (window.formatStoreMoney) {
            return window.formatStoreMoney(value);
        }

        const amount = Number(value || 0);
        return Number.isFinite(amount) ? amount.toFixed(2) : '0.00';
    };
    const cleanText = (value) => String(value || '').replace(/\s+/g, ' ').trim();
    const contains = (question, terms) => terms.some((term) => question.includes(term));
    const extractMetric = (source, pattern) => {
        const match = cleanText(source).match(pattern);
        return match ? cleanText(match[1] || match[0]) : '';
    };
    const renderAiAnswer = (question) => {
        if (!aiAnswerBox || !aiAnswerText) return;

        const endpoint = endpoints.aiAssistantQuery;
        if (!endpoint) {
            aiAnswerText.textContent = 'AI service is currently unavailable.';
            aiAnswerBox.classList.remove('hidden');
            return;
        }

        // Show loading state
        aiAnswerBox.classList.remove('hidden');
        aiAnswerText.innerHTML = '<span class="ai-typing">Analyzing product details...</span>';
        aiAnswerBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                id: data.id,
                question: question,
            }),
        })
            .then(async (response) => {
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload?.message || 'AI Assistant is busy. Please try again.');
                }
                return payload;
            })
            .then((payload) => {
                aiAnswerText.textContent = payload.answer || 'I couldn\'t find a specific answer. Please check the product details.';
            })
            .catch((error) => {
                aiAnswerText.textContent = error.message;
            });
    };

    const normalizeQuantity = (value) => {
        const parsed = parseInt(value, 10);
        return Number.isFinite(parsed) && parsed > 0 ? parsed : 1;
    };

    const stockMeta = (quantity) => {
        if (!data.track) {
            return ['In stock', 'stock', true];
        }

        if (data.backorder) {
            return ['Backorder available', 'stock', true];
        }

        if (quantity > 5) {
            return [`In stock (${quantity})`, 'stock', true];
        }

        if (quantity > 0) {
            return [`Limited stock (${quantity})`, 'stock low', true];
        }

        return ['Out of stock', 'stock out', false];
    };

    const findVariation = () => {
        if (!variations.length) return null;

        const entries = Object.entries(selected)
            .map(([attributeId, valueId]) => [Number(attributeId), Number(valueId)])
            .filter(([, valueId]) => valueId > 0);

        if (!entries.length) {
            return variations[0] || null;
        }

        return (
            variations.find((variation) =>
                entries.every(([, valueId]) => variation.attribute_value_ids.includes(valueId)),
            ) || null
        );
    };

    const syncFromVariation = (variation) => {
        if (!variation) return;

        variation.attribute_value_ids.forEach((valueId) => {
            if (valueToAttr[valueId]) {
                selected[valueToAttr[valueId]] = valueId;
            }
        });
    };

    const refreshOptions = () => {
        document.querySelectorAll('[data-option]').forEach((button) => {
            const attrId = Number(button.dataset.attrId);
            const valueId = Number(button.dataset.valueId);

            const available = variations.some(
                (variation) =>
                    variation.attribute_value_ids.includes(valueId) &&
                    Object.entries(selected).every(([selectedAttrId, selectedValueId]) => {
                        const selectedAttr = Number(selectedAttrId);
                        const selectedValue = Number(selectedValueId);

                        return (
                            selectedAttr === attrId ||
                            !selectedValue ||
                            variation.attribute_value_ids.includes(selectedValue)
                        );
                    }),
            );

            button.disabled = !available;
            button.classList.toggle('off', !available);
            button.classList.toggle('sel', Number(selected[attrId]) === valueId);
        });

        if (selectedColorNode) {
            const selectedColorLabel = document
                .querySelector('.color-options .color-swatch.sel .swatch-label')
                ?.textContent?.trim();
            selectedColorNode.textContent = selectedColorLabel || 'Not selected';
        }
    };

    const refresh = (updateImage = true) => {
        active = findVariation();
        if (active) {
            syncFromVariation(active);
        }

        refreshOptions();

        const price = active ? Number(active.price) : Number(data.price);
        const compare = active
            ? active.compare_price !== null
                ? Number(active.compare_price)
                : Number(data.compare || 0)
            : Number(data.compare || 0);

        priceNode.textContent = money(price);

        if (oldNode && saveNode) {
            if (compare > price) {
                oldNode.textContent = money(compare);
                oldNode.classList.remove('hidden');
                saveNode.textContent = `Save ${money(compare - price)}`;
                saveNode.classList.remove('hidden');
            } else {
                oldNode.classList.add('hidden');
                saveNode.classList.add('hidden');
            }
        }

        if (skuNode) {
            skuNode.textContent = active && active.sku ? active.sku : data.sku || 'N/A';
        }

        const quantity = active ? Number(active.quantity || 0) : Number(data.qty || 0);
        const stock = stockMeta(quantity);
        stockNode.className = stock[1];
        stockNode.textContent = stock[0];

        if (addBtn) addBtn.disabled = !stock[2];
        if (buyBtn) buyBtn.disabled = !stock[2];

        if (updateImage && active && active.image_url && mainImage) {
            clearZoom();
            mainImage.src = active.image_url;
        }
    };

    document.querySelectorAll('[data-option]').forEach((button) => {
        button.addEventListener('click', () => {
            if (button.disabled) return;

            selected[button.dataset.attrId] = Number(button.dataset.valueId);

            let match = findVariation();
            if (!match) {
                match =
                    variations.find((variation) =>
                        variation.attribute_value_ids.includes(Number(button.dataset.valueId)),
                    ) || null;

                if (match) {
                    syncFromVariation(match);
                }
            }

            active = match;
            refresh(true);
        });
    });

    document.querySelectorAll('[data-thumb]').forEach((button) => {
        button.addEventListener('click', () => {
            const src = button.dataset.image;
            if (src && mainImage) {
                clearZoom();
                mainImage.src = src;
            }

            document.querySelectorAll('[data-thumb]').forEach((thumb) => {
                thumb.classList.remove('is-active');
            });
            button.classList.add('is-active');
        });
    });

    qtyNode?.addEventListener('change', () => {
        qtyNode.value = String(normalizeQuantity(qtyNode.value));
    });

    document.querySelector('[data-dec]')?.addEventListener('click', () => {
        if (!qtyNode) return;
        qtyNode.value = String(Math.max(1, normalizeQuantity(qtyNode.value) - 1));
    });

    document.querySelector('[data-inc]')?.addEventListener('click', () => {
        if (!qtyNode) return;
        qtyNode.value = String(normalizeQuantity(qtyNode.value) + 1);
    });

    addBtn?.addEventListener('click', () => {
        if (!window.addToCart) return;

        window.addToCart(
            Number(data.id || 0),
            normalizeQuantity(qtyNode?.value || 1),
            active ? Number(active.id) : null,
        );
    });

    buyBtn?.addEventListener('click', () => {
        if (!endpoints.addToCart || !endpoints.cartIndex) return;

        fetch(endpoints.addToCart, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                product_id: Number(data.id || 0),
                quantity: normalizeQuantity(qtyNode?.value || 1),
                variation_id: active ? Number(active.id) : null,
            }),
        })
            .then(async (response) => {
                if (response.ok) {
                    return response.json();
                }

                const payload = await response.json().catch(() => ({}));
                throw new Error(payload?.message || 'Failed to add product');
            })
            .then(() => {
                if (window.updateCartCount) {
                    window.updateCartCount();
                }
                window.location.href = endpoints.cartIndex;
            })
            .catch((error) => {
                if (window.notifyUser) {
                    window.notifyUser(error.message, 'error');
                }
            });
    });

    shareProductBtn?.addEventListener('click', async () => {
        const shareUrl = shareProductBtn.dataset.shareUrl || window.location.href;
        const shareTitle = shareProductBtn.dataset.shareTitle || document.title;

        try {
            if (navigator.share) {
                await navigator.share({
                    title: shareTitle,
                    url: shareUrl,
                });
                return;
            }

            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(shareUrl);
                if (window.notifyUser) {
                    window.notifyUser('Product link copied.', 'success');
                }
                return;
            }

            window.prompt('Copy this product link:', shareUrl);
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }

            if (window.notifyUser) {
                window.notifyUser('Unable to share product link right now.', 'error');
            }
        }
    });

    assistActions.forEach((button) => {
        button.addEventListener('click', () => {
            const action = String(button.dataset.buyAction || '');
            const messages = {
                compare: 'Added to compare list.',
                alert: 'Price alert saved for this product.',
                report: 'Thanks. Our team will review this listing.',
            };

            const message = messages[action] || 'Action completed.';
            if (window.notifyUser) {
                window.notifyUser(message, 'success');
            }
        });
    });

    followStoreBtn?.addEventListener('click', () => {
        const endpoint = endpoints.followStore;
        if (!endpoint) {
            return;
        }

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({}),
        })
            .then(async (response) => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }

                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload?.message || 'Unable to update follow status.');
                }

                return payload;
            })
            .then((payload) => {
                if (!payload) {
                    return;
                }

                const following = Boolean(payload?.following);
                followStoreBtn.classList.toggle('is-following', following);
                followStoreBtn.setAttribute('aria-pressed', following ? 'true' : 'false');
                followStoreBtn.textContent = following ? 'Following' : 'Follow';

                if (followerCountNode && Number.isFinite(Number(payload?.followers))) {
                    followerCountNode.textContent = Number(payload.followers).toLocaleString();
                }

                if (window.notifyUser && payload?.message) {
                    window.notifyUser(payload.message, 'success');
                }
            })
            .catch((error) => {
                if (window.notifyUser) {
                    window.notifyUser(error.message || 'Unable to update follow status.', 'error');
                }
            });
    });

    document.querySelectorAll('[data-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            const key = button.dataset.tab;

            document.querySelectorAll('.tab-btn').forEach((tabButton) => {
                tabButton.classList.remove('is-active');
            });
            document.querySelectorAll('[data-pane]').forEach((pane) => {
                pane.classList.remove('is-active');
            });

            button.classList.add('is-active');
            document.querySelector(`[data-pane="${key}"]`)?.classList.add('is-active');
        });
    });

    document.querySelector('[data-open-reviews]')?.addEventListener('click', () => {
        document.getElementById('reviews-tab-btn')?.click();
    });

    document.querySelector('[data-open-qa]')?.addEventListener('click', () => {
        document.getElementById('qa-tab-btn')?.click();
    });

    dealsSignupBtn?.addEventListener('click', () => {
        const email = String(dealsEmailInput?.value || '').trim();
        if (!email) {
            window.notifyUser?.('Enter your email address first.', 'warning');
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            window.notifyUser?.('Please enter a valid email address.', 'error');
            return;
        }

        const subscribeEndpoint = endpoints.dealsSubscribe;
        if (!subscribeEndpoint) {
            window.notifyUser?.('Subscription service is unavailable right now.', 'error');
            return;
        }

        const originalLabel = dealsSignupBtn.textContent;
        dealsSignupBtn.disabled = true;
        dealsSignupBtn.textContent = 'Submitting...';

        fetch(subscribeEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                email,
                source: 'product_show',
            }),
        })
            .then(async (response) => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }

                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const firstError = payload?.errors
                        ? Object.values(payload.errors)[0]?.[0]
                        : null;
                    throw new Error(firstError || payload?.message || 'Subscription failed.');
                }

                return payload;
            })
            .then((payload) => {
                if (!payload) return;

                window.notifyUser?.(
                    payload.message || 'Thanks. You are subscribed to product deal alerts.',
                    'success',
                );

                if (dealsEmailInput) {
                    dealsEmailInput.value = '';
                }
            })
            .catch((error) => {
                window.notifyUser?.(error.message || 'Subscription failed.', 'error');
            })
            .finally(() => {
                dealsSignupBtn.disabled = false;
                dealsSignupBtn.textContent = originalLabel || 'Sign Up';
            });
    });

    document.querySelectorAll('[data-ai-question]').forEach((button) => {
        button.addEventListener('click', () => {
            const question = cleanText(button.dataset.aiQuestion || button.textContent);
            if (!question) {
                return;
            }

            if (aiQuestionInput) {
                aiQuestionInput.value = question;
            }
            renderAiAnswer(question);
        });
    });

    aiAskBtn?.addEventListener('click', () => {
        const question = cleanText(aiQuestionInput?.value || '');
        if (!question) {
            window.notifyUser?.('Type your question first.', 'warning');
            return;
        }

        renderAiAnswer(question);
    });

    aiQuestionInput?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        event.preventDefault();
        aiAskBtn?.click();
    });

    refresh(false);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductShowPage);
} else {
    initProductShowPage();
}
