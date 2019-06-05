<?php

use CAMOO_SMS\Newsletter;
use CAMOO_SMS\Option;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Show SMS newsletter form.
 *
 */
function wp_camoo_sms_subscribes()
{
    Newsletter::loadNewsLetter();
}

/**
 * Get option value.
 *
 * @param $option_name
 * @param bool $pro
 * @param string $setting_name
 *
 * @return string
 */
function wp_camoo_sms_get_option($option_name, $pro = false, $setting_name = '')
{
    return Option::getOption($option_name, $pro, $setting_name);
}

/**
 * Send SMS.
 *
 * @param $to
 * @param $msg $pro
 * @param bool $is_flash
 *
 * @return string | WP_Error
 */
function wp_camoo_sms_send($to, $msg, $is_flash = false)
{
    global $sms;

    $sms->isflash = $is_flash;
    $sms->to      = array( $to );
    $sms->msg     = $msg;

    return $sms->sendSMS();
}
