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

    private static $hStatus = [
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

	public static function allowedStatus()
	{
		return self::$hStatus;
	}
    public function manage(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        $id = sanitize_key($data['id']);
        $status = sanitize_key($data['status']);
        $recipient = sanitize_text_field($data['recipient']);
        $statusDatetime = sanitize_text_field($data['statusDatetime']);

        if (!empty($id) && !empty($status) && !empty($recipient) && !empty($statusDatetime) && ($ohSMS = $this->getByMessageId($id))) {
            $options = ['status' => $status, 'status_time' => $statusDatetime];
            if (in_array($status, static::allowedStatus()) && $this->updateById($ohSMS->ID, $options)) {
                return new \WP_REST_Response(['message' => 'OK', 'error' => []], 200);
                exit;
            }
        }
        return new \WP_Error('404', 'Page Not Found!', ['status' => 404]);
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
