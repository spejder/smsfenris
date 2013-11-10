<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 22:06
 */

class DBConnection extends mysqli
{
    /**
     * @return DBConnection
     */
    public static function get() {
        $conn = new DBConnection(DBHOST, DBUSER, DBPASS, DBNAME);

        if ($conn->connect_errno) {
            Logger::error($conn->connect_error. '('. $conn->connect_errno. ')');
            return null;
        }

        //Set char encoding
        $conn->query("SET NAMES ". DBENCODING);

        $conn->autocommit(false);
        return $conn;
    }

    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        Logger::debug("DB Query '". $query. "'");
        $res = parent::query($query, $resultmode);

        if ($this->errno) {
            Logger::error($this->error. '('. $this->errno. ')');
            throw new Exception("DB error, see error log");
        }

        return $res;
    }

    public function getLastInsertId() {
        $res = $this->query("SELECT LAST_INSERT_ID()");
        if (!$res)
            return -1;

        $row = $res->fetch_array(MYSQL_NUM);
        return $row[0];
    }


}