<?php
require('functions.php');
switch ($_GET['req']){
    case 'check':
            echo json_encode(array("auth"=>auth($_POST['sid'])));
        break;
    case 'login':
        if(isset($_POST['UserID'])&&isset($_POST['UserPass'])){
            login($_POST['UserID'], $_POST['UserPass']);
        }else{
            echo json_encode(array("res"=>-1));
        }
        break;
    case 'info':
        if(auth($_POST['sid'])){
            info();
        }
        break;
    case 'absences':
        if(auth($_POST['sid'])){
        absences();
        }
        break;
    case 'plan':
        break;
    case 'exams':
        if(auth($_POST['sid'])){
            exam_sched();
        }
        break;
    default:
        break;
}
//$database->log_error($database->connection->get_warnings);
?>