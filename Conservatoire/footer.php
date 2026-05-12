<?php
  $adresse    = get_field('site_address', 'infos');
  $tel        = get_field('site_phone', 'infos');
  $mail       = get_field('site_email', 'infos');
  $logo_footer = get_field('site_logo_footer', 'infos');
  $logo_gb     = get_field('site_logo_partner', 'infos');
?>

	</main>
  
  <footer class="grid -withoutMargin footer">
    
    <?php if ( $logo_footer ) :
      $lf_w = ! empty( $logo_footer['width'] )  ? intval( $logo_footer['width'] )  : 200;
      $lf_h = ! empty( $logo_footer['height'] ) ? intval( $logo_footer['height'] ) : 80;
    ?>
    <div class="footer__logo">
      <img src="<?php echo esc_url( $logo_footer['url'] ); ?>" class="styleSvg" alt="" aria-hidden="true" width="<?php echo $lf_w; ?>" height="<?php echo $lf_h; ?>"/>
    </div>
    <?php endif; ?>

    <nav class="footer__menu" aria-label="Menu du pied de page">
      <p class="footer__title"><strong>Menu</strong></p>
      <?php wp_nav_menu( array(
        'theme_location' => 'footer-menu',
        'container'      => false,
        'menu_class'     => 'listUnstyled',
      ) ); ?>
    </nav>

    <div class="footer__contact">
      <p class="footer__title"><strong>Contact</strong></p>
      <address class="footer__address">
        <?php if ( $adresse ) : ?>
          <?php echo esc_html( $adresse['street_number'] ); ?> <?php echo esc_html( $adresse['street_name'] ); ?><br>
          <?php echo esc_html( $adresse['post_code'] ); ?> <?php echo esc_html( $adresse['city'] ); ?><br>
        <?php endif; ?>
        <?php if ( $tel ) : ?>
          <a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $tel ); ?></a><br>
        <?php endif; ?>
        <?php if ( $mail ) : ?>
          <a href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a>
        <?php endif; ?>
      </address>
    </div>

    <div class="footer__social">
      <?php if( have_rows( 'site_social_networks', 'infos' ) ): ?>
      <p class="footer__title"><strong>Nous suivre</strong></p>
      <ul class="footer__list listUnstyled">
        <?php while(have_rows('site_social_networks', 'infos')): the_row();
          $reseau_social = get_sub_field('social_network_name');
          $url = get_sub_field('social_network_url');
          $icon = get_sub_field('social_network_icon');
        ?>
        <li>
          <a href="<?php echo esc_url( $url ); ?>" aria-label="Notre page <?php echo esc_attr( $reseau_social ); ?>" target="_blank" rel="noopener noreferrer">
            <?php if ( $icon && ! empty( $icon['url'] ) ) : ?>
              <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" aria-hidden="true" width="24" height="24">
            <?php endif; ?>
          </a>
        </li>
        <?php endwhile; ?>
      </ul>
      <?php endif; ?>
    </div>

    <?php if ( $logo_gb ) :
      $gb_w = ! empty( $logo_gb['width'] )  ? intval( $logo_gb['width'] )  : 200;
      $gb_h = ! empty( $logo_gb['height'] ) ? intval( $logo_gb['height'] ) : 80;
    ?>
    <div class="footer__gb">
      <img src="<?php echo esc_url( $logo_gb['url'] ); ?>" class="styleSvg" alt="<?php echo esc_attr( $logo_gb['alt'] ?? 'Grand Belfort' ); ?>" width="<?php echo $gb_w; ?>" height="<?php echo $gb_h; ?>"/>
    </div>
    <?php endif; ?>

    <small class="footer__copyright">© 2026 <a href="https://www.melinaterrier.fr/" target="_blank" rel="noopener noreferrer">Mélina Terrier</a> — Projet d'exercice pédagogique</small>
    
  </footer>
  
  <?php 
    wp_footer(); 
  ?>
</body>
</html>
