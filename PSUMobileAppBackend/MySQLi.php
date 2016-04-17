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
			$this->connection=mysqli_connect($GLOBALS['db']['server'], $GLOBALS['db']['user'], $GLOBALS['db']['password'], $GLOBALS['db']['name']);
		}else{
			$this->connection=mysqli_connect($GLOBALS['db']['server'], $GLOBALS['db']['user'], $GLOBALS['db']['password'], $GLOBALS['db']['name'], $GLOBALS['db']['port']);
        }
    }
    
    function query ($query)
    {
        return mysqli_query($this->connection, $query);
    }

    function fetch_array($result)
    {
        return mysqli_fetch_array($result);
    }

    function fetch_row($result)
    {
        return mysqli_fetch_row($result);
    }

    function close ()
    {
        return mysqli_close($this->connection);
    }
}
?>