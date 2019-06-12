<?php
namespace CAMOO_SMS;

class Status
{
    private $table_prefix = '';
    private $dbh_connect = null;
    private $dbh_query  = null;
    private $dbh_error  = null;
    private $dbh_escape = null;
    private $connection = null;

    public function manage()
    {
        if (empty($_GET['id']) || empty($_GET['status']) || empty($_GET['recipient']) || empty($_GET['statusDatetime'])) {
            header('HTTP/1.1 404 Not Found', true, 404);
            exit;
        } // Exit if accessed directly

        if (!($handlers = $this->getMysqlHandlers())) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }
        list($this->dbh_connect, $this->dbh_query, $this->dbh_error, $this->dbh_escape) = $handlers;

        $conf_path  = $this->get_conf_path();
        $this->connection = $this->doDbConnection($conf_path);
        if (empty($this->connection)) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }
        
        $id     = $this->escape_string($_GET['id']);
        $status = $this->escape_string($_GET['status']);
        //$phone  = $this->escape_string($_GET['recipient']);
        //$date   = $this->escape_string($_GET['statusDatetime']);
        if (($hRow=$this->getByMessageId($id)) && $this->updateById($hRow['ID'], $status)) {
            $this->close_connection();
            header('HTTP/1.1 200 OK', true, 200);
            exit;
        }
        $this->close_connection();
        header('HTTP/1.1 400 Bad Request', true, 400);
    }

    private function escape_string($string)
    {
        return $this->is_mysqli()?  call_user_func($this->dbh_escape, $this->connection, trim($string)) : call_user_func($this->dbh_escape, trim($string));
    }

    private function close_connection()
    {
        return $this->is_mysqli()?  mysqli_close($this->connection) : mysql_close();
    }

    private function getMysqlHandlers()
    {
        if (function_exists('mysqli_connect')) {
            return array('mysqli_connect', 'mysqli_query', 'mysqli_error', 'mysqli_real_escape_string');
        }

        if (function_exists('mysql_connect')) {
            return array('mysql_connect', 'mysql_query', 'mysql_error', 'mysql_real_escape_string');
        }
    }

    private function get_conf_path(string $file = 'wp-config.php')
    {
        $opath = $file;
  
        for ($i = 0; $i < 10; $i++) {
            $path = $i == 0 ? './' : str_repeat('../', $i);
            $file = $path . $file;
    
            if (is_readable($file)) {
                return $file;
            }
    
            $file = $opath;
        }
    }

    private function doDbConnection($config)
    {
        require_once($config);

        if (isset($table_prefix)) {
            $this->table_prefix = $table_prefix;
        }

        if ($success = $this->db_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)) {
            return $success;
        }
    }

    private function db_connect($host, $user, $password, $name)
    {
        if ($this->is_mysqli()) {
            $port           = null;
            $socket         = null;
            $port_or_socket = strstr($host, ':');

            if (!empty($port_or_socket)) {
                $host           = substr($host, 0, strpos($host, ':'));
                $port_or_socket = substr($port_or_socket, 1);

                if (strpos($port_or_socket, '/') !== 0) {
                    $port         = intval($port_or_socket);
                    $maybe_socket = strstr($port_or_socket, ':');

                    if (!empty($maybe_socket)) {
                        $socket = substr($maybe_socket, 1);
                    }
                } else {
                    $socket = $port_or_socket;
                }
            }

            $connection = call_user_func($this->dbh_connect, $host, $user, $password, $name, $port, $socket);
        } else {
            $connection = call_user_func($this->dbh_connect, $host, $user, $password, $name);
            mysql_select_db($name);
        }

        if (!$connection) {
            echo "Failed to connect to MySQL: " . call_user_func($this->dbh_error) . "\n";
            return 0;
        }

        return $connection;
    }

    public function fetch_assoc($result)
    {
        $array = array();
        if ($this->is_mysqli()) {
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
        } else {
            while ($row = mysql_fetch_assoc($result)) {
                $array[] = $row;
            }
        }

        return $array;
    }

    public function is_mysqli()
    {
        return $this->dbh_connect === 'mysqli_connect';
    }

    public function execute_query($query)
    {
        if ($this->is_mysqli()) {
            $result = call_user_func($this->dbh_query, $this->connection, $query);
        } else {
            $result = call_user_func($this->dbh_query, $query);
        }

        if (!$result) {
            echo $this->get_error();
        }

        return $result;
    }

    public function get_error()
    {
        if ($this->is_mysqli()) {
            return mysqli_error($this->connection);
        } else {
            return mysql_error();
        }
    }

    private function getByMessageId($id)
    {
        $result = $this->execute_query("SELECT ID,message_id,status FROM " . $this->escape_string($this->table_prefix) . "camoo_sms_send WHERE message_id='$id'");

        $result = $this->fetch_assoc($result);

        if (count($result) == 0) {
            return null;
        }
        return $result[0];
    }

    private function updateById($id, $status)
    {
        return $this->execute_query("UPDATE " . $this->escape_string($this->table_prefix) . "camoo_sms_send SET status='$status' WHERE ID='$id'");
    }
}

$oStatus = new Status();
$oStatus->manage();
