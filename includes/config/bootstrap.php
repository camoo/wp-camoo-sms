<?php

namespace CAMOO_SMS\Config;

use CAMOO_SMS\Gateway;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Bootstrap
{
    public function initialze()
    {
        require_once dirname(plugin_dir_path(__FILE__)) . '/defines.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-gateway.php';
        if (file_exists(WP_CAMOO_SMS_DIR . 'includes/config/app.php') && is_readable(WP_CAMOO_SMS_DIR . 'includes/config/app.php')) {
            require_once WP_CAMOO_SMS_DIR . 'includes/config/app.php';
        }
        require_once WP_CAMOO_SMS_DIR . 'includes/admin/class-wpsms-admin-helper.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-option.php';
        require WP_CAMOO_SMS_DIR . 'includes/class-wpsms.php';
        // hook afterfind option
        add_filter('option_' .\CAMOO_SMS\Option::MAIN_SETTING_KEY, [\CAMOO_SMS\Option::class, 'afterFind']);
        return Gateway::initial();
    }
}
