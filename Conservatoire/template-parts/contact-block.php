<?php
defined( 'ABSPATH' ) || exit;
/**
 * Bloc <address> contenant les coordonnées du site (rue, CP, ville, tél, mail).
 * Lit les options ACF via crdtheme_get_site_infos() — donc safe si ACF désactivé
 * (renvoie un tableau vide, les `if` ci-dessous filtrent).
 *
 * @param string $args['address_class'] Classe CSS optionnelle sur l'élément <address>.
 */

$infos    = crdtheme_get_site_infos();
$adresse  = $infos['address'] ?? null;
$tel      = $infos['phone']   ?? null;
$mail     = $infos['email']   ?? null;
$class    = isset( $args['address_class'] ) ? ' class="' . esc_attr( $args['address_class'] ) . '"' : '';
?>
<address<?php echo $class; ?>>
	<?php if ( $adresse ) : ?>
		<?php echo esc_html( $adresse['street_number'] ?? '' ); ?> <?php echo esc_html( $adresse['street_name'] ?? '' ); ?><br>
		<?php echo esc_html( $adresse['post_code'] ?? '' ); ?> <?php echo esc_html( $adresse['city'] ?? '' ); ?><br>
	<?php endif; ?>
	<?php if ( $tel ) : ?>
		<a href="tel:<?php echo esc_attr( preg_replace( '/^0/', '+33', preg_replace( '/[^\d+]/', '', $tel ) ) ); ?>"><?php echo esc_html( $tel ); ?></a><br>
	<?php endif; ?>
	<?php if ( $mail ) : ?>
		<a href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a>
	<?php endif; ?>
</address>
