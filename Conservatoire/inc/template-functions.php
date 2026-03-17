<?php


function crdtheme_setup() {

  // permet aux plugins et aux thèmes de gérer la balise de titre du document.
  add_theme_support( 'title-tag' );

  // permet la prise en charge des images mises en avant.
  add_theme_support( 'post-thumbnails' );

  // permet de rendre le code valide pour HTML5.
  add_theme_support(
    'html5',
    array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
      'style',
      'script',
    )
  );

  /**
   * permet la prise en charge d'un logo personnalisé.
   */
  add_theme_support( 'custom-logo', array(
    'height'      => 250,
    'width'       => 250,
    'flex-width'  => true,
    'flex-height' => true,
  ) );

  // Désactive les tailles de police et couleurs pour Gutenberg
  add_theme_support( 'disable-custom-font-sizes' );
  add_theme_support( 'disable-custom-colors' );
  add_theme_support( 'editor-color-palette' );

  // permet la prise en charge des extraits.
  add_post_type_support( 'page', 'excerpt' );

  // Enregistre les tailles d'images personnalisées.
  add_image_size( 'square', 1024, 1024, true);
  add_image_size( 'paysage', 1024, 680, true);

}

/**
 * File d'attente des scripts et des styles.
 */
function crdtheme_scripts_styles() {

	wp_enqueue_style( 'crd-style', get_stylesheet_uri() );
	wp_enqueue_script( 'crd-vendors', get_template_directory_uri() . '/src/js/vendors.js', array(), '', true );
	wp_enqueue_script( 'crd-scripts', get_template_directory_uri() . '/src/js/script.js', array( 'crd-vendors' ), '', true );

	wp_localize_script( 'crd-scripts', 'esgiValues', array(
		'ajaxURL' => admin_url( 'admin-ajax.php' ),
		'base'    => get_pagenum_link( 1 ),
		'nonce'   => wp_create_nonce( 'crdtheme_load_posts' ),
	));

}

/**
 * Enregistre les emplacements du menu de navigation pour un thème.
 */
function crdtheme_register_menus() {
  register_nav_menus( array(
    'primary-menu' => 'En-tête de page',
    'footer-menu' => 'Pied de page'
  ) );
}


/**
 * Callback AJAX pour le chargement paginé des événements de l'agenda.
 */
function crdtheme_ajax_load_posts() {
  check_ajax_referer( 'crdtheme_load_posts', 'nonce' );

  $page = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
  $base = isset( $_GET['base'] ) ? esc_url_raw( $_GET['base'] ) : get_post_type_archive_link( 'agenda' );

  $today = wp_date('Y-m-d H:i:s');
  $args = array(
    'post_type'      => 'agenda',
    'posts_per_page' => 6,
    'paged'          => $page,
    'meta_key'       => 'event_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => array(
      array(
        'key'     => 'event_date',
        'value'   => $today,
        'compare' => '>=',
      ),
    ),
  );

  $query = new WP_Query( $args );

  ob_start();
  if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
      $query->the_post();
      get_template_part( 'template-parts/card', 'card' );
    }
    wp_reset_postdata();
  }
  $cards_html = ob_get_clean();

  $pagination = paginate_links( array(
    'base'      => trailingslashit( $base ) . '%_%',
    'format'    => 'page/%#%/',
    'current'   => $page,
    'total'     => $query->max_num_pages,
    'prev_text' => 'Précédent',
    'next_text' => 'Suivant',
  ));

  wp_send_json( array(
    'cards'      => $cards_html,
    'pagination' => $pagination ?: '',
  ));
}


/**
 * Personnalisation de la requête principale sur les archives et taxonomies du CPT agenda.
 */
function crdtheme_custom_query_vars( $query ) {

  if ( ! is_admin() && $query->is_main_query() ) {

    if ( is_post_type_archive( 'agenda' ) || is_tax( 'location' ) || is_tax( 'cat_agenda' ) ) {
      $today = wp_date('Y-m-d H:i:s');
      $query->set( 'posts_per_page', 6 );
      $query->set( 'meta_key', 'event_date' );
      $query->set( 'orderby', 'meta_value' );
      $query->set( 'order', 'ASC' );
      $query->set( 'meta_query', array(
        array(
          'key'     => 'event_date',
          'value'   => $today,
          'compare' => '>='
        )
      ));
    }

  }
  return $query;
}
