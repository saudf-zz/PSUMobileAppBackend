<?php
include('config.php');
include($db['type'].'.php');

global $database;
$database = new DB();
if(!$database->connection){
    echo "Connection error :(";
    exit();
}

function headers(){
    //should be moved later to a general functions file, this function purpose is unifying headers to make any future changes easy.
    header("Access-Control-Allow-Origin: http://localhost:4400");
    header('Content-Type: application/json');
}
?>