<?php
get_header(); 
	
get_template_part( 'template-parts/hero' );
	
	if(have_posts()) : ?>
		<div class="grid">
			<?php	while(have_posts()) : the_post();
				get_template_part( 'template-parts/card', 'card', array( 'heading' => 'h2' ) );
			endwhile; ?>
		</div>
	
	<?php
		the_posts_pagination(); 
	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;

get_footer();