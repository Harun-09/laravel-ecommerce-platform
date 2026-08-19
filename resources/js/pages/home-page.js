function initHomeHeroSlider() {
    const slider = document.querySelector('[data-hero-slider]');
    if (!slider) return;

    const slides = Array.from(slider.querySelectorAll('[data-hero-slide]'));
    if (slides.length <= 1) return;

    const dots = Array.from(document.querySelectorAll('[data-hero-dot]'));
    const prevBtn = document.querySelector('[data-hero-prev]');
    const nextBtn = document.querySelector('[data-hero-next]');

    let activeIndex = 0;
    let autoSlideTimer = null;
    const autoSlideIntervalMs = 6000;
    let dragStartX = null;
    let dragStartY = null;
    let movedDistanceX = 0;
    let movedDistanceY = 0;
    let suppressClickOnce = false;
    const swipeThreshold = 50;
    let pointerId = null;
    let slideChangedByDrag = false;
    let dragDirectionLocked = null;

    const setActiveSlide = (index) => {
        activeIndex = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === activeIndex);
        });

        dots.forEach((dot, dotIndex) => {
            dot.classList.toggle('is-active', dotIndex === activeIndex);
        });
    };

    const stopAutoSlide = () => {
        if (!autoSlideTimer) return;
        window.clearInterval(autoSlideTimer);
        autoSlideTimer = null;
    };

    const startAutoSlide = () => {
        stopAutoSlide();
        autoSlideTimer = window.setInterval(() => {
            setActiveSlide(activeIndex + 1);
        }, autoSlideIntervalMs);
    };

    const goPrev = () => {
        setActiveSlide(activeIndex - 1);
        startAutoSlide();
    };

    const goNext = () => {
        setActiveSlide(activeIndex + 1);
        startAutoSlide();
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const targetIndex = Number(dot.getAttribute('data-hero-dot'));
            if (Number.isNaN(targetIndex)) return;

            setActiveSlide(targetIndex);
            startAutoSlide();
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', goPrev);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', goNext);
    }

    const heroSection = document.getElementById('hero-banner-pick');
    if (heroSection) {
        heroSection.addEventListener('mouseenter', stopAutoSlide);
        heroSection.addEventListener('mouseleave', startAutoSlide);
        heroSection.addEventListener('touchstart', stopAutoSlide, { passive: true });
        heroSection.addEventListener('touchend', startAutoSlide, { passive: true });
    }

    const onPointerDown = (event) => {
        if (event.pointerType === 'mouse' && event.button !== 0) return;

        dragStartX = event.clientX;
        dragStartY = event.clientY;
        movedDistanceX = 0;
        movedDistanceY = 0;
        pointerId = event.pointerId;
        slideChangedByDrag = false;
        dragDirectionLocked = null;
        slider.classList.add('is-dragging');
        slider.classList.remove('is-swiping');
        if (typeof slider.setPointerCapture === 'function') {
            slider.setPointerCapture(event.pointerId);
        }
        stopAutoSlide();
    };

    const onPointerMove = (event) => {
        if (dragStartX === null) return;

        if (pointerId !== null && event.pointerId !== pointerId) {
            return;
        }

        movedDistanceX = event.clientX - dragStartX;
        movedDistanceY = event.clientY - dragStartY;

        const absX = Math.abs(movedDistanceX);
        const absY = Math.abs(movedDistanceY);

        if (!dragDirectionLocked && (absX > 6 || absY > 6)) {
            dragDirectionLocked = absX > absY ? 'horizontal' : 'vertical';
        }

        if (dragDirectionLocked !== 'horizontal') {
            return;
        }

        event.preventDefault();
        slider.classList.add('is-swiping');

        if (slideChangedByDrag || absX < swipeThreshold) {
            return;
        }

        slideChangedByDrag = true;
        suppressClickOnce = true;

        if (movedDistanceX < 0) {
            goNext();
        } else {
            goPrev();
        }
    };

    const resetPointerState = () => {
        dragStartX = null;
        dragStartY = null;
        movedDistanceX = 0;
        movedDistanceY = 0;
        pointerId = null;
        slideChangedByDrag = false;
        dragDirectionLocked = null;
        slider.classList.remove('is-dragging');
        slider.classList.remove('is-swiping');
    };

    const onPointerUp = (event) => {
        if (dragStartX === null) return;

        if (pointerId !== null && event && event.pointerId !== pointerId) {
            return;
        }

        if (!slideChangedByDrag) {
            const absX = Math.abs(movedDistanceX);
            const absY = Math.abs(movedDistanceY);
            if (absX > absY && absX > swipeThreshold) {
                suppressClickOnce = true;
                if (movedDistanceX < 0) {
                    goNext();
                } else {
                    goPrev();
                }
            } else {
                startAutoSlide();
            }
        } else {
            startAutoSlide();
        }

        resetPointerState();
    };

    slider.addEventListener('pointerdown', onPointerDown);
    slider.addEventListener('pointermove', onPointerMove);
    slider.addEventListener('pointerup', onPointerUp);
    slider.addEventListener('dragstart', (event) => {
        event.preventDefault();
    });
    slider.addEventListener('pointercancel', () => {
        resetPointerState();
        startAutoSlide();
    });
    slider.addEventListener('pointerleave', () => {
        if (dragStartX === null) return;
        onPointerUp();
    });
    slider.addEventListener(
        'click',
        (event) => {
            if (!suppressClickOnce) return;
            event.preventDefault();
            event.stopPropagation();
            suppressClickOnce = false;
        },
        true,
    );

    startAutoSlide();
}

function initHomeNewsletter() {
    const newsletterForm = document.getElementById('home-newsletter-form');
    const newsletterEmailWrap = document.getElementById('home-newsletter-email-wrap');
    const newsletterEmailInput = document.getElementById('home-newsletter-email');
    const newsletterSubmitBtn = document.getElementById('home-newsletter-submit');
    const isSubscribed = () => newsletterSubmitBtn?.dataset.subscribed === '1';
    const setSubscribedState = (subscribed) => {
        if (newsletterSubmitBtn) {
            newsletterSubmitBtn.dataset.subscribed = subscribed ? '1' : '0';
            newsletterSubmitBtn.textContent = subscribed ? 'Subscribed' : 'Subscribe';
            newsletterSubmitBtn.disabled = subscribed;
        }

        if (newsletterEmailWrap) {
            newsletterEmailWrap.style.display = subscribed ? 'none' : '';
        }

        if (newsletterEmailInput) {
            newsletterEmailInput.required = !subscribed;
            newsletterEmailInput.disabled = subscribed;
            if (subscribed) {
                newsletterEmailInput.value = '';
            }
        }
    };

    setSubscribedState(isSubscribed());

    newsletterForm?.addEventListener('submit', (event) => {
        event.preventDefault();

        if (isSubscribed()) {
            return;
        }

        const email = String(newsletterEmailInput?.value || '').trim();
        if (!email) {
            window.notifyUser?.('Enter your email address first.', 'warning');
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            window.notifyUser?.('Please enter a valid email address.', 'error');
            return;
        }

        const action = newsletterForm.getAttribute('action') || '';
        if (!action) {
            newsletterForm.submit();
            return;
        }

        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content ||
            String(newsletterForm.querySelector('input[name="_token"]')?.value || '');
        const source = String(
            newsletterForm.querySelector('input[name="source"]')?.value || 'home_newsletter',
        );

        if (newsletterSubmitBtn) {
            newsletterSubmitBtn.disabled = true;
            newsletterSubmitBtn.textContent = 'Submitting...';
        }

        fetch(action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                email,
                source,
            }),
        })
            .then(async (response) => {
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
                window.notifyUser?.(
                    payload?.message || 'Thanks. You are subscribed to product deal alerts.',
                    'success',
                );

                if (newsletterEmailInput) {
                    newsletterEmailInput.value = '';
                }

                if (newsletterSubmitBtn) {
                    newsletterSubmitBtn.dataset.subscribed = '1';
                }
            })
            .catch((error) => {
                window.notifyUser?.(error.message || 'Subscription failed.', 'error');
            })
            .finally(() => {
                setSubscribedState(isSubscribed());
            });
    });
}

function initHomePage() {
    initHomeHeroSlider();
    initHomeNewsletter();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHomePage);
} else {
    initHomePage();
}
