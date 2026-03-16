<?php
/**
 * Template Name: Plan du site
*/

get_header();

while ( have_posts() ) : the_post();
	get_template_part( 'template-parts/hero' );
	?>

	<div class="main-column legalContent">
		<?php the_content(); ?>

		<?php if ( have_rows( 'sitemap_main_pages' ) ) : ?>
			<h2 class="sitemap__heading">Pages principales</h2>
			<div class="sitemapGrid">
				<?php while ( have_rows( 'sitemap_main_pages' ) ) : the_row();
					$title       = get_sub_field( 'sitemap_main_page_title' );
					$description = get_sub_field( 'sitemap_main_page_description' );
					$link        = get_sub_field( 'sitemap_main_page_link' );
				?>
				<div class="sitemapGrid__item">
					<?php if ( $link ) : ?>
						<h3 class="sitemapGrid__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
						<p class="sitemapGrid__desc"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<?php if ( have_rows( 'sitemap_legal_pages' ) ) : ?>
			<h2 class="sitemap__heading">Informations légales</h2>
			<div class="sitemapGrid">
				<?php while ( have_rows( 'sitemap_legal_pages' ) ) : the_row();
					$title       = get_sub_field( 'sitemap_legal_page_title' );
					$description = get_sub_field( 'sitemap_legal_page_description' );
					$link        = get_sub_field( 'sitemap_legal_page_link' );
				?>
				<div class="sitemapGrid__item">
					<?php if ( $link ) : ?>
						<h3 class="sitemapGrid__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
						<p class="sitemapGrid__desc"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
