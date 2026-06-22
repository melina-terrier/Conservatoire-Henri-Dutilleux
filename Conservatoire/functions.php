<?php

if ( ! defined( 'GOOGLE_MAPS_API_KEY' ) ) {
	define( 'GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: '' );
}

require_once get_template_directory() . '/inc/actions.php';

require_once get_template_directory() . '/inc/filters.php';

require_once get_template_directory() . '/inc/template-functions.php';

require_once get_template_directory() . '/classes/class-crdtheme-walker-nav-menu.php';

require_once get_template_directory() . '/inc/custom-post-types.php';

require_once get_template_directory() . '/inc/plugins/acf.php';

require_once get_template_directory() . '/inc/plugins/acf-fields.php';