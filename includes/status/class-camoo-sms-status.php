<?php

namespace CAMOO_SMS\Status;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * @category   class
 * @package    CAMOO_SMS_Status
 * @version    1.0
 */
class Status
{
    protected $db;
    protected $tb_prefix;

    protected $hStatus = [
            'delivered',
            'scheduled',
            'buffered',
            'sent',
            'expired',
            'delivery_failed',
        ];

    public function __construct()
    {
        global $wpdb;

        $this->db        = $wpdb;
        $this->tb_prefix = $wpdb->prefix;
    }

    public function manage()
    {
        $id = sanitize_key(get_query_var('id'));
        $status = sanitize_key(get_query_var('status'));
        $recipient = sanitize_text_field(get_query_var('recipient'));
        $statusDatetime = sanitize_text_field(get_query_var('statusDatetime'));

        if (!empty($id) && !empty($status) && !empty($recipient) && !empty($statusDatetime) && ($ohSMS = $this->getByMessageId($id))) {
            $options = ['status' => $status, 'status_time' => $statusDatetime];
            if (in_array($status, $this->hStatus) && $this->updateById($ohSMS->ID, $options)) {
                header('HTTP/1.1 200 OK', true, 200);
                exit;
            }
        }
    }

    private function updateById($id, $options)
    {
        return $this->db->update($this->db->prefix .'camoo_sms_send', $options, ['ID' => $id]);
    }

    private function getByMessageId($id)
    {
        return $this->db->get_row("SELECT * FROM `{$this->db->prefix}camoo_sms_send` WHERE message_id='$id' LIMIT 1");
    }
}
