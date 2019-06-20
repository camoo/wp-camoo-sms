<?php

namespace CAMOO_SMS;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class RestApi
{

    public $sms;
    protected $option;
    protected $db;
    protected $tb_prefix;
    public $namespace;

    public function __construct()
    {
        global $sms, $wpdb;

        $this->sms       = $sms;
        $this->options   = Option::getOptions();
        $this->db        = $wpdb;
        $this->tb_prefix = $wpdb->prefix;
        $this->namespace = 'camoosms';
    }

    /**
     * Handle Response
     *
     * @param $message
     * @param int $status
     *
     * @return \WP_REST_Response
     */
    public static function response($message, $status = 200)
    {
        if ($status == 200) {
            $output = array(
                'message' => $message,
                'error'   => array(),
            );
        } else {
            $output = array(
                'error' => array(
                    'code'    => $status,
                    'message' => $message,
                ),
            );
        }

        return new \WP_REST_Response($output, $status);
    }

    /**
     * Subscribe User
     *
     * @param $name
     * @param $mobile
     * @param null $group
     *
     * @return array|string
     */
    public static function subscribe($name, $mobile, $group)
    {
        global $sms;

        if (empty($name) or empty($mobile)) {
            return new \WP_Error('subscribe', __('The name and mobile number must be valued!', 'wp-camoo-sms'));
        }

        $check_group = Newsletter::getGroup($group);

        if (! isset($check_group) and empty($check_group)) {
            return new \WP_Error('subscribe', __('The group number is not valid!', 'wp-camoo-sms'));
        }

        if (preg_match(CAMOO_SMS_MOBILE_REGEX, $mobile) == false) {
            // Return response
            return new \WP_Error('subscribe', __('Please enter a valid mobile number', 'wp-camoo-sms'));
        }

        $max_number = Option::getOption('mobile_terms_maximum');

        if ($max_number) {
            if (strlen($mobile) > $max_number) {
                // Return response
                return new \WP_Error('subscribe', sprintf(__('Your mobile number should be less than %s digits', 'wp-camoo-sms'), $max_number));
            }
        }
        $min_number = Option::getOption('mobile_terms_minimum');
        if ($min_number) {
            if (strlen($mobile) < $min_number) {
                // Return response
                return new \WP_Error('subscribe', sprintf(__('Your mobile number should be greater than %s digits', 'wp-camoo-sms'), $min_number));
            }
        }

        $gateway_name = Option::getOption('gateway_name');

        if (Option::getOption('newsletter_form_verify') and $gateway_name) {
            // Check gateway setting
            if (! $gateway_name) {
                // Return response
                return new \WP_Error('subscribe', __('Service provider is not available for send activate key to your mobile. Please contact with site.', 'wp-camoo-sms'));
            }

            $key = rand(1000, 9999);

            // Add subscribe to database
            $result = Newsletter::addSubscriber($name, $mobile, $group, '0', $key);

            if ($result['result'] == 'error') {
                // Return response
                return new \WP_Error('subscribe', $result['message']);
            } else {
                $sms->to  = array( $mobile );
                $sms->msg = __('Your activation code', 'wp-camoo-sms') . ': ' . $key;
                $sms->sendSMS();
            }

            // Return response
            return __('You will join the newsletter, Activation code sent to your mobile.', 'wp-camoo-sms');
        } else {
            // Add subscribe to database
            $result = Newsletter::addSubscriber($name, $mobile, $group, '1');

            if ($result['result'] == 'error') {
                // Return response
                return new \WP_Error('subscribe', $result['message']);
            }

            return __('Your number has been successfully subscribed.', 'wp-camoo-sms');
        }
    }

    /**
     * Unsubscribe user
     *
     * @param $name
     * @param $mobile
     * @param null $group
     *
     * @return array|string
     */
    public static function unSubscribe($name, $mobile, $group)
    {

        if (empty($name) or empty($mobile)) {
            return new \WP_Error('unsubscribe', __('The name and mobile number must be valued!', 'wp-camoo-sms'));
        }

        $check_group = Newsletter::getGroup($group);

        if (! isset($check_group) and empty($check_group)) {
            return new \WP_Error('unsubscribe', __('The group number is not valid!', 'wp-camoo-sms'));
        }

        if (preg_match(CAMOO_SMS_MOBILE_REGEX, $mobile) == false) {
            // Return response
            return new \WP_Error('unsubscribe', __('Please enter a valid mobile number', 'wp-camoo-sms'));
        }

        $max_number = Option::getOption('mobile_terms_maximum');

        if ($max_number) {
            if (strlen($mobile) > $max_number) {
                // Return response
                return new \WP_Error('unsubscribe', sprintf(__('Your mobile number should be less than %s digits', 'wp-camoo-sms'), $max_number));
            }
        }

        $max_number = Option::getOption('mobile_terms_minimum');

        if ($max_number) {
            if (strlen($mobile) < $max_number) {
                // Return response
                return new \WP_Error('unsubscribe', sprintf(__('Your mobile number should be greater than %s digits', 'wp-camoo-sms'), $max_number));
            }
        }
        // Delete subscriber
        $result = Newsletter::deleteSubscriberByNumber($mobile, $group);

        // Check result
        if ($result['result'] == 'error') {
            // Return response
            return new \WP_Error('unsubscribe', $result['message']);
        }

        return __('Your subscription was canceled.', 'wp-camoo-sms');
    }

    /**
     * Verify Subscriber
     *
     * @param $mobile
     * @param $name
     * @param $activation
     *
     * @return array|string
     */
    public static function verifySubscriber($name, $mobile, $activation, $group)
    {
        global $sms, $wpdb;

        if (empty($name) or empty($mobile) or empty($activation)) {
            return new \WP_Error('unsubscribe', __('The required parameters must be valued!', 'wp-camoo-sms'));
        }

        // Check the mobile number is string or integer
        if (strpos($mobile, '+') !== false) {
            $db_prepare = $wpdb->prepare("SELECT * FROM `{$wpdb->prefix}camoo_sms_subscribes` WHERE `mobile` = %s AND `status` = %d AND group_ID = %d", $mobile, 0, $group);
        } else {
            $db_prepare = $wpdb->prepare("SELECT * FROM `{$wpdb->prefix}camoo_sms_subscribes` WHERE `mobile` = %d AND `status` = %d AND group_ID = %d", $mobile, 0, $group);
        }

        $check_mobile = $wpdb->get_row($db_prepare);

        if (isset($check_mobile)) {
            if ($activation != $check_mobile->activate_key) {
                // Return response
                return new \WP_Error('verify_subscriber', __('Activation code is wrong!', 'wp-camoo-sms'));
            }

            // Check the mobile number is string or integer
            if (strpos($mobile, '+') !== false) {
                $result = $wpdb->update("{$wpdb->prefix}camoo_sms_subscribes", array( 'status' => '1' ), array( 'mobile' => $mobile, 'group_ID' => $group ), array( '%d', '%d' ), array( '%s' ));
            } else {
                $result = $wpdb->update("{$wpdb->prefix}camoo_sms_subscribes", array( 'status' => '1' ), array( 'mobile' => $mobile, 'group_ID' => $group ), array( '%d', '%d' ), array( '%d' ));
            }

            if ($result) {
                // Send welcome message
                if (Option::getOption('newsletter_form_welcome')) {
                    $template_vars = array(
                        '%subscribe_name%'   => $name,
                        '%subscribe_mobile%' => $mobile,
                    );
                    $text          = Option::getOption('newsletter_form_welcome_text');
                    $message       = str_replace(array_keys($template_vars), array_values($template_vars), $text);

                    $sms->to  = array( $mobile );
                    $sms->msg = $message;
                    $sms->sendSMS();
                }

                // Return response
                return __('Your subscription was successful!', 'wp-camoo-sms');
            }
        }

        return new \WP_Error('verify_subscriber', __('Not found the number!', 'wp-camoo-sms'));
    }

    /**
     * Get Subscribers
     *
     * @param string $page
     * @param string $group_id
     * @param string $mobile
     * @param string $search
     *
     * @return array|object|null
     */
    public static function getSubscribers($page = '', $group_id = '', $mobile = '', $search = '')
    {
        global $wpdb;

        $result_limit = 50;
        $where        = '';
        $limit        = $wpdb->prepare(' LIMIT %d', $result_limit);

        if ($page) {
            $limit = $limit . $wpdb->prepare(' OFFSET %d', $result_limit * $page - $result_limit);
        }
        if ($group_id and $where) {
            $where .= $wpdb->prepare(' AND group_ID = %d', $group_id);
        } elseif ($group_id and ! $where) {
            $where = $wpdb->prepare('WHERE group_ID = %d', $group_id);
        }

        if ($mobile and $where) {
            $where .= $wpdb->prepare(' AND mobile = %s', $mobile);
        } elseif ($mobile and ! $where) {
            $where = $wpdb->prepare('WHERE mobile = %s', $mobile);
        }

        if ($search and $where) {
            $where .= $wpdb->prepare(' AND name LIKE %s', '%' . $wpdb->esc_like($search) . '%');
        } elseif ($search and ! $where) {
            $where = $wpdb->prepare('WHERE name LIKE "%s"', '%' . $wpdb->esc_like($search) . '%');
        }

        $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}camoo_sms_subscribes {$where}{$limit}");

        return $result;
    }

    /**
     * Send SMS
     *
     * @param $to
     * @param $msg
     * @param bool $isflash
     *
     * @return string|\WP_Error
     */
    public static function sendSMS($to, $msg, $isflash = false)
    {
        // Check if valued required parameters or not
        if (empty($to) or empty($msg)) {
            return new \WP_Error('send_sms', __('The required parameters must be valued!', 'wp-camoo-sms'));
        }

        // Get the result
        global $sms;
        $sms->to  = array( $to );
        $sms->msg = $msg;
        $result   = $sms->sendSMS();

        return $result;
    }
}

new RestApi();
