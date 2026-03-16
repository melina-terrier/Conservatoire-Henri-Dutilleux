<?php
/**
 * Template Name: Contact
*/

get_header();

while ( have_posts() ) : the_post();
	get_template_part( 'template-parts/hero' );
	$horaires    = get_field( 'contact_hours' );
	$sites_intro = get_field( 'contact_locations_intro' );
	$sites       = get_field( 'contact_locations' );
	$adresse     = get_field( 'site_address', 'infos' );
	$tel         = get_field( 'site_phone', 'infos' );
	$mail        = get_field( 'site_email', 'infos' );
	$acces_intro = get_field( 'contact_access_intro' );
	$acces       = get_field( 'contact_access_items' );
	$form_display = get_field( 'form_display' );
	$form_shortcode = get_field( 'form_shortcode' );
?>

<div class="main-column">
	<?php the_content(); ?>

	<div class="contactInfos">
		<div class="contactInfos__block">
			<h3>Siège principal</h3>
			<address>
				<?php if ( $adresse ) : ?>
					<?php echo esc_html( $adresse['street_number'] ); ?> <?php echo esc_html( $adresse['street_name'] ); ?><br>
					<?php echo esc_html( $adresse['post_code'] ); ?> <?php echo esc_html( $adresse['city'] ); ?><br>
				<?php endif; ?>
				<?php if ( $tel ) : ?>
					<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $tel ); ?></a><br>
				<?php endif; ?>
				<?php if ( $mail ) : ?>
					<a href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a>
				<?php endif; ?>
			</address>
		</div>

		<div class="contactInfos__block">
			<h3>Horaires d'accueil</h3>
			<p><?php echo nl2br( esc_html( $horaires ) ); ?></p>
		</div>
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
