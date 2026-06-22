<?php
defined( 'ABSPATH' ) || exit;

/**
 * Déclare les pages d'options ACF réservées aux administrateurs.
 *
 * Capability `manage_options` volontaire : les options globales (coordonnées,
 * logos, sous-menu enseignements, en-têtes archives) configurent l'identité
 * visuelle/structurelle du site sur toutes les pages — un éditeur ne doit pas
 * pouvoir les changer sans supervision admin.
 *
 * Note sécurité : `form_shortcode` (do_shortcode injecté dans page-contact.php)
 * n'est PAS sur ces pages d'options mais sur la page WP de contact directement.
 * Il est donc accessible à tout rôle disposant de `edit_pages`. Acceptable car
 * les éditeurs peuvent déjà injecter du shortcode arbitraire dans le contenu
 * d'une page via Gutenberg — pas de privilège supplémentaire ici.
 */
function crdtheme_acf_options_pages() {
	if( ! function_exists('acf_add_options_page') ) return;

	acf_add_options_page( array(
		'page_title'    => 'Infos générales',
		'menu_title'    => 'Options',
		'menu_slug'     => 'infos-site',
		'capability'    => 'manage_options',
		'position'      => 3,
		'icon_url'      => false,
		'redirect'      => false,
		'post_id'       => 'infos',
		'autoload'      => false,
		'update_button' => 'Mettre à jour',
	) );

	acf_add_options_sub_page(array(
		'page_title' 	=> 'En-têtes des pages archives',
		'menu_title'	=> 'En-têtes archives',
		'parent_slug'	=> 'infos-site',
		'capability'    => 'manage_options',
		'update_button' => 'Mettre à jour',
		'post_id' => 'header_archives',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Sous-menu enseignements',
		'menu_title'	=> 'Enseignements',
		'parent_slug'	=> 'infos-site',
		'capability'    => 'manage_options',
		'update_button' => 'Mettre à jour',
		'post_id' => 'header_enseignements',
	));
}

/**
 * Injecte la clé Google Maps API dans la configuration ACF pour activer
 * le rendu des champs de type "Google Map".
 */
function crdtheme_set_google_map_api_key() {
  acf_update_setting('google_api_key', GOOGLE_MAPS_API_KEY );
}