<?php get_header(); ?>

<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/hero' );

		// Contenu flexible ACF : chaque layout est rendu par un partial dédié
		// template-parts/section-{numbers,featured,banner}.php. get_sub_field()
		// y fonctionne car the_row() positionne le pointeur de ligne global ACF.
		if ( have_rows( 'section' ) ) :
			while ( have_rows( 'section' ) ) :
				the_row();
				$crdtheme_layout = str_replace( 'section_', '', get_row_layout() );
				get_template_part( 'template-parts/section', $crdtheme_layout );
			endwhile;
		endif;

	endwhile;
endif;

get_footer();
