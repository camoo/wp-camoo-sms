<?php

namespace WP_CAMOO\SMS\Model;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
use CAMOO_SMS\Admin\Helper;
use WP_CAMOO\SMS\Exception\AppException;

// https://developer.wordpress.org/reference/classes/wpdb
/**
 * Class AppModel
 * @author CamooSarl
 */
abstract class AppModel
{
    /** @var wpdb $db */
    protected $db;

    /** @var string $tbPrefix */
    protected $tbPrefix;

    protected $table;

    public function __construct()
    {
        global $wpdb;

        $this->db        = $wpdb;
        $this->tbPrefix = $this->db->prefix;
    }

    protected function setTable($name)
    {
        $this->table = $name;
    }

    /**
     * @param string $name
     * @throws MessageException
     * @return string
     */
    public function getTable()
    {
        if (empty($this->name)) {
            throw new AppException('Table name is missing!');
        }
        
        return sprintf('`%s%s`', $this->tbPrefix, Helper::sanitizer($this->name));
    }

    public function insert($params, $values)
    {
        return $this->query($this->prepare("INSERT INTO {$this->getTable()}( ".$this->cleanParams($params)." )VALUES (".$this->getPlaceHolder().")", $values));
    }

    private function getPlaceHolder($params)
    {
        return implode(',', array_fill(0, count($params), '%s'));
    }

    private function cleanParams($params)
    {
        return implode(',', array_map(static function ($param) {
            return Helper::sanitizer($param);
        }, $params));
    }

    public function query($arg)
    {
        return $this->db->query($arg);
    }

    public function prepare($arg)
    {
        return $this->db->prepare($arg);
    }

    public function getDb()
    {
        return $this->db;
    }
}
