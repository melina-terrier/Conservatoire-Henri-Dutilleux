<?php

// Initialisation des fonctions personnalisées du thème.
add_action( 'after_setup_theme', 'crdtheme_setup' );

// Register the menu locations.
add_action( 'init', 'crdtheme_register_menus' );

// Enregistrement du CPT agenda et de ses taxonomies.
add_action( 'init', 'crdtheme_register_cpt_agenda' );
add_action( 'init', 'crdtheme_register_tax_cat_agenda' );
add_action( 'init', 'crdtheme_register_tax_location' );

// File d'attente des styles et des scripts
add_action( 'wp_enqueue_scripts', 'crdtheme_scripts_styles' );

// Pour activer la carte Google map
add_action('acf/init', 'google_map_api');

// Pages d'options ACF
add_action('acf/init', 'crdtheme_acf_options_pages');

// Personnalisation de la requête principale pour les archives du CPT agenda.
add_action( 'pre_get_posts', 'crdtheme_custom_query_vars' );

// Ajax pour le chargement des posts sur les archives du CPT agenda.
add_action( 'wp_ajax_loadPosts', 'crdtheme_ajax_load_posts' );
add_action( 'wp_ajax_nopriv_loadPosts', 'crdtheme_ajax_load_posts' );