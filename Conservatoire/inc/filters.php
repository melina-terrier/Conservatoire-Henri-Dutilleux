<?php

// Retire la balise p des descriptions de taxonomies
remove_filter('term_description', 'wpautop');

// Ne pas afficher la barre d'administration en front-end
add_filter('show_admin_bar', '__return_false');

// Prise en charge des images SVG
function wpc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'wpc_mime_types');