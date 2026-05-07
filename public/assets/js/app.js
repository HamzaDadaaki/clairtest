document.addEventListener('DOMContentLoaded', () => {
    const revealItems = document.querySelectorAll('.reveal-on-scroll');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.15 });

    revealItems.forEach((item) => observer.observe(item));

    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach((counter) => {
        const target = Number(counter.dataset.counter || 0);
        if (!target) return;

        let current = 0;
        const increment = Math.max(1, Math.ceil(target / 45));
        const original = counter.textContent.trim().replace(/[0-9]/g, '');
        const update = () => {
            current += increment;
            if (current >= target) {
                counter.textContent = `${target}${original}`;
                return;
            }
            counter.textContent = `${current}${original}`;
            requestAnimationFrame(update);
        };
        update();
    });

    const header = document.querySelector('[data-site-header]');
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const drawer = document.querySelector('[data-site-drawer]');
    const backdrop = document.querySelector('[data-menu-backdrop]');
    const drawerClose = document.querySelector('[data-drawer-close]');

    const setDrawerState = (open) => {
        if (!drawer || !menuToggle || !backdrop) return;
        drawer.classList.toggle('is-open', open);
        drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        backdrop.hidden = !open;
        document.body.style.overflow = open ? 'hidden' : '';
    };

    if (menuToggle && drawer && backdrop) {
        menuToggle.addEventListener('click', () => {
            const open = !drawer.classList.contains('is-open');
            setDrawerState(open);
        });

        backdrop.addEventListener('click', () => setDrawerState(false));
        drawerClose?.addEventListener('click', () => setDrawerState(false));
        drawer.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setDrawerState(false));
        });
    }

    const navWrap = document.querySelector('[data-nav-wrap]');
    const mobileSearchToggle = document.querySelector('[data-mobile-search-toggle]');
    const mobileSearchPanel = document.querySelector('[data-mobile-search-panel]');

    const MOBILE_SEARCH_BREAKPOINT = 768;

    const setMobileSearchState = (open) => {
        if (!navWrap || !mobileSearchToggle || !mobileSearchPanel) return;
        const shouldOpen = window.innerWidth <= MOBILE_SEARCH_BREAKPOINT ? open : false;
        navWrap.classList.toggle('mobile-search-open', shouldOpen);
        mobileSearchToggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
        if (shouldOpen) {
            window.setTimeout(() => {
                const input = mobileSearchPanel.querySelector('input');
                input?.focus();
            }, 40);
        }
    };

    mobileSearchToggle?.addEventListener('click', () => {
        if (window.innerWidth > MOBILE_SEARCH_BREAKPOINT) return;
        const open = !navWrap?.classList.contains('mobile-search-open');
        setMobileSearchState(open);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > MOBILE_SEARCH_BREAKPOINT) {
            setMobileSearchState(false);
        }
    });

    if (header) {
        let lastScrollY = window.scrollY;

        const updateHeader = () => {
            const currentScrollY = window.scrollY;
            const isMenuOpen = drawer?.classList.contains('is-open');

            if (currentScrollY <= 10 || isMenuOpen) {
                header.classList.remove('is-hidden');
            } else if (currentScrollY > lastScrollY && currentScrollY > 120) {
                header.classList.add('is-hidden');
            } else if (currentScrollY < lastScrollY) {
                header.classList.remove('is-hidden');
            }

            lastScrollY = currentScrollY;
        };

        window.addEventListener('scroll', updateHeader, { passive: true });
    }

    const liveSearchForm = document.querySelector('[data-live-search-form]');
    const liveSearchInput = document.querySelector('[data-live-search-input]');
    const liveSearchDropdown = document.querySelector('[data-live-search-dropdown]');
    const liveSearchResults = document.querySelector('[data-live-search-results]');
    const liveSearchViewAll = document.querySelector('[data-live-search-view-all]');

    if (liveSearchForm && liveSearchInput && liveSearchDropdown && liveSearchResults && liveSearchViewAll) {
        let debounceTimer = null;
        let activeRequest = null;

        const closeDropdown = () => {
            liveSearchDropdown.hidden = true;
        };

        const openDropdown = () => {
            liveSearchDropdown.hidden = false;
        };

        const renderResultItem = (item) => {
            return `
                <a class="live-search-item" href="${item.url}">
                    <span class="live-search-thumb"><img src="${item.image}" alt="${item.name}"></span>
                    <span class="live-search-text">
                        <strong>${item.name}</strong>
                        <span>${item.short}</span>
                        <span class="live-search-price">${item.price_label}</span>
                    </span>
                </a>
            `;
        };

        const renderEmptyState = (productsUrl) => {
            liveSearchResults.innerHTML = `
                <div class="live-search-empty">
                    <div>
                        <h4>No result found.</h4>
                        <p>Try another keyword or explore all products.</p>
                    </div>
                    <a href="${productsUrl}" class="ghost-button">Explore products</a>
                </div>
            `;
        };

        const performLiveSearch = async () => {
            const query = liveSearchInput.value.trim();

            if (query.length === 0) {
                closeDropdown();
                return;
            }

            try {
                if (activeRequest) {
                    activeRequest.abort();
                }

                activeRequest = new AbortController();
                const response = await fetch(`/search/live?q=${encodeURIComponent(query)}`, {
                    signal: activeRequest.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Search request failed');
                }

                const payload = await response.json();
                liveSearchViewAll.href = payload.view_all_url;

                if (payload.results.length) {
                    liveSearchResults.innerHTML = payload.results.map(renderResultItem).join('');
                } else {
                    renderEmptyState(payload.products_url);
                }

                openDropdown();
            } catch (error) {
                if (error.name !== 'AbortError') {
                    closeDropdown();
                }
            }
        };

        liveSearchInput.addEventListener('input', () => {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(performLiveSearch, 160);
        });

        liveSearchInput.addEventListener('focus', () => {
            if (liveSearchInput.value.trim().length) {
                performLiveSearch();
            }
        });

        document.addEventListener('click', (event) => {
            if (!liveSearchForm.contains(event.target)) {
                closeDropdown();
                if (window.innerWidth <= MOBILE_SEARCH_BREAKPOINT && navWrap && mobileSearchToggle && mobileSearchPanel && !mobileSearchToggle.contains(event.target)) {
                    setMobileSearchState(false);
                }
            }
        });

        liveSearchForm.addEventListener('submit', () => {
            closeDropdown();
            if (window.innerWidth <= MOBILE_SEARCH_BREAKPOINT) {
                setMobileSearchState(false);
            }
        });
    }

    const galleryRoot = document.querySelector('[data-product-gallery]');
    const lightbox = document.querySelector('[data-image-lightbox]');

    if (galleryRoot && lightbox) {
        const slides = Array.from(galleryRoot.querySelectorAll('[data-gallery-slide]'));
        const thumbs = Array.from(galleryRoot.querySelectorAll('[data-gallery-thumb]'));
        const prevBtn = galleryRoot.querySelector('[data-gallery-prev]');
        const nextBtn = galleryRoot.querySelector('[data-gallery-next]');
        const lightboxImg = lightbox.querySelector('[data-lightbox-image]');
        const lightboxClose = lightbox.querySelector('[data-lightbox-close]');
        const lightboxPrev = lightbox.querySelector('[data-lightbox-prev]');
        const lightboxNext = lightbox.querySelector('[data-lightbox-next]');
        let activeIndex = 0;

        const setSlide = (index) => {
            if (!slides.length) return;
            activeIndex = (index + slides.length) % slides.length;

            slides.forEach((slide, slideIndex) => {
                const isActive = slideIndex === activeIndex;
                slide.classList.toggle('is-active', isActive);
                slides.forEach((slide, slideIndex) => {
    const isActive = slideIndex === activeIndex;

    slide.classList.toggle('is-active', isActive);
    slide.style.display = isActive ? 'block' : 'none';
});
            });

            thumbs.forEach((thumb, thumbIndex) => {
                thumb.classList.toggle('is-active', thumbIndex === activeIndex);
            });

            if (lightbox.hidden === false && lightboxImg) {
                const activeSlide = slides[activeIndex];
                const src = activeSlide.dataset.galleryFull || activeSlide.querySelector('img')?.src || '';
                const alt = activeSlide.querySelector('img')?.alt || 'Product image';
                lightboxImg.src = src;
                lightboxImg.alt = alt;
            }
        };

        const openLightbox = () => {
            if (!lightboxImg || !slides.length) return;
            lightbox.hidden = false;
            document.body.style.overflow = 'hidden';
            setSlide(activeIndex);
        };

        const closeLightbox = () => {
            lightbox.hidden = true;
            document.body.style.overflow = drawer?.classList.contains('is-open') ? 'hidden' : '';
        };

        prevBtn?.addEventListener('click', () => setSlide(activeIndex - 1));
        nextBtn?.addEventListener('click', () => setSlide(activeIndex + 1));
        lightboxPrev?.addEventListener('click', () => setSlide(activeIndex - 1));
        lightboxNext?.addEventListener('click', () => setSlide(activeIndex + 1));
        lightboxClose?.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });

        slides.forEach((slide, index) => {
            slide.addEventListener('click', () => {
                setSlide(index);
                openLightbox();
            });
        });

        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => setSlide(index));
        });

        document.addEventListener('keydown', (event) => {
            if (lightbox.hidden === false) {
                if (event.key === 'Escape') closeLightbox();
                if (event.key === 'ArrowLeft') setSlide(activeIndex - 1);
                if (event.key === 'ArrowRight') setSlide(activeIndex + 1);
            }
        });

        setSlide(0);
    }
});
