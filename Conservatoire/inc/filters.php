<?php
defined( 'ABSPATH' ) || exit;

// Retire la balise p des descriptions de taxonomies
remove_filter('term_description', 'wpautop');

// Ne pas afficher la barre d'administration en front-end
add_filter('show_admin_bar', '__return_false');

/**
 * Autorise l'upload des fichiers SVG dans la médiathèque, mais uniquement
 * pour les administrateurs. Un SVG peut contenir du JavaScript exécuté au
 * rendu (XSS stocké via la médiathèque), donc on restreint l'autorisation
 * ET on filtre le contenu à l'upload via crdtheme_sanitize_svg_upload().
 *
 * Pour un sanitization plus robuste, envisager le plugin "safe-svg"
 * (enshrined/svg-sanitize). Le filtre ci-dessous est une défense en
 * profondeur minimale, pas un sanitizer complet.
 */
function crdtheme_mime_types($mimes) {
    if ( current_user_can('manage_options') ) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter('upload_mimes', 'crdtheme_mime_types');

/**
 * Nettoie le contenu d'un SVG à l'upload : retire <script>, <foreignObject>,
 * les attributs `on*` (onload, onclick, etc.) et les URI `javascript:`.
 * Refuse l'upload si l'utilisateur n'a pas la capability requise.
 */
function crdtheme_sanitize_svg_upload( $file ) {
    if ( empty( $file['tmp_name'] ) || empty( $file['type'] ) ) {
        return $file;
    }
    if ( 'image/svg+xml' !== $file['type'] ) {
        return $file;
    }
    if ( ! current_user_can('manage_options') ) {
        $file['error'] = 'Vous n’avez pas l’autorisation d’uploader des fichiers SVG.';
        return $file;
    }

    $content = file_get_contents( $file['tmp_name'] );
    if ( false === $content ) {
        return $file;
    }

    $patterns = array(
        '#<script\b[^>]*>.*?</script>#is',
        '#<foreignObject\b[^>]*>.*?</foreignObject>#is',
        '#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i',
        '#(?:xlink:)?href\s*=\s*("javascript:[^"]*"|\'javascript:[^\']*\')#i',
    );
    $sanitized = preg_replace( $patterns, '', $content );

    if ( null !== $sanitized && $sanitized !== $content ) {
        file_put_contents( $file['tmp_name'], $sanitized );
    }

    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'crdtheme_sanitize_svg_upload' );

// Sécurité : masquer la version WordPress (head, RSS, Atom, RDF).
// Note : le `remove_action( 'wp_head', 'wp_generator' )` correspondant est dans actions.php.
add_filter( 'the_generator', '__return_empty_string' );

// Performance : désactiver le chargement emoji côté contenus (RSS, mails).
// Les `remove_action` correspondants (head, admin) sont dans actions.php.
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Sécurité : désactiver XML-RPC (vecteur d'attaque). Le filtre ci-dessous
// désactive la fonctionnalité au niveau PHP. Pour aussi renvoyer un 403 HTTP et
// ne pas exposer le endpoint au scan, ajouter le blocage <Files "xmlrpc.php"> au
// .htaccess RACINE WordPress (cf. docs/htaccess-root.example) — pas dans le thème.
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Empêche l'énumération des utilisateurs via /wp-json/wp/v2/users.
 * Sans ce filtre, un visiteur anonyme peut récupérer le login admin
 * (id=1, slug, etc.) puis brute-forcer le mot de passe.
 *
 * On désactive l'endpoint UNIQUEMENT pour les requêtes non authentifiées :
 * l'admin reste fonctionnel côté back (édition d'auteurs, etc.).
 */
function crdtheme_restrict_users_rest_endpoint( $endpoints ) {
    if ( is_user_logged_in() ) {
        return $endpoints;
    }
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
}
add_filter( 'rest_endpoints', 'crdtheme_restrict_users_rest_endpoint' );


/**
 * Ajoute l'attribut `defer` aux scripts du thème côté front uniquement.
 */
function crdtheme_defer_scripts( $tag, $handle ) {
    if ( is_admin() ) {
        return $tag;
    }
    $defer = array( 'crd-vendors-core', 'crd-vendors-anim', 'crd-scripts' );
    if ( in_array( $handle, $defer, true ) && false === strpos( $tag, ' defer' ) && false === strpos( $tag, ' async' ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'crdtheme_defer_scripts', 10, 2 );

/**
 * Force loading="lazy" + decoding="async" sur les images rendues via
 * wp_get_attachment_image() / get_the_post_thumbnail() / wp_get_attachment_image_attributes.
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
 * Ajoute l'attribut HTML5 `required` natif aux champs obligatoires de CF7.
 * CF7 valide côté serveur mais n'injecte pas `required` dans le HTML — sans ce
 * filtre, le navigateur ne déclenche pas sa propre validation et l'utilisateur
 * doit soumettre pour découvrir qu'un champ obligatoire est vide.
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
 * Force og:type = "website" sur les pages statiques (Contact, Enseignements…),
 * les archives (taxonomies agenda) et la recherche. Yoast met "article" par
 * défaut sur ces contextes, ce qui est sémantiquement faux pour un site
 * institutionnel (+ balises article:* inutiles). Seules les fiches agenda
 * (is_singular('agenda')) gardent "article", cohérent pour un événement.
 */
function crdtheme_opengraph_type( $type ) {
    if ( is_page() || is_search() || is_archive() ) {
        return 'website';
    }
    return $type;
}
add_filter( 'wpseo_opengraph_type', 'crdtheme_opengraph_type' );