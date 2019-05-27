<?php

namespace CAMOO_SMS\Gateway;

use CAMOO_SMS\Gateway;

class Default_Gateway extends Gateway
{
    private $wsdl_link = '';
    public $tariff = '';
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;
    public $bulk_send = false;

    public function __construct()
    {
        $this->validateNumber = "1xxxxxxxxxx";
    }

    public function sendSMS()
    {
        // Check gateway credit
        if (is_wp_error($this->getCredit())) {
            return new \WP_Error('account-credit', __('Your account does not credit for sending sms.', 'wp-camoo-sms'));
        }

        return new \WP_Error('send-sms', __('Does not set any gateway', 'wp-camoo-sms'));
    }

    public function getCredit()
    {
        // Check username and password
        if (! $this->username && ! $this->password) {
            return new \WP_Error('account-credit', __('Username/Password does not set for this gateway', 'wp-camoo-sms'));
        }

        return new \WP_Error('account-credit', 0);
    }
}
