/*------------------------------------*\
  # AFFICHAGE DU MENU POUR LE MOBILE
\*------------------------------------*/

const hamburger = document.querySelector(".menuBurger");
const menu = document.querySelector(".menu");
const page = document.documentElement;

function doToggle() {
  this.classList.toggle('-open');
  menu.classList.toggle('-open');
  page.classList.toggle('noscroll');
}

hamburger.addEventListener('click', doToggle);


/*------------------------------------*\
  # SEARCHFORM
\*------------------------------------*/

var openCtrl        = document.querySelector('.header__search'),
    closeCtrl       = document.querySelector('.searchForm__close'),
    searchContainer = document.querySelector('.searchForm'),
    inputSearch     = searchContainer.querySelector('.searchForm__input');

function init() {
  initEvents();	
}

function initEvents() {
  openCtrl.addEventListener('click', openSearch);
  closeCtrl.addEventListener('click', closeSearch);
}

function openSearch() {
  searchContainer.classList.add('-open');
  inputSearch.focus();
}

function closeSearch() {
  searchContainer.classList.remove('-open');
  inputSearch.blur();
  inputSearch.value = '';
}

init();

/*------------------------------------*\
  # INIT HEADROOM
\*------------------------------------*/

var navBar = document.querySelector(".headroom");
var headroom = new Headroom(navBar, {
  offset: 205
});
headroom.init();

/*------------------------------------*\
  # INIT FLICKITY CAROUSEL
\*------------------------------------*/

var carousel = document.querySelector('.carousel');
if ( carousel ) {
  var flkty = new Flickity( carousel, {
    wrapAround: true,
    imagesLoaded: true,
    lazyLoad: 3,
    cellAlign: 'left',
    arrowShape: 'M44.314 64.142L31.586 51.414a2 2 0 010-2.828l12.728-12.728a2 2 0 112.828 2.828L37.828 48H73v4H37.828l9.314 9.314a2 2 0 11-2.828 2.828z'
  })
}

/*------------------------------------*\
  # PARALLAXE
\*------------------------------------*/

if (document.querySelector('.rellax')) {
  var rellax = new Rellax('.rellax');
}

/*------------------------------------*\
  # ANIMATION DES MOTIFS
\*------------------------------------*/

var lines = document.querySelectorAll(".curveLines path");
var i = 0;
if ( lines ) {
  lines.forEach( function( el ) {
    gsap.set(el, { strokeDasharray: el.getTotalLength() });
    gsap.fromTo(el,
      { strokeDashoffset: el.getTotalLength(), opacity: 0 },
      { strokeDashoffset: 0, opacity: 1, duration: 2, delay: i/4 + .5 }
    )
    i++
  });
}


/*------------------------------------*\
  # PAGINATION AJAX
\*------------------------------------*/

function loadPosts(page) {
	const container = document.getElementById("ajax-response");
	const cleanUrl = page === 1 ? esgiValues.base : `${esgiValues.base}page/${page}/`;

	fetch(`${esgiValues.ajaxURL}?action=loadPosts&page=${page}&base=${esgiValues.base}&nonce=${esgiValues.nonce}`)
		.then((response) => {
			if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
			return response.json();
		})
		.then((data) => {
			container.innerHTML = data.cards;
			const navLinks = document.querySelector(".nav-links");
			if (navLinks) navLinks.innerHTML = data.pagination;
			window.history.pushState({}, "", cleanUrl);
			window.scrollTo({ top: container.offsetTop - 100, behavior: "smooth" });
		})
		.catch((error) => {
			console.error("Erreur lors du chargement des articles :", error);
		});
}

document.addEventListener("DOMContentLoaded", () => {
	const nav = document.querySelector(".navigation.pagination");
	if (!nav) return;

	nav.addEventListener("click", (event) => {
		const el = event.target.closest("a.page-numbers");
		if (!el) return;
		event.preventDefault();

		const href = el.getAttribute("href");
		const match = href.match(/\/page\/(\d+)/) || href.match(/[?&]paged=(\d+)/);
		const page = match ? parseInt(match[1], 10) : 1;

		loadPosts(page);
	});
});
