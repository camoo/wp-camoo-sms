<?php

namespace CAMOO_SMS\Config;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use CAMOO_SMS\Gateway;
class Bootstrap
{
    public function initialze()
    {
        require_once dirname(plugin_dir_path(__FILE__)) . '/defines.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-gateway.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/admin/class-wpsms-admin-helper.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-option.php';
        require WP_CAMOO_SMS_DIR . 'includes/class-wpsms.php';
        return Gateway::initial();
    }
}
