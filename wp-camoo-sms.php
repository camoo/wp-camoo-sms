<?php
/**
 * Plugin Name: Camoo SMS
 * Plugin URI: 'https://www.camoo.cm/bulk-sms'
 * Description: Camoo SMS plugin for WordPress
 * Version: 1.0
 * Author: Camoo Sarl
 * Author URI: https://www.camoo.cm/
 * Text Domain: wp-sms-camoo
 * Domain Path: /languages
 */

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Load plugin Special Options init
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/config/bootstrap.php';

$oCamooSMS = (new \CAMOO_SMS\Config\Bootstrap())->initialze();

new \CAMOO_SMS();
