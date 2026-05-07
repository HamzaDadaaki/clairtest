const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.dot');
let slideIndex = 0;

function setSlide(index) {
  slides.forEach((slide, i) => slide.classList.toggle('active', i === index));
  dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
  slideIndex = index;
}

// Replace hero slide images with random product images when available
if (window.heroProductImages && Array.isArray(window.heroProductImages) && window.heroProductImages.length) {
  slides.forEach((slide) => {
    const img = window.heroProductImages[Math.floor(Math.random() * window.heroProductImages.length)];
    if (img) slide.style.backgroundImage = `url('${img}')`;
  });
}

if (slides.length > 0) {
  setInterval(() => setSlide((slideIndex + 1) % slides.length), 5000);
  dots.forEach((dot, index) => dot.addEventListener('click', () => setSlide(index)));
}

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.14 });

document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));

const siteHeader = document.getElementById('siteHeader');
let lastScrollY = window.scrollY;

function handleHeaderScroll() {
  if (!siteHeader) return;

  const currentScrollY = window.scrollY;

  if (currentScrollY <= 12) {
    siteHeader.classList.remove('is-hidden');
  } else if (currentScrollY > lastScrollY) {
    siteHeader.classList.add('is-hidden');
  } else {
    siteHeader.classList.remove('is-hidden');
  }

  lastScrollY = currentScrollY;
}

window.addEventListener('scroll', handleHeaderScroll, { passive: true });

const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mobileNavPanel = document.getElementById('mobileNavPanel');

function closeMobileMenu() {
  if (!mobileMenuToggle || !mobileNavPanel) return;
  mobileMenuToggle.setAttribute('aria-expanded', 'false');
  mobileNavPanel.hidden = true;
}

if (mobileMenuToggle && mobileNavPanel) {
  mobileMenuToggle.addEventListener('click', () => {
    const isOpen = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
    mobileMenuToggle.setAttribute('aria-expanded', String(!isOpen));
    mobileNavPanel.hidden = isOpen;
  });

  mobileNavPanel.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', closeMobileMenu);
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 860) {
      closeMobileMenu();
    }
  });
}

const backgroundVideo = document.getElementById('siteBackgroundVideo');

if (backgroundVideo) {
  backgroundVideo.muted = true;
  backgroundVideo.defaultMuted = true;
  backgroundVideo.playsInline = true;

  const tryPlayVideo = () => {
    const playPromise = backgroundVideo.play();
    if (playPromise && typeof playPromise.catch === 'function') {
      playPromise.catch(() => {});
    }
  };

  ['loadeddata', 'canplay', 'pageshow'].forEach((eventName) => {
    window.addEventListener(eventName, tryPlayVideo, { passive: true });
  });

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
      tryPlayVideo();
    }
  });

  backgroundVideo.addEventListener('ended', () => {
    backgroundVideo.currentTime = 0.01;
    tryPlayVideo();
  });

  tryPlayVideo();
}

const productGallery = document.querySelector('.gallery-main[data-images]');

if (productGallery) {
  let images = [];
  try {
    images = JSON.parse(productGallery.getAttribute('data-images') || '[]');
  } catch (_error) {
    images = [];
  }

  if (Array.isArray(images) && images.length > 0) {
    let currentIndex = Number(productGallery.getAttribute('data-current') || 0);

    const setProductImage = (nextIndex) => {
      currentIndex = (nextIndex + images.length) % images.length;
      productGallery.style.backgroundImage = `url(${images[currentIndex]})`;
      productGallery.setAttribute('data-current', String(currentIndex));
    };

    const prevButton = productGallery.querySelector('.gallery-prev');
    const nextButton = productGallery.querySelector('.gallery-next');

    if (prevButton) {
      prevButton.addEventListener('click', () => setProductImage(currentIndex - 1));
    }

    if (nextButton) {
      nextButton.addEventListener('click', () => setProductImage(currentIndex + 1));
    }
  }
}

/* --- Email List Form Handler --- */
const emailSubscribeForm = document.getElementById('email-subscribe-form');
const emailFormMessage = document.getElementById('email-form-message');

if (emailSubscribeForm) {
  emailSubscribeForm.addEventListener('submit', (e) => {
    e.preventDefault();

    // Get the email input value
    const emailInput = emailSubscribeForm.querySelector('.email-input');
    const email = emailInput.value.trim();

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showMessage('Please enter a valid email address.', 'error');
      return;
    }

    // Disable submit button during processing
    const submitButton = emailSubscribeForm.querySelector('.button-email-submit');
    const originalButtonText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.textContent = 'Joining...';

    // Show success message
    // Future: Replace this with actual email service integration (Mailchimp, ConvertKit, etc.)
    showMessage('Email list coming soon — thank you for your interest!', 'success');

    // Reset form
    emailSubscribeForm.reset();

    // Re-enable submit button
    setTimeout(() => {
      submitButton.disabled = false;
      submitButton.textContent = originalButtonText;
    }, 2000);
  });

  function showMessage(text, type) {
    if (!emailFormMessage) return;

    emailFormMessage.textContent = text;
    emailFormMessage.className = `email-form-message show ${type}`;

    // Auto-hide message after 5 seconds
    setTimeout(() => {
      emailFormMessage.classList.remove('show');
    }, 5000);
  }
}

/* Claire Admin V3 — Kimi-style tabs and password toggle */
document.addEventListener('DOMContentLoaded', () => {
  const tabButtons = document.querySelectorAll('[data-admin-tab]');
  const panels = document.querySelectorAll('[data-admin-panel]');

  function showAdminPanel(tabName) {
    tabButtons.forEach((button) => {
      button.classList.toggle('active', button.getAttribute('data-admin-tab') === tabName);
    });

    panels.forEach((panel) => {
      panel.classList.toggle('active', panel.getAttribute('data-admin-panel') === tabName);
    });

    if (window.history && window.history.replaceState) {
      window.history.replaceState(null, '', `#${tabName}`);
    }
  }

  if (tabButtons.length && panels.length) {
    tabButtons.forEach((button) => {
      button.addEventListener('click', () => showAdminPanel(button.getAttribute('data-admin-tab')));
    });

    const initialTab = (window.location.hash || '').replace('#', '');
    const hasInitialTab = Array.from(tabButtons).some((button) => button.getAttribute('data-admin-tab') === initialTab);
    showAdminPanel(hasInitialTab ? initialTab : 'originals');
  }

  const passwordToggle = document.querySelector('[data-password-toggle]');
  const passwordInput = document.getElementById('admin-password');
  if (passwordToggle && passwordInput) {
    passwordToggle.addEventListener('click', () => {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
      passwordToggle.textContent = isPassword ? '🙈' : '👁';
    });
  }
});
