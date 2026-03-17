<?php
  $adresse    = get_field('site_address', 'infos');
  $tel        = get_field('site_phone', 'infos');
  $mail       = get_field('site_email', 'infos');
  $logoFooter = get_field('site_logo_footer', 'infos');
  $logoGB     = get_field('site_logo_partner', 'infos');
?>

	</main>
  
  <footer class="grid -withoutMargin footer">
    
    <?php if ( $logoFooter ) : ?>
    <div class="footer__logo">
      <img src="<?php echo esc_url( $logoFooter['url'] ); ?>" class="style-svg"/>
    </div>
    <?php endif; ?>

    <nav class="footer__menu">
      <h3 class="footer__title">Menu</h3>
      <?php wp_nav_menu( array(
        'theme_location' => 'footer-menu',
        'container'      => false,
        'menu_class'     => 'listUnstyled',
      ) ); ?>
    </nav>
    
    <div class="footer__contact">
      <h3 class="footer__title">Contact</h3>
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
      <h3 class="footer__title">Nous suivre</h3>
      <ul class="footer__list listUnstyled">
        <?php while(have_rows('site_social_networks', 'infos')): the_row();
          $reseau_social = get_sub_field('social_network_name');
          $url = get_sub_field('social_network_url');
        ?>
        <li>
          <a href="<?php echo esc_url( $url ); ?>" aria-label="Notre page <?php echo esc_attr( $reseau_social ); ?>" target="_blank">
            <i class="icon<?php echo esc_attr( $reseau_social ); ?>" aria-hidden="true"></i>
          </a>
        </li>
        <?php endwhile; ?>
      </ul>
      <?php endif; ?>
    </div>

    <?php if ( $logoGB ) : ?>
    <div class="footer__gb">
      <img src="<?php echo esc_url( $logoGB['url'] ); ?>" class="style-svg"/>
    </div>
    <?php endif; ?>

    <small class="footer__copyright">© 2026 <a href="https://www.melinaterrier.fr/" target="_blank">Mélina Terrier</a> — Projet d'exercice pédagogique</small>
    
  </footer>
  
  <?php 
    wp_footer(); 
  ?>
</body>
</html>
