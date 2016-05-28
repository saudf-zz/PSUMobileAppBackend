<?php
/**
 * MySQL database library: for testing and prototyping.
 *  uses the improved library in PHP (MySQLi).
 *
 * @version 1.0
 */
class DB {
    var $connection;
	function __construct()
    {
		$this->connect();
	}
    function __destruct()
    {
        $this->close();
    }
	function connect()
    {
		if($GLOBALS['db']['port']==null){
			$this->connection = new mysqli($GLOBALS['db']['server'], $GLOBALS['db']['user'], $GLOBALS['db']['password'], $GLOBALS['db']['name']);
		}else{
			$this->connection = new mysqli($GLOBALS['db']['server'], $GLOBALS['db']['user'], $GLOBALS['db']['password'], $GLOBALS['db']['name'], $GLOBALS['db']['port']);
        }
        /* check connection */
        if ($this->connection->connect_errno) {
            $this->log_error($this->connection->connect_error);
            exit();
        }
    }
    function query ($query)
    {
        $result = $this->connection->query($query);
        if(!$result){
            $this->log_error($this->connection->connect_errno.': '.$this->connection->error);
        }
        return $result;
    }
    function fetch_array($result)
    {
        return mysqli_fetch_array($result);
    }
    function fetch_row($result)
    {
        return mysqli_fetch_row($result);
    }
    function log_error($error){
        error_log($error, 3, "db.log");
    }
    function close ()
    {
        return mysqli_close($this->connection);
    }
}
?>