<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#1d096b">

  <!-- CookieYes (CMP) — chargé tôt pour gérer le consentement avant les autres scripts -->
  <script id="cookieyes" type="text/javascript" src="https://cdn-cookieyes.com/client_data/3c85f9738b2fa10643bbd4a52b6e01d3/script.js"></script>

  <?php $favicon_uri = get_template_directory_uri() . '/src/img/favicons'; ?>
  <link rel="icon" type="image/x-icon" href="<?php echo esc_url( $favicon_uri . '/favicon.ico' ); ?>" sizes="32x32">
  <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $favicon_uri . '/favicon.svg' ); ?>">
  <link rel="icon" type="image/png" sizes="96x96" href="<?php echo esc_url( $favicon_uri . '/favicon-96x96.png' ); ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $favicon_uri . '/apple-touch-icon.png' ); ?>">
  <link rel="manifest" href="<?php echo esc_url( $favicon_uri . '/site.webmanifest' ); ?>">

  <link rel="preload" href="<?php echo esc_url( get_template_directory_uri() ); ?>/src/fonts/Mulish-VariableFont_wght.woff2" as="font" type="font/woff2" crossorigin>
  <?php // Space Mono = police des titres h1/h2 (présents above-the-fold dans le hero) : précharge pour éviter le FOUT sur le titre LCP. ?>
  <link rel="preload" href="<?php echo esc_url( get_template_directory_uri() ); ?>/src/fonts/SpaceMono-Regular.woff2" as="font" type="font/woff2" crossorigin>

  <?php
  /**
   * Preload de l'image LCP du hero — sur front-page et singular avec featured image.
   * Contourne le lazy-loading automatique de WordPress 6.5+ qui peut écraser
   * un loading="eager" passé en attribut explicite : le preload démarre le fetch
   * avant le rendu, donc le loading reste un détail.
   */
  $crdtheme_hero_id = 0;
  if ( is_front_page() ) {
    $crdtheme_hero_id = (int) get_post_thumbnail_id( (int) get_option( 'page_on_front' ) );
  } elseif ( is_singular() ) {
    $crdtheme_hero_id = (int) get_post_thumbnail_id();
  }
  if ( $crdtheme_hero_id ) :
    $crdtheme_hero_url    = wp_get_attachment_image_url( $crdtheme_hero_id, 'large' );
    $crdtheme_hero_srcset = wp_get_attachment_image_srcset( $crdtheme_hero_id, 'large' );
    if ( $crdtheme_hero_url ) : ?>
      <link rel="preload" as="image"
            href="<?php echo esc_url( $crdtheme_hero_url ); ?>"
            <?php if ( $crdtheme_hero_srcset ) : ?>imagesrcset="<?php echo esc_attr( $crdtheme_hero_srcset ); ?>" imagesizes="100vw"<?php endif; ?>
            fetchpriority="high">
  <?php endif; endif; ?>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<a class="skipLink" href="#main">Aller au contenu</a>

	<header class="header headroom">

		<div class="header__start">
      <?php
        if ( has_custom_logo() ) :
          $logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
          $logo_w = ! empty( $logo[1] ) ? (int) $logo[1] : 180;
          $logo_h = ! empty( $logo[2] ) ? (int) $logo[2] : 60;
          if ( $logo ) :
      ?>
      <a class="header__logo" href="<?php
        echo esc_url( home_url( '/' ) ); ?>" aria-label="Accueil du Conservatoire">
        <img src="<?php echo esc_url( $logo[0] ); ?>" class="styleSvg" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="<?php echo (int) $logo_w; ?>" height="<?php echo (int) $logo_h; ?>" decoding="async"/>
      </a>
      <?php endif; endif; ?>
    </div>

		<div class="header__end">

      <button class="header__menuBtn menuBurger" aria-label="Ouvrir le menu principal" aria-expanded="false" aria-controls="mainNav">
        <span class="menuBurger__bar" aria-hidden="true"></span>
      </button>

      <?php get_search_form(); ?>

      <nav class="header__menu menu" id="mainNav" aria-label="Menu principal">
        <?php
        wp_nav_menu( array(
          'theme_location' => 'primary-menu',
          'container'      => false,
          'menu_class'     => 'menu__list',
          'walker'         => new Crdtheme_Walker_Nav_Menu()
        ) );
        ?>
      </nav>

      <button type="button" class="header__search" aria-label="Rechercher">
				<svg width="24" height="24" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="11" cy="10" r="7" stroke="#191919" stroke-width="2" />
          <path stroke="#191919" stroke-width="2" d="M17.707 17.293l3.536 3.535" />
        </svg>
      </button>

    </div>  
	</header>

	<main id="main" tabindex="-1">
