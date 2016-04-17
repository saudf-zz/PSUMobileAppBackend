<?php
/**
 * PSUMobileApp Backend code
 * set the database information as you wish
 * 
 * @version 1.0
 * */

 //globalize the settings array for use in DB class
global $db;

$db['server'] = "localhost";
$db['type'] = "MySQLi";
$db['user'] = "root";
$db['password'] = "";
$db['name'] = "mock";
$db['port'] = null;
// not implemented yet, will be used for psudo-random generations related to login security
$db['seed'] = 4796827464916759436;
?>