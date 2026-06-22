<?php
/**
 * Layout flexible « section_numbers » de la front page : présentation du CRD
 * avec ses chiffres clés. Chargé depuis front-page.php pendant la boucle
 * have_rows('section') — get_sub_field() lit la ligne courante (pointeur global ACF).
 */
defined( 'ABSPATH' ) || exit;

$chapo    = get_sub_field( 'numbers_subtitle' );
$cta_btn  = get_sub_field( 'numbers_show_cta' );
$cta_txt  = get_sub_field( 'numbers_cta_label' );
$cta_link = get_sub_field( 'numbers_cta_link' );
$image    = get_sub_field( 'numbers_image' );
?>
<section class="grid crd">
  <?php if ( $image ) : ?>
  <div class="crd__img">
    <?php echo wp_get_attachment_image( $image['ID'], 'large' ); ?>
  </div>
  <?php endif; ?>
  <header class="crd__header">
    <?php if ( $chapo ) : ?>
    <h2 class="crd__title"><?php echo wp_kses( $chapo, array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'br' => array() ) ); ?></h2>
    <?php endif; ?>
    <?php if ( $cta_btn && $cta_link ) : ?>
      <a class="crd__link btn" href="<?php echo esc_url( $cta_link ); ?>">
        <?php get_template_part( 'template-parts/svg-arrow' ); ?>
        <?php echo esc_html( $cta_txt ); ?>
      </a>
    <?php endif; ?>
  </header>
  <?php if ( have_rows( 'numbers_items' ) ) : ?>
    <div class="crd__stats">
      <?php
      while ( have_rows( 'numbers_items' ) ) :
        the_row();
        $chiffre = get_sub_field( 'number_value' );
        $label   = get_sub_field( 'number_label' );
        ?>
        <p class="stat">
          <span class="stat__chiffre"><?php echo esc_html( $chiffre ); ?></span>
          <span class="stat__text"><?php echo esc_html( $label ); ?></span>
        </p>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
  <div class="crd__bg"></div>
</section>
