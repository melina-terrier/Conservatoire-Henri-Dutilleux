<?php get_header(); ?>

<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
      get_template_part( 'template-parts/hero', 'hero' );

      if( have_rows('section') ):
        while( have_rows('section') ): the_row();
          // Section présentation CRD avec chiffres
          if( get_row_layout() == 'section_numbers' ):
            $chapo    = get_sub_field('numbers_subtitle');
            $ctaBtn   = get_sub_field('numbers_show_cta');
            $ctaTxt   = get_sub_field('numbers_cta_label');
            $ctaLink  = get_sub_field('numbers_cta_link');
            $image    = get_sub_field('numbers_image');
            $chiffres = 'numbers_items';
            ?>

            <section class="grid crd">
              <?php if ( $image ) : ?>
              <div class="crd__img">
                <?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?>
              </div>
              <?php endif; ?>
              <header class="crd__header">
                <?php if ( $chapo ) : ?>
                <h2 class="crd__title"><?php echo wp_kses_post( $chapo ); ?></h2>
                <?php endif; ?>
                <?php if ( $ctaBtn && $ctaLink ) : ?>
                  <a class="crd__link btn" href="<?php echo esc_url( $ctaLink ); ?>">
                    <svg class="btn__arrow" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M23.354 12.354a.5.5 0 000-.708l-3.182-3.182a.5.5 0 10-.707.708L22.293 12l-2.828 2.828a.5.5 0 10.707.708l3.182-3.182zM0 12.5h23v-1H0v1z" fill="#1C1514" />
                    </svg>
                    <?php echo esc_html( $ctaTxt ); ?>
                  </a>
                <?php endif; ?>
              </header>
              <?php if( have_rows( $chiffres ) ): ?>
                <div class="crd__stats">
                  <?php while( have_rows( $chiffres ) ) : the_row(); 
                    $chiffre = get_sub_field('number_value');
                    $label   = get_sub_field('number_label');
                  ?>
                    <p class="stat">
                      <span class="stat__chiffre"><?php echo esc_html( $chiffre ); ?></span>
                      <span class="stat__text"><?php echo esc_html( $label ); ?></span>
                    </p>
                  <?php endwhile; ?>
                </div>
              <?php endif;?>
              <div class="crd__bg"></div>
            </section>

            <?php 
              // Section type de contenu (Agenda ou sites)
              elseif( get_row_layout() == 'section_featured' ):
                $title   = get_sub_field('featured_title');
                $chapo   = get_sub_field('featured_subtitle');
                $ctaBtn  = get_sub_field('featured_show_cta');
                $ctaTxt  = get_sub_field('featured_cta_label');
                $ctaLink = get_sub_field('featured_cta_link');
                $cptBtn  = get_sub_field('featured_show_events');
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
              <?php if ( $ctaBtn && $ctaLink ) : ?>
              <a class="section__link btn" href="<?php echo esc_url( $ctaLink ); ?>">
                <svg class="btn__arrow" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M23.354 12.354a.5.5 0 000-.708l-3.182-3.182a.5.5 0 10-.707.708L22.293 12l-2.828 2.828a.5.5 0 10.707.708l3.182-3.182zM0 12.5h23v-1H0v1z" fill="#1C1514" />
                </svg>
                <?php echo esc_html( $ctaTxt ); ?>
              </a>
              <?php endif; ?>
              <?php if( $cptBtn ): ?>
              <?php
                $today = wp_date('Y-m-d H:i:s');
                $args = array(
                  'post_type' => 'agenda',
                  'posts_per_page' => 3,
                  'meta_key' => 'event_date',
                  'orderby' => 'meta_value',
                  'order' => 'ASC',
                  'meta_query' => array(
                    array(
                      'key' => 'event_date',
                      'value' => $today,
                      'compare' => '>='
                    )
                  )
                );
                $query = new WP_Query( $args );

                if($query->have_posts()) :
                  while($query->have_posts()) : $query->the_post(); 
                  get_template_part('template-parts/card', 'card');
                  endwhile;
                endif;
                wp_reset_postdata();
              ?>
              <?php endif; ?>
            </section>

            <?php 
              // Section inscription
              elseif( get_row_layout() == 'section_banner' ):
                $title   = get_sub_field('banner_title');
                $chapo   = get_sub_field('banner_subtitle');
                $ctaBtn  = get_sub_field('banner_show_cta');
                $ctaTxt  = get_sub_field('banner_cta_label');
                $ctaLink = get_sub_field('banner_cta_link');
                $image   = get_sub_field('banner_image');
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
              <?php if ( $ctaBtn && $ctaLink ) : ?>
              <a class="section__link btn -outlined" href="<?php echo esc_url( $ctaLink ); ?>">
                <svg class="btn__arrow" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M23.354 12.354a.5.5 0 000-.708l-3.182-3.182a.5.5 0 10-.707.708L22.293 12l-2.828 2.828a.5.5 0 10.707.708l3.182-3.182zM0 12.5h23v-1H0v1z" fill="#1C1514" />
                </svg>
                <?php echo esc_html( $ctaTxt ); ?>
              </a>
              <?php endif; ?>
              <?php if ( $image ) : ?>
              <div class="duotone section__img">
                <?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?>
              </div>
              <?php endif; ?>
            </section>
            
          <?php endif; ?>
        <?php endwhile; ?>
      <?php endif; ?>
    <?php endwhile; ?>
  <?php endif; ?>

<?php
get_footer();
