<?php

namespace CAMOO_SMS;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * CAMOO_SMS gateway class
 */
class Gateway
{
    public $username;
    public $password;
    public $has_key = false;
    public $validateNumber = "";
    public $help = false;
    public $bulk_send = true;
    public $from;
    public $to;
    public $msg;
    protected $db;
    protected $tb_prefix;
    public $options;

    public function __construct()
    {
        global $wpdb;

        $this->db        = $wpdb;
        $this->tb_prefix = $wpdb->prefix;
        $this->options   = Option::getOptions();

        // Check option for add country code to prefix numbers
        if (isset($this->options['mobile_county_code']) and $this->options['mobile_county_code']) {
            add_filter('wp_camoo_sms_to', array( $this, 'applyCountryCode' ));
        }

        if (isset($this->options['send_unicode']) and $this->options['send_unicode']) {
            //add_filter( 'wp_camoo_sms_msg', array( $this, 'applyUnicode' ) );
        }

        // Add Filters
        add_filter('wp_camoo_sms_to', array( $this, 'modify_bulk_send' ));
    }

    /**
     * Initial Gateway
     *
     * @return mixed
     */
    public static function initial()
    {
        // Set the default_gateway class
        $class_name = '\\CAMOO_SMS\\Gateway\\Default_Gateway';
        // Include default gateway
        include_once WP_CAMOO_SMS_DIR . 'includes/class-wpsms-gateway.php';
        include_once WP_CAMOO_SMS_DIR . 'includes/gateways/class-wpsms-gateway-default.php';

        $gateway_name = Option::getOption('gateway_name');
        // Using default gateway if does not set gateway in the setting
        if (empty($gateway_name)) {
            return new $class_name();
        }

        if (is_file(WP_CAMOO_SMS_DIR . 'includes/gateways/class-wpsms-gateway-' . $gateway_name . '.php')) {
            include_once WP_CAMOO_SMS_DIR . 'includes/gateways/class-wpsms-gateway-' . $gateway_name . '.php';
        } elseif (is_file(WP_PLUGIN_DIR . '/wp-camoo-sms/includes/gateways/class-wpsms-pro-gateway-' . $gateway_name . '.php')) {
            include_once(WP_PLUGIN_DIR . '/wp-camoo-sms/includes/gateways/class-wpsms-pro-gateway-' . $gateway_name . '.php');
        } else {
            return new $class_name();
        }

        // Create object from the gateway class
        if ($gateway_name == 'default') {
            $sms = new $class_name();
        } else {
            $class_name = '\\CAMOO_SMS\\Gateway\\' . ucfirst($gateway_name);
            $sms        = new $class_name();
        }

        // Set username and password
        $sms->username = Option::getOption('gateway_username');
        $sms->password = Option::getOption('gateway_password');

        $gateway_key = Option::getOption('gateway_key');

        // Set api key
        if ($sms->has_key && $gateway_key) {
            $sms->has_key = $gateway_key;
        }

        // Show gateway help configuration in gateway page
        if ($sms->help) {
            add_action('wp_camoo_sms_after_gateway', function () {
                echo ' < p class="description" > ' . $sms->help . '</p > ';
            });
        }

        // Check unit credit gateway
        if ($sms->unitrial == true) {
            $sms->unit = __('Credit', 'wp - sms');
        } else {
            $sms->unit = __('SMS', 'wp - sms');
        }

        // Set sender id
        if (! $sms->from) {
            $sms->from = Option::getOption('gateway_sender_id');
        }

        // SET encryption setting
        $sms->encrypt_sms = Option::getOption('encrypt_sms') == 1;

        // SET datacoding
        $sms->isUnicode = Option::getOption('send_unicode') == 1;

        if (! Option::getOption('bulk_chunk')) {
            $sms->bulk_chunk = Option::getOption('bulk_chunk');
        }


        // Unset gateway key field if not available in the current gateway class.
        add_filter('wp_camoo_sms_gateway_settings', function ($filter) {
            global $sms;

            if (! $sms->has_key) {
                unset($filter['gateway_key']);
            }

            return $filter;
        });

        // Return gateway object
        return $sms;
    }

    /**
     * @param $sender
     * @param $message
     * @param $to
     * @param $response
     * @param string $status
     *
     * @return false|int
     */
    public function log($options, $status = 'sent')
    {
        $hData = [
                'date'      => WP_SMS_CURRENT_DATE,
                'sender'    => $options['sender'],
                'message'   => $options['message'],
                'recipient' => implode(',', $options['to']),
                'response'  => var_export($options['response'], true),
                'status'    => $status,
            ];
        if (array_key_exists('message_id', $options)) {
            $hData['message_id'] = $options['message_id'];
        }
        if (array_key_exists('reference', $options)) {
            $hData['reference'] = $options['reference'];
        }

        return $this->db->insert($this->tb_prefix . "camoo_sms_send", $hData);
    }

    /**
     * Apply Country code to prefix numbers
     *
     * @param $recipients
     *
     * @return array
     */
    public function applyCountryCode($recipients = array())
    {
        $country_code = $this->options['mobile_county_code'];
        $numbers      = array();

        foreach ($recipients as $number) {
            // Remove zero from first number
            $number = ltrim($number, '0');

            // Add country code to prefix number
            $numbers[] = $country_code . $number;
        }

        return $numbers;
    }

    /**
     * Apply Unicode for non-English characters
     *
     * @param string $msg
     *
     * @return string
     */
    public function applyUnicode($msg = '')
    {
        $encodedMessage = bin2hex(mb_convert_encoding($msg, 'utf-16', 'utf-8'));

        return $encodedMessage;
    }

    /**
     * @var
     */
    public static $get_response;

    /**
     * @return mixed|void
     */
    public static function gateway()
    {
        $sCamoo = ' (' .__('Recommended', 'wp-camoo-sms'). ')';
        $sCamooLegacy = '';
        if (static::getPhpVersion() < CAMOO_SMS_MIN_PHP_VERSION) {
            $sCamoo = '';
            $sCamooLegacy = ' (' .__('Recommended', 'wp-camoo-sms'). ')';
        }
        $gateways = array(
            ''               => array(
                'default' => __('Please select your gateway', 'wp-camoo-sms'),
            ),
            'camoo' => array(
                'camoo'       => 'camoo.cm' .$sCamoo,
                'camoolegacy' => 'camoo.cm Legacy version' .$sCamooLegacy,
            ),
        );

        return apply_filters('wpsms_gateway_list', $gateways);
    }

    /**
     * @return string
     */
    public static function status()
    {
        global $sms;

        //Check that, Are we in the Gateway CAMOO_SMS tab setting page or not?
        if (is_admin() and isset($_REQUEST['page']) and isset($_REQUEST['tab']) and $_REQUEST['page'] == 'wp-camoo-sms-settings' and $_REQUEST['tab'] == 'gateway') {
            // Get credit
            $result = $sms->getCredit();

            if (is_wp_error($result)) {
                // Set error message
                self::$get_response = var_export($result->get_error_message(), true);

                // Update credit
                update_option('wp_camoo_sms_gateway_credit', 0);

                // Return html
                return '<div class="wpsms-no-credit"><span class="dashicons dashicons-no"></span> ' . __('Deactive!', 'wp-camoo-sms') . '</div>';
            }
            // Update credit
            if (! is_object($result)) {
                update_option('wp_camoo_sms_gateway_credit', $result);
            }
            self::$get_response = var_export($result, true);

            // Return html
            return '<div class="wpsms-has-credit"><span class="dashicons dashicons-yes"></span> ' . __('Active!', 'wp-camoo-sms') . '</div>';
        }
    }

    /**
     * @return mixed
     */
    public static function response()
    {
        return self::$get_response;
    }

    /**
     * @return mixed
     */
    public static function help()
    {
        global $sms;

        // Get gateway help
        return $sms->help;
    }

    /**
     * @return mixed
     */
    public static function from()
    {
        global $sms;

        // Get gateway from
        return $sms->from;
    }

    /**
     * @return string
     */
    public static function bulk_status()
    {
        global $sms;

        // Get bulk status
        if ($sms->bulk_send == true) {
            // Return html
            return '<div class="wpsms-has-credit"><span class="dashicons dashicons-yes"></span> ' . __('Supported', 'wp-camoo-sms') . '</div>';
        } else {
            // Return html
            return '<div class="wpsms-no-credit"><span class="dashicons dashicons-no"></span> ' . __('Does not support!', 'wp-camoo-sms') . '</div>';
        }
    }

    /**
     * @return int
     */
    public static function credit()
    {
        global $sms;
        $result = $sms->getCredit();

        if (is_wp_error($result)) {
            update_option('wp_camoo_sms_gateway_credit', 0);

            return 0;
        }

        if (! is_object($result)) {
            update_option('wp_camoo_sms_gateway_credit', $result);
        }

        return $result;
    }

    /**
     * Modify destination number
     *
     * @param array $to
     *
     * @return array/string
     */
    public function modify_bulk_send($to)
    {
        global $sms;
        if (! $sms->bulk_send) {
            return array( $to[0] );
        }

        return $to;
    }

    public static function getPhpVersion()
    {
        if (!defined('PHP_VERSION_ID')) {
            $version = explode('.', PHP_VERSION);
            define('PHP_VERSION_ID', $version[0] * 10000 + $version[1] * 100 + $version[2]);
        }

        return PHP_VERSION_ID;
    }

    public function can_bulk_send()
    {
        global $sms;
        return $sms->bulk_send;
    }

}
