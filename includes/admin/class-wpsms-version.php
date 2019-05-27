<?php

namespace CAMOO_SMS;

use CAMOO_SMS\Admin\Helper;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * WP SMS version class
 *
 * @category   class
 * @package    CAMOO_SMS
 */
class Version
{

    public function __construct()
    {
        // Check pro pack is enabled
        if ($this->pro_is_active()) {
            add_action('wp_camoo_sms_pro_after_setting_logo', array( $this, 'pro_setting_title' ));

            // Check license key.
            if (Option::getOption('license_key_status', true) == 'no') {
                add_action('admin_notices', array( $this, 'license_notice' ));
            }
        } else {
            add_filter('plugin_row_meta', array( $this, 'pro_meta_links' ), 10, 2);
            add_action('admin_enqueue_scripts', array( $this, 'pro_admin_script' ));
            add_action('wp_camoo_sms_pro_after_setting_logo', array( $this, 'pro_setting_title_pro_not_activated' ));
            add_action('wp_camoo_sms_after_setting_logo', array( $this, 'setting_title_pro_not_activated' ));
            add_filter('wpsms_gateway_list', array( $this, 'pro_gateways' ));
        }
    }

    /**
     * Check pro pack is enabled
     * @return bool
     */
    private function pro_is_active()
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        return true;
    }

    /**
     * Check pro pack is exists
     * @return bool
     */
    private function pro_is_exists()
    {
            return true;
    }

    /**
     * @param $links
     * @param $file
     *
     * @return array
     */
    public function pro_meta_links($links, $file)
    {
        if ($file == 'wp-camoo-sms/wp-camoo-sms.php') {
            $links[] = sprintf(__('<b><a href="%s" target="_blank" class="wpsms-plugin-meta-link wp-camoo-sms-pro" title="Get professional package!">Get professional package!</a></b>', 'wp-camoo-sms'), WP_CAMOO_SMS_SITE . '/purchase');
        }

        return $links;
    }

    /**
     * @return string
     * @internal param $string
     */
    public function pro_setting_title()
    {
        echo ' SMS';
    }

    /**
     * @return string
     * @internal param $string
     */
    public function pro_setting_title_pro_not_activated()
    {
        $html = '<p class="wpsms-error-notice">' . __('Requires Pro Pack version!', 'wp-camoo-sms') . '</p>';

        if ($this->pro_is_exists()) {
            $html .= '<a style="margin-bottom: 8px; font-weight: normal;" href="plugins.php" class="button button-primary">' . __('Active WP-SMS-Pro', 'wp-camoo-sms') . '</a>';
        } else {
            $html .= '<a style="margin-bottom: 8px; font-weight: normal;" target="_blank" href="http://wp-camoo-sms-pro.com/purchase/" class="button button-primary">' . __('Buy Professional Pack', 'wp-camoo-sms') . '</a>';
        }

        echo $html;
    }

    public function setting_title_pro_not_activated()
    {
        if (! $this->pro_is_exists()) {
            $html = '<a style="margin: 10px 0; font-weight: normal;" target="_blank" href="http://wp-camoo-sms-pro.com/purchase/" class="button button-primary">' . __('Buy Professional Pack', 'wp-camoo-sms') . '</a>';
            echo $html;
        }
    }

    /**
     * Load script
     */
    public function pro_admin_script()
    {
        wp_enqueue_script('wpsms-pro-admin', WP_CAMOO_SMS_URL . 'assets/js/pro-pack.js', true, WP_CAMOO_SMS_VERSION);
    }

    /**
     * @param $gateways
     *
     * @return mixed
     */
    public function pro_gateways($gateways)
    {
        $gateways['pro_pack_gateways'] = array(
        );

        return $gateways;
    }

    /**
     * Version notice
     */
    public function version_notice()
    {
        Helper::notice(sprintf(__('The <a href="%s" target="_blank">WP-SMS-Pro</a> is out of date and not compatible with new version of WP-SMS, Please update the plugin to the <a href="%s" target="_blank">latest version</a>.', 'wp-camoo-sms'), WP_CAMOO_SMS_SITE, 'https://wp-camoo-sms-pro.com/checkout/purchase-history/'), 'error');
    }

    /**
     * License notice
     */
    public function license_notice()
    {
        $url = admin_url('admin.php?page=wp-camoo-sms-pro');
        Helper::notice(sprintf(__('Please <a href="%s">enter and activate</a> your license key for WP-SMS Pro to enable automatic updates.', 'wp-camoo-sms'), $url), 'error');
    }
}

new Version();
