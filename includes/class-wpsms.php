<?php

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class CAMOO_SMS
{
    public function __construct()
    {
        /*
         * Plugin Loaded Action
         */
        add_action('plugins_loaded', array( $this, 'plugin_setup' ));

        /**
         * Install And Upgrade plugin
         */
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-install.php';

        register_activation_hook(WP_CAMOO_SMS_DIR . 'wp-camoo-sms.php', array( '\CAMOO_SMS\Install', 'install' ));
        register_deactivation_hook(WP_CAMOO_SMS_DIR . 'wp-camoo-sms.php', [$this, 'sms_status_plugin_deactivate']);
    }

    /**
     * Constructors plugin Setup
     *
     * @param Not param
     */
    public function plugin_setup()
    {
        // Load text domain
        add_action('init', array( $this, 'load_textdomain' ));
        $this->includes();
        add_action('rest_api_init', [$this,'sms_status']);
        add_filter('template_redirect', [$this,'camoo_export']);
    }

    /**
     * Load plugin textdomain.
     *
     * @since 1.0.0
     */
    public function load_textdomain()
    {
        load_plugin_textdomain('wp-camoo-sms', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function sms_status()
    {
        register_rest_route(
            'camoo/v1',
            '/status',
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [new \CAMOO_SMS\Status\Status(),'manage'],
                'args'                => array(
                    'id'      => array(
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_string($param);
                        }
                    ),
                    'status'     => array(
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return in_array($param,\CAMOO_SMS\Status\Status::allowedStatus());
                        }
                    ),
                    'recipient' => array(
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_string($param);
                        }
                    ),
                    'statusDatetime' => [
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_string($param);
                        }
                  ]
                ),

            ]
        );
        flush_rewrite_rules();
    }

    public function camoo_export()
    {
        $page = get_query_var('pagename');
        if ('camoo_export' === $page) {
            return call_user_func([new \CAMOO_SMS\Export\Export(), 'download']);
        }
    }

    public function sms_status_plugin_deactivate()
    {
        flush_rewrite_rules();
    }

    /**
     * Includes plugin files
     *
     * @param Not param
     */
    public function includes()
    {

        // Utility classes.
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-features.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-notifications.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-integrations.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-gravityforms.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-quform.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-newsletter.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-widget.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-rest-api.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-shortcode.php';

        if (is_admin()) {
            // Admin classes.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/class-wpsms-admin.php';
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/class-wpsms-version.php';
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/class-wpsms-admin-helper.php';
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/welcome/class-wpsms-welcome.php';

            // Groups class.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/groups/class-wpsms-groups-table-edit.php';

            // Outbox class.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/outbox/class-wpsms-outbox.php';

            // Privacy class.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/privacy/class-wpsms-privacy-actions.php';

            // Send class.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/send/class-wpsms-send.php';

            // Settings classes.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/settings/class-wpsms-settings.php';
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/settings/class-wpsms-settings-pro.php';

            // Subscribers class.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/subscribers/class-wpsms-subscribers-table-edit.php';

            // System info class.
            require_once WP_CAMOO_SMS_DIR . 'includes/admin/system-info/class-wpsms-system-info.php';
        }

        if (! is_admin()) {
            // Front Class.
            require_once WP_CAMOO_SMS_DIR . 'includes/class-front.php';
        }

        // API class.
        require_once WP_CAMOO_SMS_DIR . 'includes/api/v1/class-wpsms-api-newsletter.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/api/v1/class-wpsms-api-subscribers.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/api/v1/class-wpsms-api-send.php';
        require_once WP_CAMOO_SMS_DIR . 'includes/api/v1/class-wpsms-api-credit.php';

        // Template functions.
        require_once WP_CAMOO_SMS_DIR . 'includes/template-functions.php';

        // SMS Status
        require_once WP_CAMOO_SMS_DIR . 'includes/status/class-camoo-sms-status.php';
        // Export Download class.
        require_once WP_CAMOO_SMS_DIR . 'includes/export/class-camoo-sms-export.php';
    }
}
