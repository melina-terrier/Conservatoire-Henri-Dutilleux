<?php

/**
 * Enregistrement du Custom Post Type : agenda
 */
function crdtheme_register_cpt_agenda() {

	$labels = array(
		'name'               => 'Agenda',
		'singular_name'      => 'Événement',
		'add_new'            => 'Ajouter un événement',
		'add_new_item'       => 'Ajouter un événement',
		'edit_item'          => 'Modifier l\'événement',
		'new_item'           => 'Nouvel événement',
		'view_item'          => 'Voir l\'événement',
		'search_items'       => 'Rechercher un événement',
		'not_found'          => 'Aucun événement trouvé',
		'not_found_in_trash' => 'Aucun événement dans la corbeille',
		'menu_name'          => 'Agenda',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'agenda' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-calendar-alt',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
	);

	register_post_type( 'agenda', $args );
}

/**
 * Enregistrement de la taxonomie : cat_agenda (catégorie d'événement)
 */
function crdtheme_register_tax_cat_agenda() {

	$labels = array(
		'name'              => 'Catégories',
		'singular_name'     => 'Catégorie',
		'search_items'      => 'Rechercher une catégorie',
		'all_items'         => 'Toutes les catégories',
		'edit_item'         => 'Modifier la catégorie',
		'update_item'       => 'Mettre à jour la catégorie',
		'add_new_item'      => 'Ajouter une catégorie',
		'new_item_name'     => 'Nouvelle catégorie',
		'menu_name'         => 'Catégories',
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'categorie-agenda' ),
	);

	register_taxonomy( 'cat_agenda', array( 'agenda' ), $args );
}

/**
 * Enregistrement de la taxonomie : location (lieu de l'événement)
 */
function crdtheme_register_tax_location() {

	$labels = array(
		'name'              => 'Lieux',
		'singular_name'     => 'Lieu',
		'search_items'      => 'Rechercher un lieu',
		'all_items'         => 'Tous les lieux',
		'edit_item'         => 'Modifier le lieu',
		'update_item'       => 'Mettre à jour le lieu',
		'add_new_item'      => 'Ajouter un lieu',
		'new_item_name'     => 'Nouveau lieu',
		'menu_name'         => 'Lieux',
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'lieu' ),
	);

	register_taxonomy( 'location', array( 'agenda' ), $args );
}
