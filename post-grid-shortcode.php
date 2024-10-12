<?php
/**
 * Plugin Name: Post Grid Shortcode
 * Description: A simple plugin that adds a responsive post grid with pagination and a settings page.
 * Author: Developer Nahin
 * Version: 1.0
 * Author URI: https://nahincj.com
 * Plugin URI: https://wordpress.org/post-grid-shortcode
 * Text Domain: post-grid-shortcode
 * Domain Path: /languages
 */

defined('ABSPATH') || exit; // Exit if accessed directly

// Define constants
define('PGS_VERSION', '1.0');
define('PGS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PGS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main class file
require_once PGS_PLUGIN_DIR . 'includes/class-post-grid-shortcode.php';

// Initialize the plugin
function pgs_plugin_init() {
    $post_grid_shortcode = new Post_Grid_Shortcode();
    $post_grid_shortcode->init();
}
add_action('plugins_loaded', 'pgs_plugin_init');
