<?php

/*
 Ajoute les pages d'options ACF au menu d'administration.
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

/* Pour activer la carte Google map */
function google_map_api() {
  acf_update_setting('google_api_key', getenv('GOOGLE_MAPS_API_KEY'));
}