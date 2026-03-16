<?php

// Intégration du fichier avec les appels add_action().
require_once get_template_directory() . '/inc/actions.php';

// Intégration du fichier avec les appels add_filter().
require_once get_template_directory() . '/inc/filters.php';


// Intégration du fichier avec les fonctions de template.
require_once get_template_directory() . '/inc/template-functions.php';

// Walker Nav Menu.
require_once get_template_directory() . '/classes/class-crd-walker-menu.php';

// Custom Post Types et taxonomies
require_once get_template_directory() . '/inc/custom-post-types.php';

// Plugin ACF
require_once get_template_directory() . '/inc/plugins/acf.php';