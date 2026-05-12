<?php

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>

		<div class="grid">
			<div class="duotone single__img">
				<?php the_post_thumbnail( 'square', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
			</div>

			<header class="single__header">

				<?php the_title( '<h1 class="single__title">', '</h1>' ); ?>

				<?php
				$date = get_field_object('event_date');
				$date_formatted = '';
				if ( $date && ! empty( $date['value'] ) ) {
					$timestamp = DateTime::createFromFormat( 'Y-m-d H:i:s', $date['value'] )
					          ?: DateTime::createFromFormat( 'd/m/Y G:i', $date['value'] );
					if ( $timestamp ) {
						$date_format = get_option( 'date_format' ) . ' à ' . get_option( 'time_format' );
						$date_formatted = wp_date( $date_format, $timestamp->getTimestamp() );
					}
				}
				?>

				<ul class="infos">
					<?php if ( $date && ( $date_formatted || $date['value'] ) ) : ?>
					<li class="infos__row">
						<span class="infos__label"><?php echo esc_html( $date['label'] ); ?></span>
						<span class="infos__value"><?php echo esc_html( $date_formatted ?: $date['value'] ); ?></span>
					</li>
					<?php endif; ?>
				<?php
					$taxonomies = array( 'location', 'cat_agenda' );
					foreach ( $taxonomies as $taxonomy ) :
					$taxonomy_object = get_taxonomy( $taxonomy );
					$terms = get_the_terms( get_the_ID(), $taxonomy );

					if ( $terms && $taxonomy_object ) : ?>
						<li class="infos__row">
							<span class="infos__label"><?php echo esc_html( $taxonomy_object->label ); ?></span>
							<span class="infos__value">
								<?php foreach ( $terms as $term ) :
								$term_link = get_term_link( $term ); ?>
								<a class="infos__link" href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term->name ); ?></a>
								<?php endforeach; ?>
							</span>
						</li>
					<?php endif; ?>
					<?php endforeach; ?>
				</ul>

			</header>
		</div>

		<p class="single__excerpt">
			<?php echo esc_html( get_the_excerpt() ); ?>
		</p>

		<div class="mainColumn">

			<?php the_content(); ?>

			<?php
			// Programme 
			$programme = get_field( 'event_program' );
			if ( $programme ) : ?>
				<h2>Programme</h2>
				<ul class="programmeList">
					<?php foreach ( $programme as $item ) : ?>
						<li class="programmeList__item">
							<?php if ( ! empty( $item['program_time'] ) ) : ?>
								<span class="programmeList__time">
									<?php echo esc_html( $item['program_time'] ); ?>
								</span>
							<?php endif; ?>
							<div class="programmeList__content">
								<?php if ( ! empty( $item['program_title'] ) ) : ?>
									<strong><?php echo esc_html( $item['program_title'] ); ?></strong>
								<?php endif; ?>
								<?php if ( ! empty( $item['program_detail'] ) ) : ?>
									<span><?php echo esc_html( $item['program_detail'] ); ?></span>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php
			// Bouton retour à l'agenda
			$agenda_url = get_post_type_archive_link( 'agenda' ) ?: home_url( '/agenda' );
			?>
			<a class="btnBack" href="<?php echo esc_url( $agenda_url ); ?>">
				<svg width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M15 8H1M1 8l6-6M1 8l6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				Retour à l'agenda
			</a>

			<?php
			$images = get_field( 'event_gallery' );
			if ( $images ) : ?>
			<div class="carousel">
				<?php foreach ( $images as $image ) : ?>
					<div class="carousel__item">
						<?php echo wp_get_attachment_image( $image['ID'], 'large' ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

		<?php
		$related = new WP_Query( array(
			'post_type'      => 'agenda',
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'meta_key'       => 'event_date',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => 'event_date',
					'value'   => current_time( 'Y-m-d H:i:s' ),
					'compare' => '>=',
				),
			),
		) );

		if ( $related->have_posts() ) : ?>
		<aside class="grid -withHeader section">
			<header class="section__header -full">
				<h2>Vous aimerez également</h2>
			</header>
			<?php while ( $related->have_posts() ) : $related->the_post(); ?>
				<?php get_template_part( 'template-parts/card' ); ?>
			<?php endwhile; wp_reset_postdata(); ?>
		</aside>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/pattern', null, array( 'wrapper_class' => 'single__patterns' ) ); ?>

		<?php
	endwhile;
endif;

get_footer();