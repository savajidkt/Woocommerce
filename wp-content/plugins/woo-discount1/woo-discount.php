<?php
/*
Plugin Name:  Woo Discount
Plugin URI:   https://savajirathod.com 
Description:  A short little description of the plugin. It will be displayed on the Plugins page in WordPress admin area. 
Version:      1.0
Author:       Savji Rathod 
Author URI:   https://savajirathod.com 
License:      GPL2
License URI:  
Text Domain:  woo-discount
Domain Path:  /languages
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WOODISCOUNT_VERSION', '1.0.0' );

// Plugin Root File
define( 'WOODISCOUNT_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'WOODISCOUNT_PLUGIN_BASE', plugin_basename( WOODISCOUNT_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'WOODISCOUNT_PLUGIN_DIR', plugin_dir_path( WOODISCOUNT_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'WOODISCOUNT_PLUGIN_URL', plugin_dir_url( WOODISCOUNT_PLUGIN_FILE ) );

/**
* Load the main class for the core functionality
*/
require_once WOODISCOUNT_PLUGIN_DIR . 'core/class-hot-recipes.php';

/**
* The main function to load the only instance
* of our master class.
*
* @author Freddy
* @since 1.0.0
* @return object|Woo_Dicount
*/
function WOODISCOUNT() {
	return Woo_Dicount::instance();
}

WOODISCOUNT();