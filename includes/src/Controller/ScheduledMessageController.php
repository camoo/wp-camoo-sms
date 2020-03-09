<?php

namespace WP_CAMOO\SMS\Controller;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Class ScheduledMessageController
 * @author CamooSarl
 */
final class ScheduledMessageController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public static function addAssets()
    {
        wp_enqueue_script('camoosms-scheduled-script', self::getAssetUrl('js') . 'script.js', ['jquery'], WP_CAMOO_SMS_VERSION);
        wp_enqueue_style('camoosms-scheduled-css', self::getAssetUrl('css') . 'style.css', false, WP_CAMOO_SMS_VERSION, 'all');
    }

    public function index()
    {
        self::template('index');
    }
}
