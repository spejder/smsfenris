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

        $conn->autocommit(false);
        return $conn;
    }

    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        parent::query($query, $resultmode);

        if ($this->errno) {
            Logger::error($this->error. '('. $this->errno. ')');
        }
    }


}