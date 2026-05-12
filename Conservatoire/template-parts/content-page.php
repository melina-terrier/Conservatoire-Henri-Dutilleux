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
		<div class="carousel">
		<?php foreach ( $carousel_images as $image ) : ?>
			<div class="carousel__item">
				<img src="<?php echo esc_url( $image['url'] ); ?>"
				     alt="<?php echo esc_attr( $image['alt'] ); ?>">
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if ( $files_display && $files ) : ?>
		<?php foreach ( $files as $file ) :
			$file_url = $file['file_upload']['url'] ?? '';
			if ( ! $file_url ) continue;
		?>
			<div class="wp-block-file">
				<a href="<?php echo esc_url( $file_url ); ?>">
					<?php echo esc_html( $file['file_label'] ?? '' ); ?>
				</a>
				<a class="wp-block-file__button" href="<?php echo esc_url( $file_url ); ?>" download>
					Télécharger
				</a>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php if ( $form_display && $form_shortcode ) : 
	echo do_shortcode( $form_shortcode );
endif;