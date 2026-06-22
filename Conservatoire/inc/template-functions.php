<?php
defined( 'ABSPATH' ) || exit;

/**
 * Initialise les supports thème : balise <title>, miniatures, HTML5,
 * logo personnalisé, désactivation des sélecteurs Gutenberg de couleur/taille,
 * et déclaration des tailles d'images custom ('square' 1024×1024, 'paysage' 1024×680).
 */
function crdtheme_setup() {

  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );

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

  add_theme_support( 'custom-logo', array(
    'height'      => 250,
    'width'       => 250,
    'flex-width'  => true,
    'flex-height' => true,
  ) );

  add_theme_support( 'disable-custom-font-sizes' );
  add_theme_support( 'disable-custom-colors' );
  add_theme_support( 'editor-color-palette', array() );

  add_post_type_support( 'page', 'excerpt' );

  add_image_size( 'square', 1024, 1024, true);
  add_image_size( 'paysage', 1024, 680, true);

}

/**
 * Enregistre les feuilles de style et scripts du thème.
 * Utilise la version du thème (header style.css) pour le cache.
 *
 * Sert les versions `.min.js` si elles existent (générées par `npm run build`),
 * sinon fallback sur les versions non minifiées. Permet de développer sans
 * étape de build et de déployer en prod avec minification automatique.
 */
function crdtheme_scripts_styles() {

	$theme_version = wp_get_theme()->get( 'Version' );
	$theme_dir     = get_template_directory();
	$theme_uri     = get_template_directory_uri();

	// Helper de résolution dist/ (minifié) → src/ (fallback) + cache-busting filemtime.
	// On préfère `filemtime()` à la version du thème : le header `Version: 1.0.0` de
	// style.css ne bouge jamais, alors que `.htaccess` cache 1 an `immutable`.
	$asset = function ( $name ) use ( $theme_dir, $theme_uri, $theme_version ) {
		$min = $theme_dir . '/dist/js/' . $name . '.min.js';
		if ( file_exists( $min ) ) {
			return array( $theme_uri . '/dist/js/' . $name . '.min.js', (string) filemtime( $min ) );
		}
		return array( $theme_uri . '/src/js/' . $name . '.js', $theme_version );
	};

	$style_path = $theme_dir . '/style.css';
	$style_ver  = file_exists( $style_path ) ? (string) filemtime( $style_path ) : $theme_version;
	wp_enqueue_style( 'crd-style', get_stylesheet_uri(), array(), $style_ver );

	// vendors-core (Headroom + Rellax) : nécessaire partout (header sticky).
	list( $core_src, $core_ver ) = $asset( 'vendors-core' );
	wp_enqueue_script( 'crd-vendors-core', $core_src, array(), $core_ver, true );

	// vendors-anim (GSAP + Flickity, ~90 % du poids JS) : chargé uniquement là où
	// les motifs animés ou un carousel sont réellement présents — voir
	// crdtheme_needs_anim_libs(). Évite ~40 Ko gzip sur contact, enseignements,
	// archives, recherche et 404.
	$scripts_deps = array( 'crd-vendors-core' );
	if ( crdtheme_needs_anim_libs() ) {
		list( $anim_src, $anim_ver ) = $asset( 'vendors-anim' );
		wp_enqueue_script( 'crd-vendors-anim', $anim_src, array(), $anim_ver, true );
		$scripts_deps[] = 'crd-vendors-anim';
	}

	list( $script_src, $script_ver ) = $asset( 'script' );
	wp_enqueue_script( 'crd-scripts', $script_src, $scripts_deps, $script_ver, true );

}

/**
 * Détermine si la page courante a besoin des libs d'animation (GSAP + Flickity).
 *
 * - Motifs SVG animés (GSAP + Rellax via template-parts/pattern.php) : front-page
 *   et fiches d'événements (single-agenda) uniquement.
 * - Carousel Flickity : single-agenda + pages génériques avec `carousel_display`.
 *
 * Les modèles dédiés (Contact, Enseignements, Plan du site), les archives, la
 * recherche et la 404 n'en ont jamais besoin.
 *
 * @return bool
 */
function crdtheme_needs_anim_libs() {
	if ( is_front_page() || is_singular( 'agenda' ) ) {
		return true;
	}
	if ( is_page() && function_exists( 'get_field' )
		&& get_field( 'carousel_display', get_queried_object_id() ) ) {
		return true;
	}
	return false;
}

/**
 * Déclare les emplacements de menu utilisés par le thème :
 * - primary-menu : menu principal du header
 * - footer-menu  : menu secondaire du footer
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
 * Précondition : ne lit que les events `publish` du CPT agenda dont la meta
 * `event_date` est < maintenant.
 * Invariant : chaque event décalé conserve son ID, son post_modified ne bouge
 * pas (update_post_meta direct), seule la meta `event_date` change.
 *
 * Perfo : on traite par batch de 200 (cf. `CRDTHEME_SHIFT_BATCH_SIZE`) pour
 * ne pas dépasser le `max_execution_time` PHP-FPM (typiquement 30-60s sur OVH
 * mutualisé). Si plus de 200 events à décaler, le prochain run quotidien
 * traitera la suite. update_meta_cache() warm le cache en 1 requête.
 */
function crdtheme_shift_past_events_to_future() {
  $now = current_time( 'mysql' );

  $past_events = get_posts( array(
    'post_type'      => 'agenda',
    'post_status'    => 'publish',
    'posts_per_page' => 200,
    'fields'         => 'ids',
    'no_found_rows'  => true,
    'update_post_term_cache' => false,
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

  // Warm le cache des post_meta en une seule requête (sinon get_post_meta
  // déclenche une lecture DB par event).
  update_meta_cache( 'post', $past_events );

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
 * Retourne les informations globales du site (coordonnées, logo, réseaux sociaux)
 * lues depuis la page d'options ACF `infos`. Mémoïsé pour la durée de la requête
 * afin d'éviter les lectures répétées dans header.php / footer.php / page-contact.php.
 *
 * Renvoie un tableau vide si ACF n'est pas chargé — les templates peuvent ainsi
 * appeler la fonction sans guard `function_exists('get_field')` à chaque endroit.
 */
function crdtheme_get_site_infos() {
  static $cache = null;
  if ( null !== $cache ) {
    return $cache;
  }

  if ( ! function_exists( 'get_field' ) ) {
    $cache = array();
    return $cache;
  }

  $cache = array(
    'address'        => get_field( 'site_address',     'infos' ),
    'phone'          => get_field( 'site_phone',       'infos' ),
    'email'          => get_field( 'site_email',       'infos' ),
    'logo_footer'    => get_field( 'site_logo_footer', 'infos' ),
    'logo_partner'   => get_field( 'site_logo_partner','infos' ),
  );
  return $cache;
}

/**
 * Construit le tableau d'arguments WP_Query pour les prochains événements.
 *
 * Fonction PURE : aucune I/O (DB, WP API), aucun side effect — entièrement
 * testable unitairement. Le `now` est injecté pour que les tests puissent
 * passer une date déterministe au lieu de dépendre de `current_time()`.
 *
 * Garde anti-piège WP : `posts_per_page = 0` est interprété par WP_Query
 * comme "utiliser l'option `posts_per_page` du site" (souvent 10), pas
 * comme "0 post". On force `max(1, …)`.
 *
 * @param array $args {
 *   @type int    $limit   Nombre d'events. Défaut 3. Min forcé à 1.
 *   @type int[]  $exclude IDs à exclure (coerced en int[]).
 *   @type string $now     Date MySQL `Y-m-d H:i:s` pour le meta_query `>=`.
 *                         Injectable pour tests. Défaut : `''`.
 * }
 * @return array Tableau d'args prêt pour `new WP_Query()`.
 */
function crdtheme_build_upcoming_events_args( $args = array() ) {
  $defaults = array(
    'limit'   => 3,
    'exclude' => array(),
    'now'     => '',
  );
  $args = array_merge( $defaults, (array) $args );

  return array(
    'post_type'              => 'agenda',
    'posts_per_page'         => max( 1, (int) $args['limit'] ),
    'post__not_in'           => array_map( 'intval', (array) $args['exclude'] ),
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
    'meta_key'               => 'event_date',
    'orderby'                => 'meta_value',
    'order'                  => 'ASC',
    'meta_query'             => array(
      array(
        'key'     => 'event_date',
        'value'   => (string) $args['now'],
        'compare' => '>=',
      ),
    ),
  );
}

/**
 * Retourne une WP_Query des prochains événements (event_date >= maintenant),
 * triés par date croissante. Wrapper qui injecte `current_time('mysql')` dans
 * la fonction pure `crdtheme_build_upcoming_events_args()`.
 *
 * @param array $args Voir crdtheme_build_upcoming_events_args().
 * @return WP_Query
 */
function crdtheme_get_upcoming_events( $args = array() ) {
  $args = (array) $args;
  $args['now'] = current_time( 'mysql' );
  return new WP_Query( crdtheme_build_upcoming_events_args( $args ) );
}

/**
 * Formate la meta ACF `event_date` du post courant (ou d'un $post_id donné)
 * selon le format date+heure de WP. Retourne une string vide si la meta est
 * absente ou invalide.
 *
 * Le champ ACF `date_time_picker` stocke en `Y-m-d H:i:s` — c'est le seul
 * format reconnu ici. Les valeurs legacy `d/m/Y G:i` ne sont volontairement
 * pas gérées : le cron de rotation ne les décalerait pas non plus, ce qui
 * créerait une incohérence d'affichage.
 *
 * @param int|null $post_id ID du post (défaut : post courant).
 * @return string Date formatée ou '' si impossible à parser.
 */
function crdtheme_format_event_date( $post_id = null ) {
  if ( ! function_exists( 'get_field' ) ) {
    return '';
  }
  $date = get_field( 'event_date', $post_id );
  if ( empty( $date ) ) {
    return '';
  }

  $dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $date, wp_timezone() );
  if ( ! $dt ) {
    return '';
  }

  $format = get_option( 'date_format' ) . ' à ' . get_option( 'time_format' );
  return wp_date( $format, $dt->getTimestamp() );
}

/**
 * Génère le JSON-LD schema.org "Event" pour l'event courant. À appeler dans
 * `single-agenda.php` (dans le `<head>` via `wp_head`, ou inline avant la
 * fermeture du `</article>`). Retourne une string `<script>` ou '' si données
 * insuffisantes.
 *
 * Aide les moteurs (Google, Bing) à afficher des rich results sur les events
 * (date, lieu, image). Pas indispensable au rendu utilisateur mais gros impact SEO.
 *
 * @param int|null $post_id ID du post (défaut : post courant).
 * @return string `<script type="application/ld+json">…</script>` ou ''.
 */
function crdtheme_event_schema_jsonld( $post_id = null ) {
  if ( ! function_exists( 'get_field' ) ) {
    return '';
  }
  $post_id = $post_id ?: get_the_ID();
  if ( ! $post_id ) {
    return '';
  }
  $date = get_field( 'event_date', $post_id );
  if ( empty( $date ) ) {
    return '';
  }
  $dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $date, wp_timezone() );
  if ( ! $dt ) {
    return '';
  }

  $infos    = crdtheme_get_site_infos();
  $location_terms = get_the_terms( $post_id, 'location' );
  $location_name  = ( $location_terms && ! is_wp_error( $location_terms ) ) ? $location_terms[0]->name : 'Conservatoire Henri Dutilleux';
  $address  = $infos['address'] ?? null;

  $data = array(
    '@context'    => 'https://schema.org',
    '@type'       => 'Event',
    'name'        => wp_strip_all_tags( get_the_title( $post_id ) ),
    'startDate'   => $dt->format( DateTimeInterface::ATOM ),
    'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
    'url'         => get_permalink( $post_id ),
    'eventStatus'           => 'https://schema.org/EventScheduled',
    'eventAttendanceMode'   => 'https://schema.org/OfflineEventAttendanceMode',
    'organizer'   => array(
      '@type' => 'Organization',
      'name'  => get_bloginfo( 'name' ),
      'url'   => home_url( '/' ),
    ),
    'location'    => array(
      '@type'   => 'Place',
      'name'    => $location_name,
      'address' => $address ? array(
        '@type'           => 'PostalAddress',
        'streetAddress'   => trim( ( $address['street_number'] ?? '' ) . ' ' . ( $address['street_name'] ?? '' ) ),
        'postalCode'      => $address['post_code'] ?? '',
        'addressLocality' => $address['city'] ?? '',
        'addressCountry'  => 'FR',
      ) : null,
    ),
  );

  $thumb_id = get_post_thumbnail_id( $post_id );
  if ( $thumb_id ) {
    $img_url = wp_get_attachment_image_url( $thumb_id, 'large' );
    if ( $img_url ) {
      $data['image'] = $img_url;
    }
  }

  return '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}

/**
 * Filtre la requête principale sur l'archive du CPT agenda et ses taxonomies
 * pour ne retourner que les événements futurs, triés par date croissante.
 */
function crdtheme_custom_query_vars( $query ) {

  if ( ! is_admin() && $query->is_main_query() ) {

    if ( is_post_type_archive( 'agenda' ) || is_tax( 'location' ) || is_tax( 'cat_agenda' ) ) {
      $today = current_time( 'mysql' );
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
