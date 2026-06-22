<?php
defined( 'ABSPATH' ) || exit;

/**
 * Walker custom pour le menu de navigation principal.
 *
 * Override de Walker_Nav_Menu pour :
 *  - appliquer les classes BEM du thème (menu__item, menu__link) plutôt que
 *    les classes WordPress par défaut (menu-item, etc.)
 *  - marquer la classe 'active' sur l'item courant et son parent
 *  - gérer la variante bouton '-btn' (item de menu stylé comme un CTA)
 *  - injecter aria-current="page" sur le lien actif (accessibilité)
 *  - forcer rel="noopener noreferrer" sur les liens target="_blank" (sécurité)
 *
 * Côté admin (Apparence → Menus) : pour rendre un item du menu en bouton CTA,
 * activer "Classes CSS" via "Options de l'écran" en haut de la page, puis
 * saisir littéralement la classe `-btn` (avec le tiret) dans le champ.
 */

class Crdtheme_Walker_Nav_Menu extends Walker_Nav_Menu {

	private $menu_item_class = 'menu__item';
	private $menu_item_btn   = '-btn';
	private $menu_link_class = 'menu__link';

	/**
	 * Start the element output.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes    = empty( $item->classes ) ? array() : (array) $item->classes;
		$my_classes = array( $this->menu_item_class );

		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-parent', $classes, true ) ) {
			$my_classes[] = 'active';
		}
		if ( in_array( $this->menu_item_btn, $classes, true ) ) {
			$my_classes[] = $this->menu_item_btn;
		}

		$class_names = esc_attr( implode( ' ', $my_classes ) );
		$output     .= '<li class="' . $class_names . '">';

		$is_current  = in_array( 'current-menu-item', $classes, true );
		$rel_value   = ! empty( $item->xfn ) ? $item->xfn : '';
		if ( ! empty( $item->target ) && '_blank' === $item->target && false === stripos( $rel_value, 'noopener' ) ) {
			$rel_value = trim( $rel_value . ' noopener noreferrer' );
		}

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target )     . '"' : '';
		$attributes .= ! empty( $rel_value )        ? ' rel="'    . esc_attr( $rel_value )        . '"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_url( $item->url )         . '"' : '';
		$attributes .= $is_current                  ? ' aria-current="page"' : '';
		$attributes .= ' class="' . $this->menu_link_class . '"';

		$item_output = sprintf(
			'%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
			$args->before      ?? '',
			$attributes,
			$args->link_before ?? '',
			apply_filters( 'the_title', $item->title, $item->ID ),
			$args->link_after  ?? '',
			$args->after       ?? ''
		);

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
