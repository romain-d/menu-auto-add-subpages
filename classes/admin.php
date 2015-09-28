<?php

/**
*	Admin Class
*	@author Romain DORR
*/
class Menu_AASP_Admin {

	function __construct() {
		add_action( 'publish_page', array( __CLASS__, 'automatic_add_sub_pages' ) );

		add_action( 'admin_init', array( __CLASS__, 'save_option' ) );

		add_action( 'admin_footer', array( __CLASS__, 'load_js' ), 20 );
	}

	/**
	 * When publishing a new child page, add it to the appropriate custom menu.
	 * @param string $post_id current edit post id
	 * @author Romain DORR
	 */
	public static function automatic_add_sub_pages( $post_id ) {

		// Theme supports custom menus?
		if ( ! current_theme_supports( 'menus' ) ) {
			return false;
		}

		// Published page has parent?
		$post = get_post( $post_id );
		if ( ! $post->post_parent ) {
			return;
		}

		// Get all menus for this theme
		$menus = get_registered_nav_menus();

		// Get all locations
		$locations = get_nav_menu_locations();

		// Get Plugin option
		$auto_add_option = get_option( 'menu_aasp_options' );

		// if no menus are configured
		if ( empty( $auto_add_option ) ) {
			return false;
		}

		// Loop through the menus to find page parent
		foreach ( $menus as $menu_name => $menu ) {

			if ( ! isset( $locations[ $menu_name ] ) ) {
				continue;
			}

			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

			// Test if menu is configure for auto add submenus
			if ( empty( $auto_add_option[ $menu->term_id ] ) ) {
				continue;
			}

			$menu_parent = null;
			$menu_items = wp_get_nav_menu_items( $menu->term_id, array(
				'post_status' => 'publish,draft',
			) );

			if ( ! is_array( $menu_items ) ) {
				continue;
			}

			foreach ( $menu_items as $menu_item ) {
				// Item already in menu?
				if ( $menu_item->object_id == $post->ID ) {
					continue 2;
				}
				if ( $menu_item->object_id == $post->post_parent ) {
					$menu_parent = $menu_item;
				}
			}

			// Add new item
			if ( $menu_parent ) {
				wp_update_nav_menu_item( $menu->term_id, 0, array(
					'menu-item-object-id' => $post->ID,
					'menu-item-object'    => $post->post_type,
					'menu-item-parent-id' => $menu_parent->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}
		}
	}

	/**
	 * Save Menu Auto Add Subpages options
	 * @author Romain DORR
	 */
	public static function save_option() {
		if ( empty( $_POST['menu'] ) ) {
			return false;
		}

		$menu = (int) $_POST['menu'];

		$auto_add_option = get_option( 'menu_aasp_options' );

		if ( isset( $auto_add_option[ $menu ] ) ) {
			if ( ! empty( $_POST['auto-add-subpages'] ) && true == $_POST['auto-add-subpages'] ) {
				$auto_add_option[ $menu ] = true;
			} else {
				unset( $auto_add_option[ $menu ] );
			}
		} else {
			if ( ! empty( $_POST['auto-add-subpages'] ) && true == $_POST['auto-add-subpages'] ) {
				$auto_add_option[ $menu ] = true;
			}
		}

		update_option( 'menu_aasp_options', $auto_add_option );
	}

	/**
	 * Load JS for Checkbox
	 * @author Romain DORR
	 */
	public static function load_js() {
		$screen = get_current_screen();

		if ( empty( $screen ) || 'nav-menus' != $screen->base ) {
			return false;
		}

		// get current edit menu
		global $nav_menu_selected_id;

		if ( empty( $nav_menu_selected_id ) ) {
			return false;
		}

		$auto_add_subpages = false;

		$auto_add_option = get_option( 'menu_aasp_options' );
		if ( empty( $auto_add_option ) ) {
			$auto_add_subpages = false;
		} elseif ( ! empty( $auto_add_option[ $nav_menu_selected_id ] ) && true == $auto_add_option[ $nav_menu_selected_id ] ) {
			$auto_add_subpages = true;
		} else {
			$auto_add_subpages = false;
		}

		echo '
			<script type="text/javascript">
				(function($) {
					$( ".auto-add-pages" ).after("<dl class=\"auto-add-subpages\"><dt class=\"howto\">' . __( 'Auto add sub pages', 'bea-menu-aasp' ) . '</dt><dd class=\"checkbox-input\"><input type=\"checkbox\" name=\"auto-add-subpages\" id=\"auto-add-subpages\" value=\"1\" ' . checked( $auto_add_subpages, true, false ) . ' /> <label for=\"auto-add-subpages\">' . __('Automatically add new sub-level pages to this menu', 'bea-menu-aasp' ) .'</label></dd></dl>");
				}(jQuery));

			</script>
		';
	}
}
