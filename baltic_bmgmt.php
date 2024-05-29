<?php
/*
Plugin Name:	Baltic Banner Management Page
Plugin URI:		https://balticdesign.uk/
Description:	Creates Banner Management Page for ACF Banners
Version:		1.0.0
Author:			Dan Cotugno-Cregin
Author URI:		https://balticdesign.uk/
License:		GPL-2.0+
License URI:	http://www.gnu.org/licenses/gpl-2.0.txt

This plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with This plugin. If not, see {URI to Plugin License}.
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'wp_enqueue_scripts', 'custom_enqueue_files' );
/**
 * Loads <list assets here>.
 */
function custom_enqueue_files() {
	// if this is not the front page, abort.
	// if ( ! is_front_page() ) {
	// 	return;
	// }

	$auto_scroll = get_field('auto_scroll', 'option');
    $scroll_speed = get_field('scroll_speed', 'option');
	
	/**
	 * loads JS files in the footer.
	 */
	 wp_enqueue_script( 'slickjs', plugin_dir_url( __FILE__ ) . 'assets/js/slick.min.js', array( 'jquery' ), '1.9.2', true );

	 $translation_array = array(
        'auto_scroll' => $auto_scroll ? true : false, // Converts to boolean
        'scroll_speed' => ($scroll_speed * 1000) ?: 2000, // Default value if not set
    );
    wp_localize_script('slickjs', 'acfOptions', $translation_array);

	// loads a CSS file in the head.
	wp_enqueue_style( 'slick-css', plugin_dir_url( __FILE__ ) . 'assets/css/slick.css' );
	wp_enqueue_style( 'slick-theme-css', plugin_dir_url( __FILE__ ) . 'assets/css/slick-theme.css' );

	// wp_enqueue_script( 'highlightjs-init', plugin_dir_url( __FILE__ ) . 'assets/js/highlight-init.js', '', '1.0.0', true );
}

require plugin_dir_path( __FILE__ ) . 'includes/class-baltic-shortcodes.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-baltic-adminpage.php';