<?php

use CAMOO_SMS\Newsletter;
use CAMOO_SMS\Option;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Show SMS newsletter form.
 */
function wp_camoo_sms_subscribes()
{
    Newsletter::loadNewsLetter();
}

/**
 * Get option value.
 *
 * @param string $option_name
 * @param null|string $setting_name
 *
 * @return string
 */
function wp_camoo_sms_get_option($option_name, $setting_name=null)
{
    return Option::getOption($option_name, $setting_name);
}

/**
 * Send SMS.
 *
 * @param string|array $to
 * @param string $msg
 * @param bool $is_flash
 *
 * @return string | WP_Error
 */
function wp_camoo_sms_send($to, $msg, $is_flash = false)
{
    global $oCamooSMS;

    $recipient = is_string($to) ? [$to] : $to;
    $oCamooSMS->isflash = $is_flash;
    $oCamooSMS->to      = $to;
    $oCamooSMS->msg     = $msg;

    return $oCamooSMS->sendSMS();
}
