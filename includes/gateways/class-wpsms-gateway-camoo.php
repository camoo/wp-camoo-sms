<?php

namespace WP_SMS\Gateway;

class Camoo extends \WP_SMS\Gateway
{
    private $wsdl_link = '';
    private $client = null;
    private $http;
    public $tariff = "";
    public $unitrial = true;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        include_once('libraries/camoo/vendor/autoload.php');

        $this->validateNumber = "+2376XXXXYYY";
        $this->help           = 'WordPress SMS API Sending SMS via the CAMOO SMS gateway';
        $this->has_key        = true;
    }

    public function sendSMS()
    {

        /**
         * Modify sender number
         *
         * @since 3.4
         *
         * @param string $this ->from sender number.
         */
        $this->from = apply_filters('wp_sms_from', $this->from);

        /**
         * Modify Receiver number
         *
         * @since 3.4
         *
         * @param array $this ->to receiver number
         */
        $this->to = apply_filters('wp_sms_to', $this->to);

        /**
         * Modify text message
         *
         * @since 3.4
         *
         * @param string $this ->msg text message.
         */
        $this->msg = apply_filters('wp_sms_msg', $this->msg);

        // Get the credit.
        $credit = $this->GetCredit();

        // Check gateway credit
        if (is_wp_error($credit)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return $credit;
        }


        $oMessage = \Camoo\Sms\Message::create($this->username, $this->password);

        try {

            $oMessage->from = $this->from;
            $oMessage->to = $this->to;
            $oMessage->message = $this->msg;
            $oResult = $oMessage->send();
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $oResult);

            /**
             * Run hook after send sms.
             *
             * @since 2.4
             *
             * @param string $result result output.
             */
            do_action('wp_sms_send', $oResult);

            return $oResult;
        } catch (\Camoo\Sms\Exception\CamooSmsException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (! $this->username or ! $this->password) {
            return new \WP_Error('account-credit', __('Username/Password does not set for this gateway', 'wp-sms-pro'));
        }
        $oBalance = \Camoo\Sms\Balance::create($this->username, $this->password);

        try {
            $ohBalance = $oBalance->get();
            return $ohBalance->balance->balance;
        } catch (\Camoo\Sms\Exception\CamooSmsException $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }
    }
}
