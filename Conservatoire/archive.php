<?php
get_header();

// JSON-LD ItemList des événements de l'archive agenda (aide Google à comprendre
// la liste ; chaque single porte déjà son schema Event complet).
if ( is_post_type_archive( 'agenda' ) && have_posts() ) {
	global $wp_query;
	$crdtheme_list = array();
	foreach ( $wp_query->posts as $crdtheme_i => $crdtheme_event ) {
		$crdtheme_start = get_post_meta( $crdtheme_event->ID, 'event_date', true );
		$crdtheme_iso   = '';
		if ( $crdtheme_start ) {
			try {
				$crdtheme_iso = ( new DateTime( $crdtheme_start, wp_timezone() ) )->format( 'c' );
			} catch ( Exception $e ) {
				$crdtheme_iso = '';
			}
		}
		$crdtheme_list[] = array(
			'@type'    => 'ListItem',
			'position' => $crdtheme_i + 1,
			'item'     => array_filter( array(
				'@type'     => 'Event',
				'name'      => get_the_title( $crdtheme_event ),
				'url'       => get_permalink( $crdtheme_event ),
				'startDate' => $crdtheme_iso,
			) ),
		);
	}
	if ( $crdtheme_list ) {
		echo '<script type="application/ld+json">'
			. wp_json_encode( array(
				'@context'        => 'https://schema.org',
				'@type'           => 'ItemList',
				'itemListElement' => $crdtheme_list,
			) )
			. '</script>' . "\n";
	}
}

get_template_part( 'template-parts/hero' );
	
	if(have_posts()) : ?>
		<div class="grid">
			<?php	while(have_posts()) : the_post();
				get_template_part( 'template-parts/card', null, array( 'heading' => 'h2' ) );
			endwhile; ?>
		</div>
	
	<?php
		global $wp_query;
		$total_pages = (int) $wp_query->max_num_pages;
		$current     = max( 1, (int) get_query_var( 'paged' ) );
		if ( $total_pages > 1 ) :
			?>
			<p class="pageInfo" aria-live="polite">
				Page <?php echo esc_html( $current ); ?> sur <?php echo esc_html( $total_pages ); ?>
			</p>
			<?php
		endif;
		the_posts_pagination( array(
			'aria_label'         => 'Pagination des événements',
			'screen_reader_text' => 'Navigation entre les pages',
		) );
	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;

get_footer();