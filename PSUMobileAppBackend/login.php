<?php
header("Access-Control-Allow-Origin: http://localhost:4400");
header('Content-Type: application/json');
include('db.php');
if(isset($_POST['UserID'])&&isset($_POST['UserPass'])){
        $query = $database->query('SELECT COUNT(*) AS res FROM Students_info WHERE Student_ID ='. addslashes($_POST['UserID'])." AND WEB_PASSWORD='".md5($_POST['UserPass'])."'");
        echo json_encode(mysqli_fetch_array($query));
    }else{
        echo json_encode(array("res"=>-1));
    }
?>