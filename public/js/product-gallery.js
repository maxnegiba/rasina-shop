(() => {
    'use strict';

    if (window.__mtdProductGalleryBound) {
        return;
    }

    window.__mtdProductGalleryBound = true;

    const GALLERY_SELECTOR = '[data-product-gallery]';
    const SLIDE_SELECTOR = '[data-gallery-slide]';
    const THUMB_SELECTOR = '[data-gallery-thumb]';

    function getSlides(gallery) {
        return Array.from(gallery.querySelectorAll(SLIDE_SELECTOR));
    }

    function getThumbs(gallery) {
        return Array.from(gallery.querySelectorAll(THUMB_SELECTOR));
    }

    function normalizeIndex(index, length) {
        if (length <= 0) {
            return 0;
        }

        return ((index % length) + length) % length;
    }

    function setActiveSlide(gallery, requestedIndex, options = {}) {
        const slides = getSlides(gallery);

        if (slides.length === 0) {
            return;
        }

        const index = normalizeIndex(Number(requestedIndex) || 0, slides.length);
        const thumbs = getThumbs(gallery);
        const position = gallery.querySelector('[data-gallery-position]');

        gallery.dataset.activeIndex = String(index);

        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === index;

            slide.classList.toggle('hidden', !isActive);
            slide.classList.toggle('opacity-0', !isActive);
            slide.classList.toggle('opacity-100', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');

            const image = slide.querySelector('img');
            if (image && isActive && image.dataset.src && !image.getAttribute('src')) {
                image.setAttribute('src', image.dataset.src);
            }
        });

        thumbs.forEach((thumb, thumbIndex) => {
            const isActive = thumbIndex === index;

            thumb.setAttribute('aria-selected', isActive ? 'true' : 'false');
            thumb.setAttribute('tabindex', isActive ? '0' : '-1');
            thumb.classList.toggle('ring-vintage-gold', isActive);
            thumb.classList.toggle('shadow-sm', isActive);
            thumb.classList.toggle('ring-black/5', !isActive);

            const image = thumb.querySelector('img');
            image?.classList.toggle('opacity-100', isActive);
            image?.classList.toggle('opacity-70', !isActive);
        });

        if (position) {
            position.textContent = `${index + 1} / ${slides.length}`;
        }

        if (options.focusThumbnail && thumbs[index] instanceof HTMLElement) {
            thumbs[index].focus({ preventScroll: true });
        }
    }

    function moveGallery(gallery, direction) {
        const slides = getSlides(gallery);
        const currentIndex = Number(gallery.dataset.activeIndex) || 0;
        setActiveSlide(gallery, currentIndex + direction, { focusThumbnail: false });
    }

    function initializeGallery(gallery) {
        if (!(gallery instanceof HTMLElement)) {
            return;
        }

        const initialIndex = Number(gallery.dataset.activeIndex) || 0;
        setActiveSlide(gallery, initialIndex);
    }

    function initializeAll(root = document) {
        root.querySelectorAll(GALLERY_SELECTOR).forEach(initializeGallery);
    }

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;

        if (!target) {
            return;
        }

        const previousButton = target.closest('[data-gallery-prev]');
        if (previousButton) {
            const gallery = previousButton.closest(GALLERY_SELECTOR);
            if (gallery) {
                event.preventDefault();
                moveGallery(gallery, -1);
            }
            return;
        }

        const nextButton = target.closest('[data-gallery-next]');
        if (nextButton) {
            const gallery = nextButton.closest(GALLERY_SELECTOR);
            if (gallery) {
                event.preventDefault();
                moveGallery(gallery, 1);
            }
            return;
        }

        const thumbnail = target.closest(THUMB_SELECTOR);
        if (thumbnail) {
            const gallery = thumbnail.closest(GALLERY_SELECTOR);
            if (gallery) {
                event.preventDefault();
                setActiveSlide(gallery, Number(thumbnail.dataset.galleryIndex) || 0);
            }
        }
    });

    document.addEventListener('keydown', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        const gallery = target?.closest(GALLERY_SELECTOR);

        if (!gallery) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            moveGallery(gallery, -1);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            moveGallery(gallery, 1);
        } else if (event.key === 'Home') {
            event.preventDefault();
            setActiveSlide(gallery, 0, { focusThumbnail: true });
        } else if (event.key === 'End') {
            event.preventDefault();
            setActiveSlide(gallery, getSlides(gallery).length - 1, { focusThumbnail: true });
        }
    });

    document.addEventListener('touchstart', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        const gallery = target?.closest(GALLERY_SELECTOR);
        const touch = event.touches[0];

        if (gallery && touch) {
            gallery.dataset.touchStartX = String(touch.clientX);
            gallery.dataset.touchStartY = String(touch.clientY);
        }
    }, { passive: true });

    document.addEventListener('touchend', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        const gallery = target?.closest(GALLERY_SELECTOR);
        const touch = event.changedTouches[0];

        if (!gallery || !touch) {
            return;
        }

        const startX = Number(gallery.dataset.touchStartX);
        const startY = Number(gallery.dataset.touchStartY);
        delete gallery.dataset.touchStartX;
        delete gallery.dataset.touchStartY;

        if (!Number.isFinite(startX) || !Number.isFinite(startY)) {
            return;
        }

        const deltaX = touch.clientX - startX;
        const deltaY = touch.clientY - startY;

        if (Math.abs(deltaX) < 45 || Math.abs(deltaX) <= Math.abs(deltaY)) {
            return;
        }

        moveGallery(gallery, deltaX > 0 ? -1 : 1);
    }, { passive: true });

    document.addEventListener('DOMContentLoaded', () => initializeAll());
    document.addEventListener('livewire:navigated', () => initializeAll());

    if (document.readyState !== 'loading') {
        initializeAll();
    }
})();
