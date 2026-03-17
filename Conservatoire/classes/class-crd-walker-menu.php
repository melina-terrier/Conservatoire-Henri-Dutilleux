<?php
/**
 * Personnalisation des classes CSS du menu de navigation WordPress.
 */

class Crd_Walker_Nav_Menu extends Walker_Nav_Menu {

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

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target )     . '"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn )        . '"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url )        . '"' : '';
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
