<?php 
$hero = function_exists('get_field') ? get_field('header_default', 'header_archives') : [];
$hero_agenda = function_exists('get_field') ? get_field('header_archive_agenda', 'header_archives') : [];

global $post;
$post_id = $post->ID ?? 0;

// Taxonomies liées au CPT agenda
$agenda_taxonomies = array( 'location', 'cat_agenda' );
$is_agenda_tax = is_tax( $agenda_taxonomies );

// Déterminer l'image et le chapo
if ( ( is_post_type_archive( 'agenda' ) || $is_agenda_tax ) && is_array($hero_agenda) ) {
    $img_archive   = $hero_agenda['img_header_archive'] ?? null;
    $chapo_archive = $hero_agenda['desc_header_archive'] ?? '';
} else {
    $img_archive   = $hero['img_header_default'] ?? null;
    $chapo_archive = '';
}

// Déterminer l'image, le titre et le chapo
$img      = '';
$title    = '';
$subtitle = '';
$chapo    = '';

// Attributs pour optimiser le LCP : image hero chargée immédiatement et priorisée
$hero_img_attrs = array( 'loading' => 'eager', 'fetchpriority' => 'high' );

$default_img = ( is_array($hero) && !empty($hero['img_header_default']) )
    ? wp_get_attachment_image( $hero['img_header_default'], 'large', false, $hero_img_attrs )
    : '';

if ( is_post_type_archive() || is_tax() || is_search() ) {
    $img = $img_archive
        ? wp_get_attachment_image( $img_archive, 'large', false, $hero_img_attrs )
        : $default_img;
} elseif ( is_front_page() || is_singular() || is_page() ) {
    $img = get_the_post_thumbnail( $post_id, 'large', $hero_img_attrs ) ?: $default_img;
} else {
    $img = $default_img;
}

if ( is_post_type_archive() ) {
    $title = post_type_archive_title( '', false );
    $chapo = $chapo_archive;
} elseif ( is_tax() ) {
    $title = single_term_title( '', false );
    $chapo = term_description();
} elseif ( is_search() ) {
    $title = 'Recherche';
    $chapo = sprintf( 'Résultats de la recherche pour : %s', get_query_var( 's' ) );
} elseif ( is_404() ) {
    $title = 'Page introuvable';
    $chapo = 'La page que vous cherchez n\'existe pas ou a été déplacée.';
} elseif ( is_front_page() ) {
    $title    = get_bloginfo( 'name' );
    $subtitle = html_entity_decode( get_bloginfo( 'description' ) );
} else {
    $title = get_the_title() ?? '';
    $chapo = get_the_excerpt() ?? '';
}

?>

<div class="grid -fullHeight -withoutMargin hero">

  <header class="hero__header">
    <h1 class="hero__title"><?php echo wp_kses( $title, array( 'br' => array() ) ); ?></h1>

    <?php if ( is_front_page() && $subtitle ) : ?>
      <p class="hero__subTitle"><?php echo esc_html( $subtitle ); ?></p>
    <?php endif; ?>
  </header>

  <?php
    $show_disciplines_menu = is_front_page() || ( ! empty( $args['show_disciplines_menu'] ) );
    $disciplines_items = ( $show_disciplines_menu && function_exists('get_field') )
        ? get_field( 'menu_items', 'header_enseignements' )
        : null;
    $has_disciplines = $show_disciplines_menu && ! empty( $disciplines_items );
  ?>
  <?php if ( $has_disciplines ) : ?>
    <ul class="hero__chapo chapo -col3" role="list" aria-label="Disciplines enseignées">
      <?php foreach ( $disciplines_items as $item ) :
        $item_menu_svg  = $item['menu_item_icon']  ?? null;
        $item_menu_txt  = $item['menu_item_label'] ?? '';
        $item_menu_link = $item['menu_item_url']   ?? '#';
        ?>
        <li class="chapo__title">
          <a class="chapo__link" href="<?php echo esc_url($item_menu_link); ?>">
            <?php if ( $item_menu_svg && isset($item_menu_svg['url']) ): ?>
              <img class="styleSvg" src="<?php echo esc_url($item_menu_svg['url']); ?>" alt="" aria-hidden="true" width="48" height="48">
            <?php endif; ?>
            <?php echo esc_html($item_menu_txt); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else : ?>
    <div class="hero__chapo chapo">
      <?php if ( $chapo ) : ?>
        <p class="chapo__text"><?php echo esc_html($chapo); ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php // $img = sortie de wp_get_attachment_image() (HTML core déjà sûr, conserve fetchpriority/loading pour le LCP). ?>
  <div class="duotone hero__img"><?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

  <?php if ( is_front_page() ) : ?>
    <?php get_template_part( 'template-parts/pattern', null, array( 'wrapper_class' => 'hero__patterns' ) ); ?>
  <?php endif; ?>

</div>
