<?php
/**
 * Template Name: Enseignements
*/

get_header();

while ( have_posts() ) : the_post();
	get_template_part( 'template-parts/hero' );
	$disciplines = get_field( 'teaching_disciplines' );
	$ems_titre   = get_field( 'teaching_ems_title' ) ?: 'Enseignement musical scolaire (EMS)';
	$ems_texte   = get_field( 'teaching_ems_content' );
	$cha_titre   = get_field( 'teaching_cha_title' ) ?: 'Classes à horaires aménagés (CHA)';
	$cha_texte   = get_field( 'teaching_cha_content' );
	$fichiers    = get_field( 'teaching_files' );
?>

	<div class="main-column">
		<?php the_content(); ?>

		<?php if ( $disciplines ) :
			foreach ( $disciplines as $disc ) :
				$titre     = esc_html( $disc['discipline_title'] );
				$intro     = $disc['discipline_intro'] ?? '';
				$cycles    = $disc['discipline_cycles'] ?? [];
				$image     = $disc['discipline_image'] ?? null;
				$legende   = $disc['discipline_image_caption'] ?? '';
				$pratiques = $disc['discipline_collective_practices'] ?? [];
			?>

			<div class="disciplineSection">
				<p class="disciplineSection__tag">Discipline</p>
				<h2><?php echo $titre; ?></h2>
			</div>

			<?php if ( $intro ) echo esc_html( $intro ); ?>

			<?php
			$instruments = $disc['discipline_instruments'] ?? [];
			if ( $instruments ) : ?>
				<h3>Instruments enseignés</h3>
				<div class="instrumentsGrid">
					<?php foreach ( $instruments as $ins ) : ?>
						<div class="instrumentsGrid__family">
							<p class="instrumentsGrid__name"><?php echo esc_html( $ins['instrument_family'] ); ?></p>
							<p class="instrumentsGrid__list"><?php echo esc_html( $ins['instrument_list'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $cycles ) : ?>
				<div class="cyclesTable-wrapper">
				<table class="cyclesTable">
					<thead>
						<tr>
							<th>Cycle</th>
							<th>Âge / Niveau</th>
							<th>Contenu</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $cycles as $c ) : ?>
							<tr>
								<td><?php echo esc_html( $c['cycle_name'] ); ?></td>
								<td><?php echo esc_html( $c['cycle_age_level'] ); ?></td>
								<td><?php echo esc_html( $c['cycle_content'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				</div>
			<?php endif; ?>

			<?php if ( $image ) : ?>
				<figure>
					<img src="<?php echo esc_url( $image['url'] ); ?>"
						alt="<?php echo esc_attr( $image['alt'] ?: $titre ); ?>">
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
							<li><?php echo esc_html( $p['practice_name'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

		<?php
			endforeach;
		else : ?>

			<div class="disciplineSection" id="musique">
				<p class="disciplineSection__tag">Discipline</p>
				<h2>Musique</h2>
			</div>
			<p>Contenu à remplir dans WP Admin → Pages → Enseignements → champs ACF.</p>

			<div class="disciplineSection" id="danse">
				<p class="disciplineSection__tag">Discipline</p>
				<h2>Danse contemporaine</h2>
			</div>
			<p>Contenu à remplir dans WP Admin → Pages → Enseignements → champs ACF.</p>

			<div class="disciplineSection" id="theatre">
				<p class="disciplineSection__tag">Discipline</p>
				<h2>Théâtre</h2>
			</div>
			<p>Contenu à remplir dans WP Admin → Pages → Enseignements → champs ACF.</p>

		<?php endif; ?>

		<?php if ( $ems_texte ) : ?>
			<div class="infoBlock">
				<h2><?php echo esc_html( $ems_titre ); ?></h2>
				<?php echo esc_html( $ems_texte ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $cha_texte ) : ?>
			<div class="infoBlock">
				<h2><?php echo esc_html( $cha_titre ); ?></h2>
				<?php echo esc_html( $cha_texte ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $fichiers ) :
			foreach ( $fichiers as $f ) :
				$file = $f['file_upload'];
				if ( ! $file ) continue;
		?>
			<div class="wp-block-file">
				<a href="<?php echo esc_url( $file['url'] ); ?>">
					<?php echo esc_html( $f['file_label'] ?: $file['filename'] ); ?>
				</a>
				<a class="wp-block-file__button" href="<?php echo esc_url( $file['url'] ); ?>" download>
					Télécharger
				</a>
			</div>
		<?php
			endforeach;
		endif; ?>

	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
