<?php

/**
 * Enregistrement du Custom Post Type : agenda
 */
function crdtheme_register_cpt_agenda() {

	$labels = array(
		'name'               => __( 'Agenda', 'crdtheme' ),
		'singular_name'      => __( 'Événement', 'crdtheme' ),
		'add_new'            => __( 'Ajouter un événement', 'crdtheme' ),
		'add_new_item'       => __( 'Ajouter un événement', 'crdtheme' ),
		'edit_item'          => __( 'Modifier l\'événement', 'crdtheme' ),
		'new_item'           => __( 'Nouvel événement', 'crdtheme' ),
		'view_item'          => __( 'Voir l\'événement', 'crdtheme' ),
		'search_items'       => __( 'Rechercher un événement', 'crdtheme' ),
		'not_found'          => __( 'Aucun événement trouvé', 'crdtheme' ),
		'not_found_in_trash' => __( 'Aucun événement dans la corbeille', 'crdtheme' ),
		'menu_name'          => __( 'Agenda', 'crdtheme' ),
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
		'name'              => __( 'Catégories', 'crdtheme' ),
		'singular_name'     => __( 'Catégorie', 'crdtheme' ),
		'search_items'      => __( 'Rechercher une catégorie', 'crdtheme' ),
		'all_items'         => __( 'Toutes les catégories', 'crdtheme' ),
		'edit_item'         => __( 'Modifier la catégorie', 'crdtheme' ),
		'update_item'       => __( 'Mettre à jour la catégorie', 'crdtheme' ),
		'add_new_item'      => __( 'Ajouter une catégorie', 'crdtheme' ),
		'new_item_name'     => __( 'Nouvelle catégorie', 'crdtheme' ),
		'menu_name'         => __( 'Catégories', 'crdtheme' ),
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
		'name'              => __( 'Lieux', 'crdtheme' ),
		'singular_name'     => __( 'Lieu', 'crdtheme' ),
		'search_items'      => __( 'Rechercher un lieu', 'crdtheme' ),
		'all_items'         => __( 'Tous les lieux', 'crdtheme' ),
		'edit_item'         => __( 'Modifier le lieu', 'crdtheme' ),
		'update_item'       => __( 'Mettre à jour le lieu', 'crdtheme' ),
		'add_new_item'      => __( 'Ajouter un lieu', 'crdtheme' ),
		'new_item_name'     => __( 'Nouveau lieu', 'crdtheme' ),
		'menu_name'         => __( 'Lieux', 'crdtheme' ),
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
