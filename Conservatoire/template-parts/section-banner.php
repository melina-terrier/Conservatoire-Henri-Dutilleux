<?php
/**
 * Layout flexible « section_banner » de la front page : bandeau inscription
 * (titre + intro + CTA + image en duotone). Chargé depuis front-page.php
 * pendant la boucle have_rows('section').
 */
defined( 'ABSPATH' ) || exit;

$title    = get_sub_field( 'banner_title' );
$chapo    = get_sub_field( 'banner_subtitle' );
$cta_btn  = get_sub_field( 'banner_show_cta' );
$cta_txt  = get_sub_field( 'banner_cta_label' );
$cta_link = get_sub_field( 'banner_cta_link' );
$image    = get_sub_field( 'banner_image' );
?>
<section class="grid -withHeader -inverse -withoutMargin section -bg">
  <header class="section__header -inverse">
    <?php if ( $title ) : ?>
    <h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
    <?php endif; ?>
    <?php if ( $chapo ) : ?>
    <p class="section__intro"><?php echo wp_kses_post( $chapo ); ?></p>
    <?php endif; ?>
  </header>
  <?php if ( $cta_btn && $cta_link ) : ?>
  <a class="section__link btn -outlined" href="<?php echo esc_url( $cta_link ); ?>">
    <?php get_template_part( 'template-parts/svg-arrow' ); ?>
    <?php echo esc_html( $cta_txt ); ?>
  </a>
  <?php endif; ?>
  <?php if ( $image ) : ?>
  <div class="duotone section__img">
    <?php echo wp_get_attachment_image( $image['ID'], 'large' ); ?>
  </div>
  <?php endif; ?>
</section>
