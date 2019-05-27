<?php

use CAMOO_SMS\Gateway;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * @return mixed
 */
function wp_camoo_sms_initial_gateway()
{
    require_once WP_SMS_DIR . 'includes/class-wpsms-option.php';

    return Gateway::initial();
}
