<?php
get_header(); 
	
get_template_part( 'template-parts/hero' );
	
	if(have_posts()) : ?>
		<div id="ajax-response" class="grid">
			<?php	while(have_posts()) : the_post();
				get_template_part('template-parts/card', 'card');
			endwhile; ?>
		</div>
	
	<?php
		the_posts_pagination(); 
	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;

get_footer();