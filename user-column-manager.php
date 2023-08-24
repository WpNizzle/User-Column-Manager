<?php
/**
 * Plugin Name: User Column Manager
 * Plugin URI: https://github.com/WpNizzle/User-Column-Manager
 * Description: Allows users to add additional columns to the user list.
 * Version: 0.1.0
 * Author: WpNizzle
 * Author URI: https://wpnizzle.com/
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package User Column manager
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue CSS and JavaScript files for User Column Manager.
 */
function user_column_manager_enqueue_scripts() {
	wp_enqueue_style( 'custom-columns-style', plugin_dir_url( __FILE__ ) . 'assets/css/usc-main.css', array(), '0.1.0' );
	wp_enqueue_script( 'custom-columns-script', plugin_dir_url( __FILE__ ) . 'assets/js/usc-main.js', array( 'jquery' ), '0.1.0', true );
}
add_action( 'admin_enqueue_scripts', 'user_column_manager_enqueue_scripts' );


require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

