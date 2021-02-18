<?php
/**
 * Plugin Name: Camoo SMS
 * Plugin URI: 'https://www.camoo.cm/bulk-sms'
 * Description: With CAMOO SMS, you have the ability to send (Bulk) SMS to a group, to a user, to a number, to members of SMS newsletter or to every signle event in your site. The usage of this plugin is completely free. You have to just have a CAMOO account. <a target="_blank" href="https://www.camoo.cm/join">Sign up</a> for a free account. Ask CAMOO Team for new access_key
 * Version: 1.2
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
