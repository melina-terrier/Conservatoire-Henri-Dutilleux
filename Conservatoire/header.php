<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<header class="header headroom">

		<div class="header__start">
      <?php 
        if ( has_custom_logo() ) :
          $logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
      ?>
      <a class="header__logo" href="<?php 
        echo esc_url( home_url( '/' ) ); ?>" aria-label="Logo du Conservatoire">
        <img src="<?php echo esc_url( $logo[0] ); ?>" class="style-svg"/>
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

      <button class="header__search" aria-label="Rechercher">
				<svg width="24" height="24" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="11" cy="10" r="7" stroke="#191919" stroke-width="2" />
          <path stroke="#191919" stroke-width="2" d="M17.707 17.293l3.536 3.535" />
        </svg>
      </button>

    </div>  
	</header>

	<main>
