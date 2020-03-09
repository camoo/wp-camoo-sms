<?php

namespace WP_CAMOO\SMS\Model;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Class ScheduledMessageModel
 * @author CamooSarl
 */
class ScheduledMessageModel extends AppModel
{
    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable('camoo_sms_scheduled_messages');
    }
}
