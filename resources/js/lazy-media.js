const loadDeferredImage = (image) => {
    if (!(image instanceof HTMLImageElement) || image.dataset.loaded === 'true') {
        return;
    }

    if (image.dataset.srcset) {
        image.srcset = image.dataset.srcset;
    }

    if (image.dataset.sizes) {
        image.sizes = image.dataset.sizes;
    }

    if (image.dataset.src) {
        image.src = image.dataset.src;
    }

    image.dataset.loaded = 'true';
    image.removeAttribute('data-src');
    image.removeAttribute('data-srcset');
    image.removeAttribute('data-sizes');
};

const loadDeferredSvgImages = (root) => {
    root.querySelectorAll('image[data-deferred-href]').forEach((image) => {
        const href = image.getAttribute('data-deferred-href');

        if (!href) {
            return;
        }

        image.setAttribute('href', href);
        image.removeAttribute('data-deferred-href');
    });
};

const observe = (elements, loader, rootMargin = '280px 0px') => {
    if (!('IntersectionObserver' in window)) {
        elements.forEach(loader);
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            loader(entry.target);
            observer.unobserve(entry.target);
        });
    }, { rootMargin, threshold: 0.01 });

    elements.forEach((element) => observer.observe(element));
};

const bootDeferredMedia = () => {
    const deferredImages = [...document.querySelectorAll('img[data-src]')];
    observe(deferredImages, loadDeferredImage, '240px 0px');

    const deferredSvgRoots = [...document.querySelectorAll('[data-deferred-svg-root]')];
    observe(deferredSvgRoots, loadDeferredSvgImages, '180px 0px');
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootDeferredMedia, { once: true });
} else {
    bootDeferredMedia();
}

document.addEventListener('livewire:navigated', bootDeferredMedia);
