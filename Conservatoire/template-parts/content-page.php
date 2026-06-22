<?php get_template_part( 'template-parts/hero' ) ?>

<?php 
	$carousel_display = get_field( 'carousel_display' );
	$carousel_images = get_field( 'carousel_images' );

	$form_display = get_field( 'form_display' );
	$form_shortcode = get_field( 'form_shortcode' );
	
	$legal_page = get_field( 'legal_page' );

	$files_display = get_field( 'files_display' );
	$files = get_field( 'files' );
?>

<div class="mainColumn <?php if ( $legal_page ) echo 'legalContent'; ?>">
	<?php the_content();

	if ( $carousel_display && $carousel_images ) : ?>
		<div class="carousel" role="group" aria-roledescription="carrousel" aria-label="Galerie d'images">
		<?php foreach ( $carousel_images as $i => $image ) : ?>
			<div class="carousel__item">
				<?php
				// wp_get_attachment_image : srcset + width/height + decoding async auto.
				// 1re slide visible d'emblée : eager ; les suivantes lazy.
				echo wp_get_attachment_image(
					$image['ID'],
					'large',
					false,
					array(
						'alt'      => $image['alt'] ?? '',
						'loading'  => 0 === $i ? 'eager' : 'lazy',
						'decoding' => 'async',
					)
				);
				?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if ( $files_display && $files ) {
		get_template_part( 'template-parts/file-list', null, array( 'files' => $files ) );
	} ?>
</div>

<?php if ( $form_display && $form_shortcode ) : 
	echo do_shortcode( $form_shortcode );
endif;