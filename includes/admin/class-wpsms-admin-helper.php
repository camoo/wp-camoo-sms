<?php

namespace CAMOO_SMS\Admin;

class Helper
{

    /**
     * Show Admin Wordpress Ui Notice
     *
     * @param string $text where Show Text Notification
     * @param string $model Type Of Model from list : error / warning / success / info
     * @param boolean $close_button Check Show close Button Or false for not
     * @param  boolean $echo Check Echo or return in function
     * @param string $style_extra add extra Css Style To Code
     *
     * @author Mehrshad Darzi
     * @return string Wordpress html Notice code
     */
    public static function notice($text, $model = "info", $close_button = true, $echo = true, $style_extra = 'padding:12px;')
    {
        $text = '
        <div class="notice notice-' . $model . '' . ( $close_button === true ? " is-dismissible" : "" ) . '">
           <div style="' . $style_extra . '">' . $text . '</div>
        </div>
        ';
        if ($echo) {
            echo $text;
        } else {
            return $text;
        }
    }

    public static function adminUrl($args, $adminFile='admin.php')
    {
        return add_query_arg($args, WP_CAMOO_SMS_ADMIN_URL . $adminFile);
    }

    public static function getPhpVersion()
    {
        if (!defined('CAMOO_SMS_PHP_VERSION_ID')) {
            $version = explode('.', PHP_VERSION);
            define('CAMOO_SMS_PHP_VERSION_ID', $version[0] * 10000 + $version[1] * 100 + $version[2]);
        }

        return CAMOO_SMS_PHP_VERSION_ID;
    }
}
