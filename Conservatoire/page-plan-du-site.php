<?php
/**
 * Template Name: Plan du site
*/

get_header();

$sections = array(
	'sitemap_main_pages' => array(
		'heading' => 'Pages principales',
		'prefix'  => 'sitemap_main_page_',
	),
	'sitemap_legal_pages' => array(
		'heading' => 'Informations légales',
		'prefix'  => 'sitemap_legal_page_',
	),
);

while ( have_posts() ) : the_post();
	get_template_part( 'template-parts/hero' );
	?>

	<div class="mainColumn legalContent">
		<?php the_content(); ?>

		<?php foreach ( $sections as $rows_key => $cfg ) : ?>
			<?php if ( ! have_rows( $rows_key ) ) continue; ?>

			<h2 class="sitemap__heading"><?php echo esc_html( $cfg['heading'] ); ?></h2>
			<div class="sitemapGrid">
				<?php while ( have_rows( $rows_key ) ) : the_row();
					$title       = get_sub_field( $cfg['prefix'] . 'title' );
					$description = get_sub_field( $cfg['prefix'] . 'description' );
					$link        = get_sub_field( $cfg['prefix'] . 'link' );
					if ( ! $link ) continue;
				?>
				<div class="sitemapGrid__item">
					<h3 class="sitemapGrid__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
					<?php if ( $description ) : ?>
						<p class="sitemapGrid__desc"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
				<?php endwhile; ?>
			</div>
		<?php endforeach; ?>

		<?php
		// ─── Agenda : événements futurs ────────────────────────────────────
		// Le cron de rotation décale les events passés vers le futur, donc on
		// liste uniquement les events à venir. Décision actée : pas de séparation
		// Agenda / Archives — un seul concept, l'Agenda (aucun event « passé »).
		$events_query = crdtheme_get_upcoming_events( array( 'limit' => 50 ) );
		if ( $events_query->have_posts() ) : ?>
			<h2 class="sitemap__heading">Agenda</h2>
			<div class="sitemapGrid">
				<?php while ( $events_query->have_posts() ) : $events_query->the_post(); ?>
					<div class="sitemapGrid__item">
						<h3 class="sitemapGrid__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php $date_formatted = crdtheme_format_event_date(); ?>
						<?php if ( $date_formatted ) : ?>
							<p class="sitemapGrid__desc"><?php echo esc_html( $date_formatted ); ?></p>
						<?php endif; ?>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php endif; ?>

		<?php
		// ─── Catégories et lieux du CPT agenda ─────────────────────────────
		$taxonomies = array(
			'cat_agenda' => 'Catégories d\'événements',
			'location'   => 'Lieux',
		);
		foreach ( $taxonomies as $taxonomy => $label ) :
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
			) );
			if ( is_wp_error( $terms ) || empty( $terms ) ) continue;
			?>
			<h2 class="sitemap__heading"><?php echo esc_html( $label ); ?></h2>
			<div class="sitemapGrid">
				<?php foreach ( $terms as $term ) :
					$term_link = get_term_link( $term );
					if ( is_wp_error( $term_link ) ) continue;
				?>
					<div class="sitemapGrid__item">
						<h3 class="sitemapGrid__title"><a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
						<?php if ( $term->description ) : ?>
							<p class="sitemapGrid__desc"><?php echo esc_html( $term->description ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
