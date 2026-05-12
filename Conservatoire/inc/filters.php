<?php

// Retire la balise p des descriptions de taxonomies
remove_filter('term_description', 'wpautop');

// Ne pas afficher la barre d'administration en front-end
add_filter('show_admin_bar', '__return_false');

/**
 * Autorise l'upload des fichiers SVG dans la médiathèque.
 *
 * Note sécurité : les SVG peuvent contenir du JS. Ce thème les utilise pour
 * des logos / icônes fournis par l'admin de confiance, pas pour des uploads
 * publics. Pour un site multi-auteurs, prévoir une sanitization côté plugin.
 *
 * @param array $mimes Types MIME autorisés par WordPress.
 * @return array $mimes enrichi du type image/svg+xml.
 */
function wpc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'wpc_mime_types');

// Sécurité : retirer la balise <meta name="generator"> exposant la version WordPress
remove_action( 'wp_head', 'wp_generator' );

// Performance : désactiver le chargement du script/style emoji de WordPress
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Sécurité : désactiver XML-RPC (vecteur d'attaque)
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Ajoute l'attribut `defer` aux scripts du thème côté front uniquement.
 *
 * Important : ne JAMAIS différer wp-hooks / wp-i18n / contact-form-7. Ces libs
 * sont consommées par des `<script>` inline non-deferred qui appellent `wp.i18n`
 * etc. — différer la lib les fait planter (TypeError sur `setLocaleData`).
 *
 * Garde admin : `is_admin()` empêche d'altérer les scripts de Gutenberg,
 * qui dépendent eux aussi de l'ordre de chargement de wp-i18n.
 *
 * @param string $tag    Balise <script> générée par WordPress.
 * @param string $handle Identifiant du script tel que déclaré via wp_enqueue_script().
 * @return string Balise modifiée ou inchangée.
 */
function crdtheme_defer_scripts( $tag, $handle ) {
    if ( is_admin() ) {
        return $tag;
    }
    $defer = array( 'crd-vendors', 'crd-scripts' );
    if ( in_array( $handle, $defer, true ) && false === strpos( $tag, ' defer' ) && false === strpos( $tag, ' async' ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'crdtheme_defer_scripts', 10, 2 );

/**
 * Force loading="lazy" + decoding="async" sur les images rendues via
 * wp_get_attachment_image() / get_the_post_thumbnail() / wp_get_attachment_image_attributes.
 *
 * Invariant (opt-out) : un appelant qui passe explicitement `loading` ou
 * `decoding` voit sa valeur préservée. Ce comportement est essentiel pour
 * le LCP : l'image hero appelle wp_get_attachment_image() avec
 * `loading => 'eager', fetchpriority => 'high'` (cf. template-parts/hero.php).
 * Si on remplace `! isset` par un override systématique, le LCP s'effondre.
 *
 * @param array $attr Attributs de la balise <img> avant rendu.
 * @return array $attr enrichi de loading/decoding s'ils n'étaient pas définis.
 */
function crdtheme_force_lazy_loading( $attr ) {
    if ( ! isset( $attr['loading'] ) ) {
        $attr['loading'] = 'lazy';
    }
    if ( ! isset( $attr['decoding'] ) ) {
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'crdtheme_force_lazy_loading' );

/**
 * Ajoute l'attribut HTML5 `required` natif aux champs obligatoires
 * de Contact Form 7.
 *
 * CF7 valide les champs marqués obligatoires (étoile *) côté serveur,
 * mais n'ajoute pas l'attribut `required` au HTML. Résultat : pas de
 * validation native du navigateur (l'utilisateur peut soumettre un
 * formulaire vide et n'a que le retour serveur après envoi).
 *
 * Ce filtre détecte la classe `wpcf7-validates-as-required` (que CF7
 * ajoute aux champs obligatoires) et injecte `required` dans la balise.
 *
 * @param string $content HTML du formulaire CF7 généré.
 * @return string $content enrichi avec l'attribut `required` natif.
 */
function crdtheme_cf7_native_required( $content ) {
    return preg_replace(
        '/<(input|textarea|select)([^>]*?)wpcf7-validates-as-required([^>]*?)>/',
        '<$1$2wpcf7-validates-as-required$3 required>',
        $content
    );
}
add_filter( 'wpcf7_form_elements', 'crdtheme_cf7_native_required' );

/**
 * Désactive le chargement de jquery-migrate sur le front.
 *
 * jquery-migrate sert à supporter les vieux plugins encore en jQuery 1.x.
 * Aucun script du thème ni des plugins activés n'en a besoin → on l'enlève
 * pour gagner ~10 Ko et une requête HTTP de moins.
 *
 * @param WP_Scripts $scripts Instance du registre des scripts WordPress.
 * @return void
 */
function crdtheme_remove_jquery_migrate( $scripts ) {
    if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
        $deps = $scripts->registered['jquery']->deps;
        $scripts->registered['jquery']->deps = array_diff( $deps, array( 'jquery-migrate' ) );
    }
}
add_action( 'wp_default_scripts', 'crdtheme_remove_jquery_migrate' );

