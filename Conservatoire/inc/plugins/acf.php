<?php

/**
 * Déclare les pages d'options ACF accessibles aux éditeurs (capability 'edit_posts').
 *
 * - 'infos-site' : page parente "Infos générales" (post_id 'infos')
 *   contient adresse, téléphone, email, réseaux sociaux, logos du footer.
 * - Sous-page 'header_archives' : en-têtes des pages d'archive (image + chapo
 *   selon le type d'archive).
 * - Sous-page 'header_enseignements' : items du sous-menu Enseignements
 *   (utilisé sur la home et la page Enseignements via show_disciplines_menu).
 *
 * Garde-fou : sortie silencieuse si ACF Pro n'est pas chargé.
 *
 * @return void
 */
function crdtheme_acf_options_pages() {
	if( ! function_exists('acf_add_options_page') ) return;

	acf_add_options_page([
		'page_title' => 'Infos générales',
		'menu_title' => 'Options',
		'menu_slug' => 'infos-site',
		'capability' => 'edit_posts',
		'position' => 3,
		'icon_url' => false,
		'redirect' => false,
		'post_id' => 'infos',
		'autoload' => false,
		'update_button' => 'Mettre à jour',
	]);

	acf_add_options_sub_page(array(
		'page_title' 	=> 'En-têtes des pages archives',
		'menu_title'	=> 'En-têtes archives',
		'parent_slug'	=> 'infos-site',
		'update_button' => 'Mettre à jour',
		'post_id' => 'header_archives',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Sous-menu enseignements',
		'menu_title'	=> 'Enseignements',
		'parent_slug'	=> 'infos-site',
		'update_button' => 'Mettre à jour',
		'post_id' => 'header_enseignements',
	));
}

/**
 * Injecte la clé Google Maps API dans la configuration ACF pour activer
 * le rendu des champs de type "Google Map".
 *
 * Précondition : la constante GOOGLE_MAPS_API_KEY DOIT être définie avant
 * que ce hook se déclenche (sinon : "Undefined constant" → erreur fatale
 * qui casse tout WordPress, comme constaté en début de projet). Elle est
 * actuellement déclarée dans functions.php tout en haut, à partir de la
 * variable d'env Docker GOOGLE_MAPS_API_KEY (cf. .env). Ne pas déplacer
 * ce define dans un fichier inclus après acf/init.
 *
 * Hooké sur 'acf/init' dans inc/actions.php.
 *
 * @return void
 */
function google_map_api() {
  acf_update_setting('google_api_key', GOOGLE_MAPS_API_KEY );
}