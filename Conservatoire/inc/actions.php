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

// Enregistrement local des groupes de champs ACF
add_action('acf/init', 'crdtheme_register_acf_fields');

// Personnalisation de la requête principale pour les archives du CPT agenda.
add_action( 'pre_get_posts', 'crdtheme_custom_query_vars' );

// Cron quotidien pour décaler les événements passés vers le futur.
// On hook un wrapper qui met aussi à jour le marqueur de dernière exécution,
// pour que la logique de rattrapage ci-dessous ne re-déclenche pas inutilement.
add_action( 'crdtheme_shift_events_cron', 'crdtheme_run_shift_and_mark' );
add_action( 'init', 'crdtheme_schedule_shift_events_cron' );

/**
 * Wrapper du cron : exécute le décalage puis met à jour le timestamp
 * `crdtheme_events_last_shift`, ce qui empêche la logique de rattrapage
 * dans crdtheme_schedule_shift_events_cron() de relancer la tâche.
 *
 * Invariant : le timestamp `crdtheme_events_last_shift` reflète le dernier
 * shift RÉUSSI. Si crdtheme_shift_past_events_to_future() lève une exception
 * (peu probable mais possible si la DB tombe), update_option() ne sera pas
 * appelé et le rattrapage relancera la tâche au prochain hit. C'est voulu —
 * ne pas inverser l'ordre des deux appels.
 *
 * @return void
 */
function crdtheme_run_shift_and_mark() {
  crdtheme_shift_past_events_to_future();
  update_option( 'crdtheme_events_last_shift', time(), false );
}

/**
 * Planifie le cron quotidien `crdtheme_shift_events_cron` et déclenche un
 * rattrapage immédiat si la dernière exécution date de plus de 24 h.
 *
 * Le rattrapage est utile en local où le site n'est pas visité chaque jour,
 * donc WP-Cron (event-driven) ne se déclenche pas naturellement.
 *
 * Garde : on saute les requêtes AJAX / REST / cron pour ne pas rejouer la
 * logique de rattrapage à chaque appel asynchrone (économie d'I/O DB).
 *
 * @return void
 */
function crdtheme_schedule_shift_events_cron() {
  // Ne rien faire pendant les requêtes AJAX, REST, cron : on n'a pas besoin
  // de rejouer la logique de rattrapage à chaque chargement asynchrone.
  if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
    return;
  }

  if ( ! wp_next_scheduled( 'crdtheme_shift_events_cron' ) ) {
    wp_schedule_event( time(), 'daily', 'crdtheme_shift_events_cron' );
  }

  // Rattrapage : si la dernière exécution date de plus de 24h, on relance immédiatement.
  $last_run = (int) get_option( 'crdtheme_events_last_shift', 0 );
  if ( ( time() - $last_run ) > DAY_IN_SECONDS ) {
    crdtheme_run_shift_and_mark();
  }
}