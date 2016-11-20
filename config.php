<?php
/**
 * PSUMobileApp Backend code
 * @deprecated the app won't use databases as planned in the past,
 * it shall use webservices for all data needed from LMS, EDUGATE,
 * current uses: config for a database that acts like the university database
 * in order to test how the backend will deal with the data and any possible errors
 * and also test how the app would deal with the encoded data
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
?>