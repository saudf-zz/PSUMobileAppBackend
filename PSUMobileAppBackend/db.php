<?php
include('config.php');
include($db['type'].'.php');

$database = new DB();
if(!$database->connection){
    echo "Connection error :(";
    break;
}
?>