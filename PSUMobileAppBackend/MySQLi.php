<?php
class DB {

	function __construct() {
		$this->connect();
	}

	function connect(){
		if($GLOBALS['db']['port']==null){
			$this->connection=mysqli_connect($GLOBALS['db']['server'], $GLOBALS['db']['db_user'], $GLOBALS['db']['db_password'], $GLOBALS['db']['db_name']);
		}else{
			$this->connection=mysqli_connect($GLOBALS['db']['server'], $GLOBALS['db']['db_user'], $GLOBALS['db']['db_password'], $GLOBALS['db']['db_name'], $GLOBALS['db']['port']);
        }
    }
    
    function query ($query){
        return mysqli_query($this->connection, $query);
    }

    function close (){
        mysqli_close($this->connection);
    }
}
?>