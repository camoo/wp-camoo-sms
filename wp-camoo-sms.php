<?php
/**
 * Plugin Name: Camoo SMS
 * Plugin URI: 'https://www.camoo.cm/bulk-sms'
 * Description: Camoo SMS plugin for WordPress
 * Version: 5.1.5
 * Author: Camoo Sarl
 * Author URI: https://www.camoo.cm/
 * Text Domain: wp-sms-camoo
 * Domain Path: /languages
 */

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Load Plugin Defines
 */
require_once 'includes/defines.php';

/**
 * Load plugin Special Functions
 */
require_once WP_CAMOO_SMS_DIR . 'includes/functions.php';

/**
 * Get plugin options
 */
$wpsms_option = get_option('wp_camoo_sms_settings');

/**
 * Initial gateway
 */
require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-gateway.php';

$sms = wp_camoo_sms_initial_gateway();

/**
 * Load Plugin
 */
require WP_CAMOO_SMS_DIR . 'includes/class-wpsms.php';

new CAMOO_SMS();
