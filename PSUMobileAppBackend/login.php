<?php
include('db.php');
    if($_POST['UserID']!= null){
        $query = $database->query('SELECT COUNT(*) FROM Students_info WHERE Student_ID ='. addslashes($_POST['UserID'])."AND WEB_PASSWORD=".md5($_POST['UserPass']));
        echo json_encode(mysqli_fetch_all($query));
    }else{
        echo json_encode(-1);
    }
    D
?>