<?php
/**
 * Layout flexible « section_featured » de la front page : section mise en avant
 * (titre + intro + CTA, et optionnellement les 3 prochains événements).
 * Chargé depuis front-page.php pendant la boucle have_rows('section').
 */
defined( 'ABSPATH' ) || exit;

$title    = get_sub_field( 'featured_title' );
$chapo    = get_sub_field( 'featured_subtitle' );
$cta_btn  = get_sub_field( 'featured_show_cta' );
$cta_txt  = get_sub_field( 'featured_cta_label' );
$cta_link = get_sub_field( 'featured_cta_link' );
$cpt_btn  = get_sub_field( 'featured_show_events' );
?>
<section class="grid -withHeader section">
  <header class="section__header">
    <?php if ( $title ) : ?>
    <h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
    <?php endif; ?>
    <?php if ( $chapo ) : ?>
    <p class="section__intro"><?php echo wp_kses_post( $chapo ); ?></p>
    <?php endif; ?>
  </header>
  <?php if ( $cta_btn && $cta_link ) : ?>
  <a class="section__link btn" href="<?php echo esc_url( $cta_link ); ?>">
    <?php get_template_part( 'template-parts/svg-arrow' ); ?>
    <?php echo esc_html( $cta_txt ); ?>
  </a>
  <?php endif; ?>
  <?php
  if ( $cpt_btn ) :
    $query = crdtheme_get_upcoming_events( array( 'limit' => 3 ) );
    if ( $query->have_posts() ) :
      while ( $query->have_posts() ) :
        $query->the_post();
        get_template_part( 'template-parts/card' );
      endwhile;
      wp_reset_postdata();
    endif;
  endif;
  ?>
</section>
