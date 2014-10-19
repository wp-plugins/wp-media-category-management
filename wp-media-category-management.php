<?php
/**
 * The WordPress Media Category Management Plugin.
 *
 * A plugin to provide bulk category management functionality for media in WordPress sites.
 *
 * @package   WP_MediaCategoryManagement
 * @author    De B.A.A.T. <wp-mcm@de-baat.nl>
 * @license   GPL-3.0+
 * @link      http://www.de-baat.nl/WP_MCM
 * @copyright 2014 De B.A.A.T.
 *
 * @wordpress-plugin
 * Plugin Name: WP Media Category Management
 * Plugin URI:  http://www.de-baat.nl/WP_MCM
 * Description: A plugin to provide bulk category management functionality for media in WordPress sites.
 * Version:     1.1.0
 * Author:      De B.A.A.T. <wp-mcm@de-baat.nl>
 * Author URI:  http://www.de-baat.nl/WP_MCM
 * Text Domain: wp-mcm-locale
 * License:     GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_MCM_LINK',					'http://www.de-baat.nl/WP_MCM' );
define( 'WP_MCM_VERSION',				'1.1.0' );
define( 'WP_MCM_OPTIONS_NAME',			'wp-media-category-management-options' ); // Option name for save settings
define( 'WP_MCM_POST_TAXONOMY',			'category' );
define( 'WP_MCM_MEDIA_TAXONOMY',		'category_media' );
define( 'WP_MCM_ACTION_BULK_TOGGLE',	'bulk_toggle' );

define( 'WP_MCM_URL', 			plugins_url('', __FILE__) );
define( 'WP_MCM_DIR', 			rtrim(plugin_dir_path(__FILE__), '/') );
define( 'WP_MCM_BASENAME', 		dirname(plugin_basename(__FILE__)) );

define( 'MCM_LANG', 'wp-mcm' );

// Our product prefix
//
if (defined('WP_MCM_PREFIX') === false) {
	define('WP_MCM_PREFIX', 'wp-mcm');
}

//====================================================================
// Main Plugin Configuration
//====================================================================

require_once( WP_MCM_DIR . '/include/wp-mcm-functions.php' );
require_once( WP_MCM_DIR . '/include/class-wp-mcm-plugin.php' );
require_once( WP_MCM_DIR . '/include/class-wp-mcm-shortcodes.php' );

// load code that is only needed in the admin section
if ( is_admin() ) {
	require_once( WP_MCM_DIR . '/include/wp-mcm-admin-functions.php' );
}

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'WP_MCM_Plugin', 'activate' ) );

/**
 * @var WP_MCM_Plugin $wp_mcm_plugin an global object for this plugin.
 */
global $wp_mcm_plugin;
$wp_mcm_plugin = WP_MCM_Plugin::get_instance();

