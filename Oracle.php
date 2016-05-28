<?php
/**
 * Oracle database library: for implementation.
 *
 * @version 1.0
 */
class DB
{
    var $connection;
    function __construct()
    {
        if($GLOBALS['db']['port']!= null){
            $this->server = $GLOBALS['db']['port'].":".$GLOBALS['db']['port'];
        }else{
            $this->server=$GLOBALS['db']['server'];
        }
        $this->connect();
    }
    function __destruct()
    {
        $this->close();
    }
    function connect()
    {
        $this->connection = oci_connect($GLOBALS['db']['user'], $GLOBALS['db']['password'], $this->server);
    }
    function query($query)
    {
        return oci_execute(oci_parse($query, $this->connection));
    }
    function fetch_array($result)
    {
        return oci_fetch_array($result);
    }
    function fetch_row($result)
    {
        return oci_fetch_row($result);
    }
    function close()
    {
        return oci_close($this->connection);
    }
}