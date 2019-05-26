<?php
/**
 * Plugin Name: Camoo SMS
 * Plugin URI: ''
 * Description: Camoo SMS plugin for WordPress
 * Version: 5.1.5
 * Author: Camoo Sarl
 * Author URI: https://www.camoo.cm/
 * Text Domain: wp-sms-camoo
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Load Plugin Defines
 */
require_once 'includes/defines.php';

/**
 * Load plugin Special Functions
 */
require_once WP_SMS_DIR . 'includes/functions.php';

/**
 * Get plugin options
 */
$wpsms_option = get_option( 'wpsms_settings' );

/**
 * Initial gateway
 */
require_once WP_SMS_DIR . 'includes/class-wpsms-gateway.php';

$sms = wp_sms_initial_gateway();

/**
 * Load Plugin
 */
require WP_SMS_DIR . 'includes/class-wpsms.php';

new WP_SMS();
