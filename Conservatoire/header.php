<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#1d096b">

  <?php $favicon_uri = esc_url( get_template_directory_uri() ) . '/src/img/favicons'; ?>
  <link rel="icon" type="image/x-icon" href="<?php echo $favicon_uri; ?>/favicon.ico" sizes="32x32">
  <link rel="icon" type="image/svg+xml" href="<?php echo $favicon_uri; ?>/favicon.svg">
  <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $favicon_uri; ?>/favicon-96x96.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $favicon_uri; ?>/apple-touch-icon.png">
  <link rel="manifest" href="<?php echo $favicon_uri; ?>/site.webmanifest">

  <link rel="preload" href="<?php echo esc_url( get_template_directory_uri() ); ?>/src/fonts/Mulish-VariableFont_wght.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?php echo esc_url( get_template_directory_uri() ); ?>/src/fonts/SpaceMono-Regular.woff2" as="font" type="font/woff2" crossorigin>
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
          $logo_w = ! empty( $logo[1] ) ? intval( $logo[1] ) : 180;
          $logo_h = ! empty( $logo[2] ) ? intval( $logo[2] ) : 60;
      ?>
      <a class="header__logo" href="<?php
        echo esc_url( home_url( '/' ) ); ?>" aria-label="Accueil du Conservatoire">
        <img src="<?php echo esc_url( $logo[0] ); ?>" class="styleSvg" alt="<?php bloginfo( 'name' ); ?>" width="<?php echo $logo_w; ?>" height="<?php echo $logo_h; ?>"/>
      </a>
      <?php endif; ?>
    </div>

		<div class="header__end">

      <button class="header__menuBtn menuBurger" aria-label="menu" aria-expanded="false" aria-controls="mainNav">
        <span class="menuBurger__bar" aria-hidden="true"></span>
      </button>

      <?php get_search_form(); ?>

      <nav class="header__menu menu" id="mainNav" aria-label="Menu principal">
        <?php
        wp_nav_menu( array(
          'theme_location' => 'primary-menu',
          'container'      => false,
          'menu_class'     => 'menu__list',
          'walker'         => new Crd_Walker_Nav_Menu()
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
