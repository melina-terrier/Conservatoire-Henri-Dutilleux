<article class="searchResult">

	<div class="searchResult__type">
		<?php
			$post_type_obj = get_post_type_object( get_post_type() );
			echo esc_html( $post_type_obj->labels->singular_name ); ?>
	</div>
	<?php the_title( sprintf( '<h2 class="searchResult__title"><a href="%s" class="searchResult__titleLink" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	<p class="searchResult__chapo"><?php the_excerpt(); ?></p>
	<a class="searchResult__link morelink" href="<?php echo esc_url( get_permalink() ); ?>">Plus d'info</a>

</article>
