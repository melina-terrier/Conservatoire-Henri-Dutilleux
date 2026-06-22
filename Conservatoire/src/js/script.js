// Hoisté en tête de fichier : utilisé par Rellax, GSAP, et l'animation des motifs.
// On écoute aussi `change` pour réagir si l'utilisateur change sa préférence
// pendant la session (rare mais correct côté a11y).
const reducedMotionMql = window.matchMedia('(prefers-reduced-motion: reduce)');
let prefersReducedMotion = reducedMotionMql.matches;
reducedMotionMql.addEventListener('change', function (e) { prefersReducedMotion = e.matches; });

// Sélecteurs focusables pour le focus trap (overlay menu + search).
const FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

/**
 * Piège le focus clavier à l'intérieur d'un conteneur tant qu'il est ouvert.
 * Tab depuis le dernier élément renvoie au premier (et inversement avec Shift+Tab).
 * Sans ça, l'utilisateur peut tabber vers le contenu en arrière-plan d'un overlay
 * plein écran — non conforme aux ARIA Authoring Practices.
 */
function trapFocus(container, e) {
  const focusables = container.querySelectorAll(FOCUSABLE_SELECTOR);
  if (!focusables.length) return;
  const first = focusables[0];
  const last  = focusables[focusables.length - 1];
  if (e.shiftKey && document.activeElement === first) {
    e.preventDefault();
    last.focus();
  } else if (!e.shiftKey && document.activeElement === last) {
    e.preventDefault();
    first.focus();
  }
}


/*------------------------------------*\
  # AFFICHAGE DU MENU POUR LE MOBILE
\*------------------------------------*/

const hamburger = document.querySelector('.menuBurger');
const menu      = document.querySelector('.menu');
const page      = document.documentElement;

function closeMenu() {
  hamburger.classList.remove('-open');
  menu.classList.remove('-open');
  page.classList.remove('noscroll');
  hamburger.setAttribute('aria-expanded', 'false');
  hamburger.setAttribute('aria-label', 'Ouvrir le menu principal');
  hamburger.focus();
}

if (hamburger && menu) {
  hamburger.addEventListener('click', function () {
    this.classList.toggle('-open');
    menu.classList.toggle('-open');
    page.classList.toggle('noscroll');
    const expanded = this.getAttribute('aria-expanded') === 'true';
    this.setAttribute('aria-expanded', String(!expanded));
    this.setAttribute('aria-label', expanded ? 'Ouvrir le menu principal' : 'Fermer le menu principal');

    // À l'ouverture, déplace le focus sur le premier élément focusable de
    // l'overlay — sinon Tab part dans le DOM en arrière-plan avant que
    // `trapFocus` ne puisse intercepter (il s'active sur first/last only).
    if (menu.classList.contains('-open')) {
      const firstFocusable = menu.querySelector(FOCUSABLE_SELECTOR);
      if (firstFocusable) firstFocusable.focus();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (!menu.classList.contains('-open')) return;
    if (e.key === 'Escape') {
      closeMenu();
    } else if (e.key === 'Tab') {
      trapFocus(menu, e);
    }
  });
}


/*------------------------------------*\
  # SEARCHFORM (overlay header)
\*------------------------------------*/

const openCtrl        = document.querySelector('.header__search');
const closeCtrl       = document.querySelector('.searchForm__close');
const searchContainer = document.querySelector('.searchForm');
const inputSearch     = searchContainer ? searchContainer.querySelector('.searchForm__input') : null;

function openSearch() {
  searchContainer.classList.add('-open');
  inputSearch.focus();
}

function closeSearch() {
  searchContainer.classList.remove('-open');
  inputSearch.blur();
  inputSearch.value = '';
  if (openCtrl) openCtrl.focus();
}

if (openCtrl && closeCtrl && searchContainer && inputSearch) {
  openCtrl.addEventListener('click', openSearch);
  closeCtrl.addEventListener('click', closeSearch);

  document.addEventListener('keydown', function (e) {
    if (!searchContainer.classList.contains('-open')) return;
    if (e.key === 'Escape') {
      closeSearch();
    } else if (e.key === 'Tab') {
      trapFocus(searchContainer, e);
    }
  });
}


/*------------------------------------*\
  # INIT HEADROOM
\*------------------------------------*/

const navBar = document.querySelector('.headroom');
if (navBar && typeof Headroom !== 'undefined') {
  // offset 205 = hauteur du hero avant que le header devienne sticky/cache.
  new Headroom(navBar, { offset: 205 }).init();
}


/*------------------------------------*\
  # INIT FLICKITY CAROUSEL
\*------------------------------------*/

const carousel = document.querySelector('.carousel');
if (carousel && typeof Flickity !== 'undefined') {
  new Flickity(carousel, {
    wrapAround: true,
    imagesLoaded: true,
    lazyLoad: 3,
    cellAlign: 'left',
    arrowShape: 'M44.314 64.142L31.586 51.414a2 2 0 010-2.828l12.728-12.728a2 2 0 112.828 2.828L37.828 48H73v4H37.828l9.314 9.314a2 2 0 11-2.828 2.828z'
  });
}


/*------------------------------------*\
  # PARALLAXE
\*------------------------------------*/

const rellaxEl = document.querySelector('.rellax');
if (rellaxEl && typeof Rellax !== 'undefined' && !prefersReducedMotion) {
  new Rellax('.rellax');
}


/*------------------------------------*\
  # ANIMATION DES MOTIFS (GSAP)
\*------------------------------------*/

const curveSvgs = document.querySelectorAll('.curveLines');
const lines     = document.querySelectorAll('.curveLines path');

if (lines.length && typeof gsap !== 'undefined') {
  if (prefersReducedMotion) {
    // Respect du choix utilisateur : pas d'animation, on rend visible directement.
    lines.forEach(function (el) {
      gsap.set(el, { opacity: 1, strokeDashoffset: 0 });
    });
  } else if ('IntersectionObserver' in window) {
    // Anime chaque path quand son SVG parent entre dans le viewport — évite
    // de tout déclencher au load sur les pages avec plusieurs blocs `.curveLines`.
    lines.forEach(function (el) {
      const len = el.getTotalLength();
      gsap.set(el, { strokeDasharray: len, strokeDashoffset: len, opacity: 0 });
    });

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        const paths = entry.target.querySelectorAll('path');
        paths.forEach(function (el, i) {
          gsap.to(el, {
            strokeDashoffset: 0,
            opacity: 1,
            duration: 2,
            delay: i / 4 + 0.5,
          });
        });
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.1 });

    curveSvgs.forEach(function (svg) { observer.observe(svg); });
  } else {
    // Fallback navigateurs sans IO : on anime tout au load (comportement initial).
    lines.forEach(function (el, i) {
      const len = el.getTotalLength();
      gsap.set(el, { strokeDasharray: len });
      gsap.fromTo(el,
        { strokeDashoffset: len, opacity: 0 },
        { strokeDashoffset: 0, opacity: 1, duration: 2, delay: i / 4 + 0.5 }
      );
    });
  }
}

/*------------------------------------*\
  # EVENTS ANALYTICS (GA / GTM)
\*------------------------------------*/

document.addEventListener('click', function(e) {
  const contactLink = e.target.closest('a[href^="tel:"], a[href^="mailto:"]');
  if (contactLink && typeof dataLayer !== 'undefined') {
    const isTel = contactLink.getAttribute('href').startsWith('tel:');
    dataLayer.push({
      event: 'contact_click',
      contact_type: isTel ? 'phone' : 'email',
      contact_value: contactLink.getAttribute('href').replace(/^(tel:|mailto:)/, '')
    });
  }
});

document.addEventListener('wpcf7mailsent', function(event) {
  if (typeof dataLayer !== 'undefined') {
    dataLayer.push({
      event: 'form_submission',
      form_id: event.detail.contactFormId
    });
  }
}, false);
