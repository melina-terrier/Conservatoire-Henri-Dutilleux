<?php
/**
 * Liste de fichiers à télécharger — blocs `.wp-block-file`.
 *
 * Factorise le markup partagé entre page-enseignements.php et content-page.php.
 *
 * @param array $args['files'] Repeater ACF : sous-champs `file_upload` (file array)
 *                             et `file_label` (text). Fallback label = nom du fichier.
 */
defined( 'ABSPATH' ) || exit;

$crdtheme_files = $args['files'] ?? array();
if ( ! $crdtheme_files ) {
	return;
}

foreach ( $crdtheme_files as $crdtheme_row ) :
	$crdtheme_file = $crdtheme_row['file_upload'] ?? null;
	if ( empty( $crdtheme_file['url'] ) ) {
		continue;
	}
	$crdtheme_label = ! empty( $crdtheme_row['file_label'] )
		? $crdtheme_row['file_label']
		: ( $crdtheme_file['filename'] ?? '' );
	?>
	<div class="wp-block-file">
		<a href="<?php echo esc_url( $crdtheme_file['url'] ); ?>">
			<?php echo esc_html( $crdtheme_label ); ?>
		</a>
		<a class="wp-block-file__button" href="<?php echo esc_url( $crdtheme_file['url'] ); ?>" download>
			Télécharger
		</a>
	</div>
	<?php
endforeach;
