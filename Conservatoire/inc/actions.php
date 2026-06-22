<?php
defined( 'ABSPATH' ) || exit;

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
add_action('acf/init', 'crdtheme_set_google_map_api_key');

// Pages d'options ACF
add_action('acf/init', 'crdtheme_acf_options_pages');

// Enregistrement local des groupes de champs ACF
add_action('acf/init', 'crdtheme_register_acf_fields');

// Personnalisation de la requête principale pour les archives du CPT agenda.
add_action( 'pre_get_posts', 'crdtheme_custom_query_vars' );

// Retire jquery-migrate côté front : WordPress moderne et les plugins maintenus
// n'en ont plus besoin, c'est ~10Ko de JS inutilisé sur chaque page.
// On retire à la fois la dep `jquery-migrate` du handle `jquery` ET on déregistre
// le handle lui-même — sinon un plugin pourrait toujours l'enqueue explicitement.
add_action( 'wp_default_scripts', 'crdtheme_dequeue_jquery_migrate' );

function crdtheme_dequeue_jquery_migrate( $scripts ) {
  if ( is_admin() ) {
    return;
  }
  if ( ! empty( $scripts->registered['jquery'] ) ) {
    $jquery = $scripts->registered['jquery'];
    if ( ! empty( $jquery->deps ) ) {
      $jquery->deps = array_diff( $jquery->deps, array( 'jquery-migrate' ) );
    }
  }
  if ( isset( $scripts->registered['jquery-migrate'] ) ) {
    unset( $scripts->registered['jquery-migrate'] );
  }
}

// ─── Sécurité & performance : retraits de hooks par défaut WordPress ──────────
// Regroupés ici (plutôt que dispersés dans filters.php) car ce sont tous des
// `remove_action` — la convention du thème est : actions dans actions.php.

// Retire la balise <meta name="generator"> exposant la version WordPress (head + RSS).
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );           // EditURI / xmlrpc?rsd : inutile, signal pour scanners
remove_action( 'wp_head', 'wlwmanifest_link' );   // Windows Live Writer : obsolète

// Désactive le chargement du script/style emoji de WordPress (perf : ~3Ko inutiles).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Bloque l'énumération des utilisateurs via `?author=N` (vecteur historique
 * WP < 4.5). Une requête `?author=1` redirige normalement vers /author/{slug}/
 * et révèle ainsi le login. On intercepte et redirige vers home.
 */
add_action( 'template_redirect', 'crdtheme_block_author_enumeration' );
function crdtheme_block_author_enumeration() {
    if ( ! is_admin() && ! empty( $_GET['author'] ) && ! is_user_logged_in() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
}

/**
 * Injecte `rel="prev"` et `rel="next"` dans le <head> sur les archives
 * paginées (agenda, taxonomies, etc.). Améliore l'a11y des lecteurs d'écran
 * et de certains navigateurs (Firefox a un widget natif).
 *
 * Note : Google a déprécié l'usage SEO de rel=next/prev en 2019, mais le
 * standard HTML reste valide et utile pour les autres user-agents.
 */
add_action( 'wp_head', 'crdtheme_pagination_rel_links' );
function crdtheme_pagination_rel_links() {
    if ( ! is_archive() && ! is_post_type_archive() && ! is_search() ) {
        return;
    }
    global $wp_query;
    $current = max( 1, (int) get_query_var( 'paged' ) );
    $total   = (int) $wp_query->max_num_pages;
    if ( $current > 1 ) {
        printf( '<link rel="prev" href="%s">' . "\n", esc_url( get_pagenum_link( $current - 1 ) ) );
    }
    if ( $current < $total ) {
        printf( '<link rel="next" href="%s">' . "\n", esc_url( get_pagenum_link( $current + 1 ) ) );
    }
}

// Headers de sécurité (HSTS, COOP, CSP report-only, etc.) sur le front uniquement.
add_action( 'send_headers', 'crdtheme_security_headers' );

/**
 * Envoie les headers de sécurité côté front. Volontairement absent en admin
 * où WordPress et les plugins peuvent avoir besoin de plus de souplesse.
 *
 * CSP en mode report-only : le navigateur signale les violations sans bloquer.
 * Une fois les sources stables (≥ 1 semaine sans nouveau report), passer en
 * `Content-Security-Policy` (enforcement) — voir SEC1 dans audit-conservatoire.md.
 */
function crdtheme_security_headers() {
  if ( is_admin() ) {
    return;
  }

  // Masque la version PHP exposée par défaut (reconnaissance pour scans de CVE).
  // Idéalement aussi `expose_php = Off` côté php.ini en prod ; ceci couvre le cas
  // où la directive serveur n'est pas modifiable.
  header_remove( 'X-Powered-By' );

  // HSTS — préparer le preload (à soumettre sur hstspreload.org après vérification).
  header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );

  // Isole le contexte d'origine (anti Spectre, anti window.opener cross-origin).
  header( 'Cross-Origin-Opener-Policy: same-origin' );

  // Anti MIME sniffing + clickjacking + referrer leak.
  header( 'X-Content-Type-Options: nosniff' );
  header( 'X-Frame-Options: SAMEORIGIN' );
  header( 'Referrer-Policy: strict-origin-when-cross-origin' );
  header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );

  // CSP report-only — autorise 'unsafe-inline' parce que WordPress core
  // et de nombreux plugins inline du JS/CSS. Pour passer en enforcement strict,
  // basculer vers une stratégie nonce/hash (refacto important).
  //
  // ATTENTION : ce header est ÉMIS mais aucun `report-uri` n'est configuré.
  // Les violations ne sont donc collectées NULLE PART. À traiter en prod :
  //  - soit créer un endpoint gratuit sur https://report-uri.com et ajouter
  //    "; report-uri https://xxx.report-uri.com/r/d/csp/reportOnly" ci-dessous
  //  - soit retirer ce header tant qu'aucun endpoint n'est en place (éviter
  //    la "sécurité-théâtre" : un header visible dans `curl -I` sans valeur).
  $csp = "default-src 'self'; "
       . "script-src 'self' 'unsafe-inline' https://maps.googleapis.com https://maps.gstatic.com https://www.google.com https://www.gstatic.com https://cdn-cookieyes.com; "
       . "style-src 'self' 'unsafe-inline'; "
       . "img-src 'self' data: blob: https:; "
       . "font-src 'self' data:; "
       . "frame-src 'self' https://www.google.com https://maps.google.com; "
       . "connect-src 'self' https://maps.googleapis.com https://log.cookieyes.com https://cdn-cookieyes.com; "
       . "object-src 'none'; "
       . "base-uri 'self'; "
       . "form-action 'self';";
  header( 'Content-Security-Policy-Report-Only: ' . $csp );
}

/**
 * Détermine si la page courante affiche un formulaire CF7 — via les champs ACF
 * form_display/form_shortcode (modèles Contact et pages standard) OU un shortcode
 * [contact-form-7] dans le contenu Gutenberg.
 *
 * @return bool
 */
function crdtheme_current_page_has_form() {
  if ( ! is_singular() ) {
    return false;
  }
  $post = get_queried_object();
  if ( ! $post instanceof WP_Post ) {
    return false;
  }
  if ( function_exists( 'get_field' )
    && get_field( 'form_display', $post->ID )
    && get_field( 'form_shortcode', $post->ID ) ) {
    return true;
  }
  return has_shortcode( $post->post_content, 'contact-form-7' );
}

// Allège les pages sans formulaire : retire les scripts Contact Form 7 (CF7 core +
// son reCAPTCHA Google) là où aucun formulaire n'est réellement affiché.
// Gain perf (LCP / Total Blocking Time) + RGPD : zéro appel Google/CF7 sur l'accueil, l'agenda, etc.
add_action( 'wp_enqueue_scripts', 'crdtheme_lighten_non_form_pages', 100 );

function crdtheme_lighten_non_form_pages() {
  if ( is_admin() || crdtheme_current_page_has_form() ) {
    return;
  }
  // Retirer d'abord le script DÉPENDANT (wpcf7-recaptcha), sinon WordPress signale
  // une dépendance non enregistrée (doing_it_wrong) quand on déregistre google-recaptcha.
  wp_dequeue_script( 'wpcf7-recaptcha' );
  wp_deregister_script( 'wpcf7-recaptcha' );
  // Handle reCAPTCHA de CF7 (retire aussi son inline).
  wp_dequeue_script( 'google-recaptcha' );
  wp_deregister_script( 'google-recaptcha' );

  // CF7 charge ses scripts (+ wp-hooks / wp-i18n) sur TOUTES les pages : on les
  // retire là où aucun formulaire n'est affiché (gain LCP / TBT).
  wp_dequeue_script( 'contact-form-7' );
  wp_dequeue_script( 'swv' );
  wp_dequeue_style( 'contact-form-7' );
}

// Preconnect vers les domaines Google reCAPTCHA — uniquement sur les pages qui le
// chargent, pour économiser la latence de connexion initiale (~200 ms).
add_action( 'wp_head', 'crdtheme_recaptcha_preconnect', 1 );

function crdtheme_recaptcha_preconnect() {
  if ( ! crdtheme_current_page_has_form() ) {
    return;
  }
  echo '<link rel="preconnect" href="https://www.google.com" crossorigin>' . "\n";
  echo '<link rel="preconnect" href="https://www.gstatic.com" crossorigin>' . "\n";
}

// Cron quotidien pour décaler les événements passés vers le futur.
// On hook un wrapper qui met aussi à jour le marqueur de dernière exécution,
// pour que la logique de rattrapage ci-dessous ne re-déclenche pas inutilement.
add_action( 'crdtheme_shift_events_cron', 'crdtheme_run_shift_and_mark' );
add_action( 'init', 'crdtheme_schedule_shift_events_cron' );

/**
 * Wrapper du cron : exécute le décalage puis met à jour le timestamp
 * `crdtheme_events_last_shift`. Protégé par un transient lock pour éviter
 * qu'une exécution concurrente (cron + rattrapage async) ne double les updates.
 */
function crdtheme_run_shift_and_mark() {
  if ( get_transient( 'crdtheme_shift_lock' ) ) {
    return;
  }
  // Lock long (1h) : si la base grossit et que la boucle prend plusieurs minutes,
  // on ne risque pas qu'il expire pendant l'exécution et déclenche un second
  // décalage. `try/finally` garantit la libération même si une exception est
  // levée (DB down, plugin tiers qui throw sur update_post_meta, etc.).
  set_transient( 'crdtheme_shift_lock', 1, HOUR_IN_SECONDS );

  try {
    crdtheme_shift_past_events_to_future();
    update_option( 'crdtheme_events_last_shift', time(), false );
  } finally {
    delete_transient( 'crdtheme_shift_lock' );
  }
}

/**
 * Planifie le cron quotidien `crdtheme_shift_events_cron` et programme un
 * rattrapage asynchrone si la dernière exécution date de plus de 24h.
 *
 * Le rattrapage passe par wp_schedule_single_event (et non un appel synchrone)
 * pour ne pas bloquer la première requête après inactivité — sur un site peu
 * fréquenté, l'exécution synchrone pouvait timeout.
 */
function crdtheme_schedule_shift_events_cron() {
  if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
    return;
  }

  if ( ! wp_next_scheduled( 'crdtheme_shift_events_cron' ) ) {
    wp_schedule_event( time(), 'daily', 'crdtheme_shift_events_cron' );
  }

  $last_run = (int) get_option( 'crdtheme_events_last_shift', 0 );
  if ( ( time() - $last_run ) > DAY_IN_SECONDS && ! get_transient( 'crdtheme_schedule_pending' ) ) {
    // Petit lock anti-cascade : empêche plusieurs requêtes concurrentes de
    // programmer le même single event. Expire vite (10s) pour ne pas
    // interférer avec le lock du run lui-même (`crdtheme_shift_lock`).
    set_transient( 'crdtheme_schedule_pending', 1, 10 );
    wp_schedule_single_event( time(), 'crdtheme_shift_events_cron' );
  }
}
