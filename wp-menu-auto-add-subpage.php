<?php
/*
Plugin Name: Menu Auto Add Sub Pages
Description: Auto add menu Subpage based on Pages Hierarchy
Plugin URI: http://romaindorr.fr
Author: Romain DORR
Author URI: http://romaindorr.fr
Version: 1.0
Text Domain: menu-aasp
Domain Path: languages
*/

/*

	Copyright 2015 (C) - Romain DORR (contact@romaindorr.fr)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin Constat
define( 'MENU_AASP', '1.0' );

define( 'MENU_AASP_URL', plugin_dir_url( __FILE__ ) );
define( 'MENU_AASP_DIR', plugin_dir_path( __FILE__ ) );

define( 'MENU_AASP_FOLDER', 'menu-aasp' );


// Function for easy load files
function _menu_aasp_load_files( $dir, $files, $prefix = '' ) {
	foreach ( $files as $file ) {
		if ( is_file( $dir . $prefix . $file . '.php' ) ) {
			require_once( $dir . $prefix . $file . '.php' );
		}
	}
}

// Plugin client classes
if ( is_admin() ) {
	_menu_aasp_load_files( MENU_AASP_DIR . 'classes/', array(
		'admin',
	) );
}

// Init plugin
add_action( 'plugins_loaded', 'init_menu_aasp_plugin' );
function init_menu_aasp_plugin() {
	if ( is_admin() ) {
		new Menu_AASP_Admin();
	}
}
