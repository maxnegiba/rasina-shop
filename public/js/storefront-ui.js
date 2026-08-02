(() => {
    'use strict';

    if (window.__mtdStorefrontUiBound) {
        return;
    }

    window.__mtdStorefrontUiBound = true;

    const SELECTORS = {
        mobileOverlay: '#mobile-sidebar',
        mobilePanel: '#mobile-sidebar-content',
        cartOverlay: '#cart-sidebar',
        cartPanel: '#cart-sidebar-content',
        cartInner: '#cart-sidebar-inner',
    };

    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    function setPageLocked(locked) {
        document.documentElement.classList.toggle('overflow-hidden', locked);
        document.body.style.overflow = locked ? 'hidden' : '';
    }

    function openDrawer(overlaySelector, panelSelector) {
        const overlay = document.querySelector(overlaySelector);
        const panel = document.querySelector(panelSelector);

        if (!overlay || !panel) {
            return;
        }

        overlay.classList.remove('hidden');
        overlay.setAttribute('aria-hidden', 'false');
        setPageLocked(true);

        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                panel.classList.remove('translate-x-full');
            });
        });
    }

    function closeDrawer(overlaySelector, panelSelector) {
        const overlay = document.querySelector(overlaySelector);
        const panel = document.querySelector(panelSelector);

        if (!overlay || !panel) {
            setPageLocked(false);
            return;
        }

        overlay.classList.add('opacity-0');
        panel.classList.add('translate-x-full');
        overlay.setAttribute('aria-hidden', 'true');

        window.setTimeout(() => {
            if (overlay.classList.contains('opacity-0')) {
                overlay.classList.add('hidden');
            }

            const anotherDrawerIsOpen = [SELECTORS.mobileOverlay, SELECTORS.cartOverlay]
                .map((selector) => document.querySelector(selector))
                .some((element) => element && !element.classList.contains('hidden'));

            setPageLocked(anotherDrawerIsOpen);
        }, 300);
    }

    function openCart() {
        closeDrawer(SELECTORS.mobileOverlay, SELECTORS.mobilePanel);
        openDrawer(SELECTORS.cartOverlay, SELECTORS.cartPanel);
    }

    function closeCart() {
        closeDrawer(SELECTORS.cartOverlay, SELECTORS.cartPanel);
    }

    function openMobileMenu() {
        closeDrawer(SELECTORS.cartOverlay, SELECTORS.cartPanel);
        openDrawer(SELECTORS.mobileOverlay, SELECTORS.mobilePanel);
    }

    function closeMobileMenu() {
        closeDrawer(SELECTORS.mobileOverlay, SELECTORS.mobilePanel);
    }

    function showError(message) {
        const existing = document.getElementById('storefront-error-toast');
        existing?.remove();

        const toast = document.createElement('div');
        toast.id = 'storefront-error-toast';
        toast.setAttribute('role', 'alert');
        toast.className = 'fixed bottom-6 left-1/2 z-[100] max-w-[calc(100%-2rem)] -translate-x-1/2 bg-dark-brown px-5 py-4 text-center text-xs text-white shadow-2xl';
        toast.textContent = message || 'A apărut o eroare. Vă rugăm să încercați din nou.';
        document.body.appendChild(toast);

        window.setTimeout(() => toast.remove(), 5000);
    }

    async function parseResponse(response) {
        const contentType = response.headers.get('content-type') ?? '';
        const payload = contentType.includes('application/json')
            ? await response.json()
            : { message: await response.text() };

        if (!response.ok) {
            const validationMessage = payload?.errors
                ? Object.values(payload.errors).flat().join(' ')
                : null;

            throw new Error(validationMessage || payload?.message || 'Cererea nu a putut fi procesată.');
        }

        return payload;
    }

    function updateCartUI(cartCount, htmlContent) {
        const badge = document.getElementById('cart-count-badge');
        const floatingButton = document.getElementById('floating-cart-btn');
        const floatingCount = document.getElementById('floating-cart-count');
        const cartInner = document.querySelector(SELECTORS.cartInner);
        const normalizedCount = Number(cartCount) || 0;

        if (badge) {
            badge.textContent = String(normalizedCount);
            badge.classList.toggle('hidden', normalizedCount === 0);
        }

        if (floatingButton) {
            floatingButton.classList.toggle('hidden', normalizedCount === 0);
        }

        if (floatingCount) {
            floatingCount.textContent = String(normalizedCount);
        }

        if (cartInner && typeof htmlContent === 'string') {
            cartInner.innerHTML = htmlContent;
        }
    }

    async function addToCart(form, submitter) {
        if (form.dataset.cartSubmitting === 'true') {
            return;
        }

        form.dataset.cartSubmitting = 'true';
        submitter?.setAttribute('aria-busy', 'true');
        submitter?.setAttribute('disabled', 'disabled');

        try {
            const response = await fetch(form.action, {
                method: (form.method || 'POST').toUpperCase(),
                body: new FormData(form),
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });

            const data = await parseResponse(response);

            if (!data.success) {
                throw new Error(data.message || 'Produsul nu a putut fi adăugat în colecție.');
            }

            updateCartUI(data.cart_count, data.html);
            openCart();
        } catch (error) {
            console.error('MTD cart add failed:', error);
            showError(error instanceof Error ? error.message : String(error));
        } finally {
            delete form.dataset.cartSubmitting;
            submitter?.removeAttribute('aria-busy');
            submitter?.removeAttribute('disabled');
        }
    }

    async function removeFromCart(button) {
        if (button.dataset.cartRemoving === 'true') {
            return;
        }

        const productId = button.dataset.id;
        const cartRoot = document.querySelector('[data-cart-remove-url]');
        const removeUrl = cartRoot?.dataset.cartRemoveUrl;

        if (!productId || !removeUrl) {
            showError('Produsul nu a putut fi identificat în colecție.');
            return;
        }

        button.dataset.cartRemoving = 'true';
        button.setAttribute('aria-busy', 'true');
        button.setAttribute('disabled', 'disabled');

        const formData = new FormData();
        formData.append('id', productId);
        formData.append('_token', csrfToken());

        try {
            const response = await fetch(removeUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });

            const data = await parseResponse(response);

            if (!data.success) {
                throw new Error(data.message || 'Produsul nu a putut fi eliminat.');
            }

            updateCartUI(data.cart_count, data.html);
        } catch (error) {
            console.error('MTD cart remove failed:', error);
            showError(error instanceof Error ? error.message : String(error));
            button.removeAttribute('disabled');
            button.removeAttribute('aria-busy');
            delete button.dataset.cartRemoving;
        }
    }

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;

        if (!target) {
            return;
        }

        if (target.closest('#cart-menu-btn, #floating-cart-btn')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            openCart();
            return;
        }

        if (target.closest('#cart-sidebar-close') || target.matches(SELECTORS.cartOverlay)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            closeCart();
            return;
        }

        if (target.closest('#mobile-menu-btn')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            openMobileMenu();
            return;
        }

        if (target.closest('#mobile-close-btn, .mobile-link') || target.matches(SELECTORS.mobileOverlay)) {
            if (target.closest('#mobile-close-btn') || target.matches(SELECTORS.mobileOverlay)) {
                event.preventDefault();
            }
            event.stopImmediatePropagation();
            closeMobileMenu();
            return;
        }

        const removeButton = target.closest('.remove-from-cart-btn');

        if (removeButton instanceof HTMLButtonElement) {
            event.preventDefault();
            event.stopImmediatePropagation();
            void removeFromCart(removeButton);
        }
    }, true);

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.matches('.add-to-cart-ajax-form')) {
            return;
        }

        const submitter = event.submitter instanceof HTMLButtonElement ? event.submitter : null;
        const redirectToCheckout = submitter?.name === 'redirect_to_checkout' && submitter.value === '1';

        if (redirectToCheckout) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        void addToCart(form, submitter);
    }, true);

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        closeCart();
        closeMobileMenu();
    });

    document.addEventListener('livewire:navigating', () => {
        closeCart();
        closeMobileMenu();
        setPageLocked(false);
    });

    document.addEventListener('livewire:navigated', () => {
        setPageLocked(false);
    });
})();
