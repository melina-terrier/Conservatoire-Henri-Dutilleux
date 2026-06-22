<?php
defined( 'ABSPATH' ) || exit;

/**
 * Enregistre le Custom Post Type "agenda" représentant les événements
 * du Conservatoire (concerts, portes ouvertes, spectacles, etc.).
 *
 * Supporte : title, editor, thumbnail, excerpt, custom-fields (pour ACF).
 * Visible publiquement, archive activée avec slug /agenda/, single via
 * single-agenda.php.
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
		'view_items'         => 'Voir les événements',
		'search_items'       => 'Rechercher un événement',
		'not_found'          => 'Aucun événement trouvé',
		'not_found_in_trash' => 'Aucun événement dans la corbeille',
		'menu_name'          => 'Agenda',
		'all_items'          => 'Tous les événements',
		// Label du lien d'archive (menus WP). Volontairement « Agenda » et non
		// « Archives » : la rotation auto des events (crdtheme_shift_events_cron)
		// recycle les events passés vers le futur, il n'existe donc pas d'archive
		// d'événements révolus. Décision : un seul concept, l'Agenda.
		'archives'           => 'Agenda',
		'attributes'         => 'Attributs de l\'événement',
		'item_published'     => 'Événement publié.',
		'item_updated'       => 'Événement mis à jour.',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'agenda', 'with_front' => false ),
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
 * Enregistre la taxonomie hiérarchique "cat_agenda" sur le CPT agenda.
 */
function crdtheme_register_tax_cat_agenda() {

	$labels = array(
		'name'              => 'Catégories',
		'singular_name'     => 'Catégorie',
		'search_items'      => 'Rechercher une catégorie',
		'all_items'         => 'Toutes les catégories',
		'parent_item'       => 'Catégorie parente',
		'parent_item_colon' => 'Catégorie parente :',
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
		'rewrite'           => array( 'slug' => 'categorie-agenda', 'with_front' => false ),
	);

	register_taxonomy( 'cat_agenda', array( 'agenda' ), $args );
}

/**
 * Enregistre la taxonomie hiérarchique "location" sur le CPT agenda.
 */
function crdtheme_register_tax_location() {

	$labels = array(
		'name'                       => 'Lieux',
		'singular_name'              => 'Lieu',
		'search_items'               => 'Rechercher un lieu',
		'popular_items'              => 'Lieux les plus utilisés',
		'all_items'                  => 'Tous les lieux',
		'edit_item'                  => 'Modifier le lieu',
		'update_item'                => 'Mettre à jour le lieu',
		'add_new_item'               => 'Ajouter un lieu',
		'new_item_name'              => 'Nouveau lieu',
		'separate_items_with_commas' => 'Séparer les lieux par une virgule',
		'add_or_remove_items'        => 'Ajouter ou retirer des lieux',
		'menu_name'                  => 'Lieux',
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'lieu', 'with_front' => false ),
	);

	register_taxonomy( 'location', array( 'agenda' ), $args );
}
