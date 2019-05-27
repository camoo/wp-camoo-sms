<?php

namespace WP_SMS\Gateway;

class Camoolegacy extends \WP_SMS\Gateway
{
    private $wsdl_link = '';
    private $client = null;
    private $http;
    public $tariff = "";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $encrypt_sms = false;
    public $isUnicode = false;
    public $isflash = false;
    public $clearObject = [\Camoo\Sms\Base::class, 'clear'];
    public $oBalance = [\Camoo\Sms\Balance::class, 'create'];
    public $oMessage = [\Camoo\Sms\Message::class, 'create'];

    public function __construct()
    {
        parent::__construct();
        include_once('libraries/camoo-legacy/vendor/autoload.php');

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
        $this->from = apply_filters('wp_camoo_sms_from', $this->from);

        /**
         * Modify Receiver number
         *
         * @since 3.4
         *
         * @param array $this ->to receiver number
         */
        $this->to = apply_filters('wp_camoo_sms_to', $this->to);

        /**
         * Modify text message
         *
         * @since 3.4
         *
         * @param string $this ->msg text message.
         */
        $this->msg = apply_filters('wp_camoo_sms_msg', $this->msg);
        $oMessage = call_user_func_array($this->oMessage, [$this->username, $this->password]);
        try {
            $oMessage->from = $this->from;
            $oMessage->to = $this->to;
            $oMessage->message = $this->msg;
            if ($this->isflash === true) {
                $oMessage->type = 'flash';
            }
            if ($this->isUnicode !== true) {
                $oMessage->datacoding = 'plain';
            }
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
            do_action('wp_camoo_sms_send', $oResult);

            return $oResult;
        } catch (\Camoo\Sms\Exception\CamooSmsException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function getCredit()
    {
        // Check username and password
        if (! $this->username or ! $this->password) {
            return new \WP_Error('account-credit', __('Username/Password does not set for this gateway', 'wp-camoo-sms-pro'));
        }
        if (property_exists($this, 'clearObject')) {
            call_user_func($this->clearObject);
        }

        $oBalance = call_user_func_array($this->oBalance, [$this->username, $this->password]);
        try {
            $ohBalance = $oBalance->get();
            return $ohBalance->balance->balance;
        } catch (\Camoo\Sms\Exception\CamooSmsException $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }
    }
}
