<?php
  $infos       = crdtheme_get_site_infos();
  $logo_footer = $infos['logo_footer']  ?? null;
  $logo_gb     = $infos['logo_partner'] ?? null;
?>

	</main>
  
  <footer class="grid -withoutMargin footer">
    
    <?php if ( $logo_footer ) :
      $lf_w = ! empty( $logo_footer['width'] )  ? (int) $logo_footer['width']  : 200;
      $lf_h = ! empty( $logo_footer['height'] ) ? (int) $logo_footer['height'] : 80;
    ?>
    <div class="footer__logo">
      <img src="<?php echo esc_url( $logo_footer['url'] ); ?>" class="styleSvg" alt="" aria-hidden="true" width="<?php echo (int) $lf_w; ?>" height="<?php echo (int) $lf_h; ?>" loading="lazy" decoding="async"/>
    </div>
    <?php endif; ?>

    <nav class="footer__menu" aria-label="Menu du pied de page">
      <h2 class="footer__title">Menu</h2>
      <?php wp_nav_menu( array(
        'theme_location' => 'footer-menu',
        'container'      => false,
        'menu_class'     => 'listUnstyled',
      ) ); ?>
    </nav>

    <div class="footer__contact">
      <h2 class="footer__title">Contact</h2>
      <?php get_template_part( 'template-parts/contact-block', null, array( 'address_class' => 'footer__address' ) ); ?>
    </div>

    <div class="footer__social">
      <?php if( have_rows( 'site_social_networks', 'infos' ) ): ?>
      <h2 class="footer__title">Nous suivre</h2>
      <ul class="footer__list listUnstyled">
        <?php while(have_rows('site_social_networks', 'infos')): the_row();
          $reseau_social = get_sub_field('social_network_name');
          $url = get_sub_field('social_network_url');
          $icon = get_sub_field('social_network_icon');
        ?>
        <li>
          <a href="<?php echo esc_url( $url ); ?>" aria-label="Notre page <?php echo esc_attr( $reseau_social ); ?> (nouvelle fenêtre)" target="_blank" rel="noopener noreferrer">
            <?php if ( $icon && ! empty( $icon['url'] ) ) : ?>
              <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" aria-hidden="true" width="24" height="24" loading="lazy" decoding="async">
            <?php endif; ?>
            <span class="sr-only"><?php echo esc_html( $reseau_social ); ?></span>
          </a>
        </li>
        <?php endwhile; ?>
      </ul>
      <?php endif; ?>
    </div>

    <?php if ( $logo_gb ) :
      $gb_w = ! empty( $logo_gb['width'] )  ? (int) $logo_gb['width']  : 200;
      $gb_h = ! empty( $logo_gb['height'] ) ? (int) $logo_gb['height'] : 80;
    ?>
    <div class="footer__gb">
      <img src="<?php echo esc_url( $logo_gb['url'] ); ?>" class="styleSvg" alt="<?php echo esc_attr( $logo_gb['alt'] ?? 'Grand Belfort' ); ?>" width="<?php echo (int) $gb_w; ?>" height="<?php echo (int) $gb_h; ?>" loading="lazy" decoding="async"/>
    </div>
    <?php endif; ?>

    <small class="footer__copyright">© <?php echo esc_html( wp_date( 'Y' ) ); ?> <a href="https://www.melinaterrier.fr/" target="_blank" rel="noopener noreferrer">Mélina Terrier</a> — Projet d'exercice pédagogique</small>
    
  </footer>
  
  <?php 
    wp_footer(); 
  ?>
</body>
</html>
