<?php

/**
 * Initialise les supports thème : balise <title>, miniatures, HTML5,
 * logo personnalisé, désactivation des sélecteurs Gutenberg de couleur/taille,
 * et déclaration des tailles d'images custom ('square' 1024×1024, 'paysage' 1024×680).
 *
 * Hooké sur `after_setup_theme` dans inc/actions.php.
 *
 * @return void
 */
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
  add_theme_support( 'editor-color-palette', array() );

  // permet la prise en charge des extraits.
  add_post_type_support( 'page', 'excerpt' );

  // Enregistre les tailles d'images personnalisées.
  add_image_size( 'square', 1024, 1024, true);
  add_image_size( 'paysage', 1024, 680, true);

}

/**
 * Enregistre les feuilles de style et scripts du thème.
 *
 * Utilise la version du thème (header style.css) pour le cache busting :
 * incrémenter "Version:" dans le header de style.css force la régénération
 * de l'URL côté navigateur après un déploiement.
 *
 * `wp_localize_script` expose les valeurs côté JS (ajaxURL, base, nonce)
 * via la variable globale `esgiValues` dans script.js.
 *
 * @return void
 */
function crdtheme_scripts_styles() {

	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'crd-style', get_stylesheet_uri(), array(), $theme_version );
	wp_enqueue_script( 'crd-vendors', get_template_directory_uri() . '/src/js/vendors.js', array(), $theme_version, true );
	wp_enqueue_script( 'crd-scripts', get_template_directory_uri() . '/src/js/script.js', array( 'crd-vendors' ), $theme_version, true );

}

/**
 * Déclare les emplacements de menu utilisés par le thème :
 * - primary-menu : menu principal du header
 * - footer-menu  : menu secondaire du footer
 *
 * L'utilisateur peut ensuite y associer un menu via Admin → Apparence → Menus.
 *
 * @return void
 */
function crdtheme_register_menus() {
  register_nav_menus( array(
    'primary-menu' => 'En-tête de page',
    'footer-menu' => 'Pied de page'
  ) );
}


/**
 * Décale automatiquement les événements passés vers le futur en ajoutant
 * des années jusqu'à ce que la date soit postérieure à maintenant.
 *
 * Utilité : ce site sert de portfolio/démo et doit rester "vivant" plusieurs
 * mois après livraison sans intervention manuelle. Les événements gardent
 * leur jour/mois/heure, seule l'année change.
 *
 * Précondition : le champ ACF `event_date` est stocké en base au format
 * MySQL DATETIME `Y-m-d H:i:s` (vérifié dans wp_postmeta). Si la config ACF
 * change pour stocker `d/m/Y G:i` ou autre, createFromFormat retourne false
 * et la fonction passe silencieusement à l'événement suivant sans rien shifter
 * et sans logger d'erreur. Régression invisible à surveiller.
 *
 * Performance : filtre `meta_query` >= now() côté SQL, donc ne traite que
 * les événements réellement passés. update_post_meta() direct (pas update_field)
 * pour éviter de charger ACF en contexte cron.
 *
 * Déclenchée par le cron quotidien `crdtheme_shift_events_cron` et par la
 * logique de rattrapage dans crdtheme_schedule_shift_events_cron().
 *
 * @return void
 */
function crdtheme_shift_past_events_to_future() {
  $now = current_time( 'Y-m-d H:i:s' );

  $past_events = get_posts( array(
    'post_type'      => 'agenda',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'meta_query'     => array(
      array(
        'key'     => 'event_date',
        'value'   => $now,
        'compare' => '<',
      ),
    ),
  ));

  if ( empty( $past_events ) ) {
    return;
  }

  $now_dt = new DateTimeImmutable( $now, wp_timezone() );

  foreach ( $past_events as $event_id ) {
    $stored = get_post_meta( $event_id, 'event_date', true );
    $event_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $stored, wp_timezone() );
    if ( ! $event_dt ) {
      continue;
    }
    while ( $event_dt < $now_dt ) {
      $event_dt = $event_dt->modify( '+1 year' );
    }
    update_post_meta( $event_id, 'event_date', $event_dt->format( 'Y-m-d H:i:s' ) );
  }
}

/**
 * Filtre la requête principale sur l'archive du CPT agenda et ses taxonomies
 * pour ne retourner que les événements futurs, triés par date croissante.
 *
 * Hooké sur `pre_get_posts`. Garde-fou : ! is_admin() évite d'altérer les
 * listes du back-office. is_main_query() évite d'altérer les requêtes
 * secondaires d'autres plugins.
 *
 * Précondition : le champ ACF `event_date` est stocké au format
 * `Y-m-d H:i:s`. Le tri et la comparaison >= sont des comparaisons de chaînes
 * MySQL — elles ne fonctionnent correctement que parce que `Y-m-d H:i:s` est
 * lexicographiquement triable. Avec `d/m/Y G:i`, le tri donnerait n'importe
 * quoi (le jour passerait avant l'année).
 *
 * @param WP_Query $query Instance de la requête en cours de construction.
 * @return WP_Query Instance modifiée (passage par référence).
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
