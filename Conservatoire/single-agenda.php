<?php

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>

		<div class="grid">
			<div class="duotone single__img">
				<?php the_post_thumbnail( 'square' ); ?>
			</div>

			<header class="single__header">

				<?php the_title( '<h1 class="single__title">', '</h1>' ); ?>

				<?php
				$date = get_field_object('event_date');
				$date_formatted = '';
				if ( $date && $date['value'] ) {
					$timestamp = DateTime::createFromFormat('d/m/Y G:i', $date['value']);
					if ( $timestamp ) {
						$date_formatted = date_i18n('j F \à G\hi', $timestamp->getTimestamp());
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
					$taxonomyObject = get_taxonomy( $taxonomy );
					$terms = get_the_terms( get_the_ID(), $taxonomy );

					if ( $terms && $taxonomyObject ) : ?>
						<li class="infos__row">
							<span class="infos__label"><?php echo esc_html( $taxonomyObject->label ); ?></span>
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

		<div class="main-column">

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
						<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
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
			'orderby'        => 'date',
			'order'          => 'DESC',
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

		<div class="single__patterns rellax" data-rellax-speed="-5">
			<svg viewBox="0 0 1260 700" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g class="curveLines">
					<path d="M714.82,146.75c-45.82,0-83-40.88-83-91.32A97.76,97.76,0,0,1,644.1,7.66" />
					<path d="M983.41,153.39a32.38,32.38,0,1,0,0-64.75h-37" />
					<path d="M826.91,15.58H976.29c56.85,0,102.94,46.28,102.94,103.36S1033.14,222.3,976.29,222.3H931.34" />
					<path d="M928.58,334H1016c50.33,0,92.48,39.34,103.49,92.16" />
					<path d="M847.66,174.15a26.57,26.57,0,1,0-26.56-26.57A26.56,26.56,0,0,0,847.66,174.15Z" />
					<path d="M847.66,222.3c42.17,0,76.36-33.64,76.36-75.13S889.83,72,847.66,72s-76.36,33.64-76.36,75.14" />
					<path d="M165.4,185.77a74.7,74.7,0,1,1-149.4,0" />
					<path d="M798.69,223.13H725.58A83.8,83.8,0,0,0,641.82,307" />
					<path d="M871.31,384.19a32.38,32.38,0,1,0,32.79,32.38V384.19" />
					<path d="M1071.76,537.57c0,46.43,38.08,84.06,85.07,84.06s85.08-37.63,85.08-84.06V447.29" />
					<path d="M1027.45,576.8h-4.22a43.59,43.59,0,1,1,0-87.17H1156a43.59,43.59,0,1,1,0,87.17h-1.3" />
					<path d="M946.43,384.19H1000A64.93,64.93,0,0,1,1065.11,449" />
					<path d="M418.62,338.11a34.07,34.07,0,1,1-34.07-34.45H494.08" />
					<path d="M505.7,226.45H469.75a47.19,47.19,0,0,0-47.05,47.32" />
					<path d="M537.65,190.75V303.66" />
					<path d="M743.91,375.18v10.95a28.12,28.12,0,0,1-56.24,0v-9.07a27.91,27.91,0,0,0-55.81,0v11.16" />
					<path d="M820.68,685.55a34,34,0,1,0-34.44-34A34.25,34.25,0,0,0,820.68,685.55Z" />
					<path d="M536.8,96.94q0,12.45.05,24.91" />
					<path d="M536.8,49.62q0,12.45.05,24.91" />
					<path d="M809.87,493q0,12.45,0,24.9" />
					<path d="M868.8,493q0,12.45,0,24.9" />
					<path d="M927.73,493q0,12.45,0,24.9" />
				</g>
			</svg>
		</div>

		<?php
	endwhile;
endif;

get_footer();