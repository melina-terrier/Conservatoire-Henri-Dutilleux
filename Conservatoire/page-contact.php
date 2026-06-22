<?php
/**
 * Template Name: Contact
*/

get_header();

while ( have_posts() ) : the_post();
	get_template_part( 'template-parts/hero' );
	$horaires       = get_field( 'contact_hours' );
	$sites_intro    = get_field( 'contact_locations_intro' );
	$sites          = get_field( 'contact_locations' );
	$acces_intro    = get_field( 'contact_access_intro' );
	$acces          = get_field( 'contact_access_items' );
	$form_display   = get_field( 'form_display' );
	$form_shortcode = get_field( 'form_shortcode' );
?>

<div class="mainColumn">
	<?php the_content(); ?>

	<div class="contactInfos">
		<div class="contactInfos__block">
			<h2>Siège principal</h2>
			<?php get_template_part( 'template-parts/contact-block' ); ?>
		</div>

		<?php if ( $horaires ) : ?>
		<div class="contactInfos__block">
			<h2>Horaires d'accueil</h2>
			<p><?php echo nl2br( esc_html( $horaires ) ); ?></p>
		</div>
		<?php endif; ?>
	</div>

	<h2>Nos sites</h2>
	<p><?php echo esc_html( $sites_intro )?></p>

	<?php if ( $sites ) : ?>
	<div class="sitesList">
		<?php foreach ( $sites as $site ) : ?>
		<div class="sitesList__item">
			<h3><?php echo esc_html( $site['location_name'] ); ?></h3>
			<address><?php echo esc_html( $site['location_address'] ); ?></address>
			<?php if ( ! empty( $site['location_disciplines'] ) ) : ?>
			<p>Disciplines : <?php echo esc_html( $site['location_disciplines'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>

	<?php endif; ?>

	<h2>Accès</h2>
	<p><?php echo esc_html( $acces_intro ); ?>
	</p>

	<?php if ( $acces ) : ?>
		<ul>
			<?php foreach ( $acces as $a ) : ?>
				<li>
					<?php if ( $a['access_type'] ) : ?>
						<strong><?php echo esc_html( $a['access_type'] ); ?> :</strong>
					<?php endif; ?>
					<?php echo esc_html( $a['access_detail'] ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>

<?php if ( $form_display && $form_shortcode ) : 
	echo do_shortcode( $form_shortcode );
endif;
endwhile; ?>

<?php get_footer(); ?>
