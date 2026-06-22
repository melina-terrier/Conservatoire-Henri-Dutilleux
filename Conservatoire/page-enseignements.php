<?php
/**
 * Template Name: Enseignements
*/

get_header();

while ( have_posts() ) : the_post();
	get_template_part( 'template-parts/hero', null, array( 'show_disciplines_menu' => true ) );
	$disciplines = get_field( 'teaching_disciplines' );
	$ems_titre   = get_field( 'teaching_ems_title' );
	$ems_texte   = get_field( 'teaching_ems_content' );
	$cha_titre   = get_field( 'teaching_cha_title' );
	$cha_texte   = get_field( 'teaching_cha_content' );
	$fichiers    = get_field( 'teaching_files' );
?>

	<div class="mainColumn">
		<?php the_content(); ?>

		<?php if ( $disciplines ) :
			foreach ( $disciplines as $disc ) :
				$titre     = $disc['discipline_title'] ?? '';
				$intro     = $disc['discipline_intro'] ?? '';
				$cycles    = $disc['discipline_cycles'] ?? [];
				$image     = $disc['discipline_image'] ?? null;
				$legende   = $disc['discipline_image_caption'] ?? '';
				$pratiques = $disc['discipline_collective_practices'] ?? [];
			?>

			<div class="disciplineSection">
				<p class="disciplineSection__tag">Discipline</p>
				<h2><?php echo esc_html( $titre ); ?></h2>
			</div>

			<?php if ( $intro ) echo wp_kses_post( $intro ); ?>

			<?php
			$instruments = $disc['discipline_instruments'] ?? [];
			if ( $instruments ) : ?>
				<h3>Instruments enseignés</h3>
				<div class="instrumentsGrid">
					<?php foreach ( $instruments as $ins ) : ?>
						<div class="instrumentsGrid__family">
							<p class="instrumentsGrid__name"><?php echo esc_html( $ins['instrument_family'] ?? '' ); ?></p>
							<p class="instrumentsGrid__list"><?php echo esc_html( $ins['instrument_list'] ?? '' ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $cycles ) : ?>
				<div class="cyclesTable__wrapper">
				<table class="cyclesTable">
					<caption class="sr-only">Cycles d'enseignement — <?php echo esc_html( $titre ); ?></caption>
					<thead>
						<tr>
							<th scope="col">Cycle</th>
							<th scope="col">Âge / Niveau</th>
							<th scope="col">Contenu</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $cycles as $c ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $c['cycle_name'] ?? '' ); ?></th>
								<td><?php echo esc_html( $c['cycle_age_level'] ?? '' ); ?></td>
								<td><?php echo esc_html( $c['cycle_content'] ?? '' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				</div>
			<?php endif; ?>

			<?php if ( $image && ! empty( $image['ID'] ) ) : ?>
				<figure>
					<?php
					// Taille 'large' + srcset auto (évite de servir l'original ~380 Ko sur mobile).
					echo wp_get_attachment_image(
						$image['ID'],
						'large',
						false,
						array(
							'alt'      => ! empty( $image['alt'] ) ? $image['alt'] : $titre,
							'loading'  => 'lazy',
							'decoding' => 'async',
						)
					);
					?>
					<?php if ( $legende ) : ?>
						<figcaption><?php echo esc_html( $legende ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>

			<?php if ( $pratiques ) : ?>
				<div class="collectifBlock">
					<h3>Pratiques collectives</h3>
					<ul>
						<?php foreach ( $pratiques as $p ) : ?>
							<li><?php echo esc_html( $p['practice_name'] ?? '' ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

		<?php
			endforeach;
		endif; ?>

		<?php if ( $ems_texte ) : ?>
			<div class="infoBlock">
				<h2><?php echo esc_html( $ems_titre ); ?></h2>
				<?php echo wp_kses_post( $ems_texte ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $cha_texte ) : ?>
			<div class="infoBlock">
				<h2><?php echo esc_html( $cha_titre ); ?></h2>
				<?php echo wp_kses_post( $cha_texte ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $fichiers ) {
			get_template_part( 'template-parts/file-list', null, array( 'files' => $fichiers ) );
		} ?>

	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
