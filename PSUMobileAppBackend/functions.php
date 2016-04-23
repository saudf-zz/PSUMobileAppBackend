<?php

/**
 *
 * PSU Backend (functions file)
 * version 1.0
 **/

// general header
ini_set('session.gc_maxlifetime', 68400);
session_set_cookie_params(86400);
require('config.php');
require($db['type'].'.php');

global $database;
$database = new DB();
if(!$database->connection){
    //connection error
}
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');


function auth($sid){
    session_id($sid);
    session_start();
    return isset($_SESSION['uid']);
}

function login($user, $pass){
    $privateKey = openssl_get_privatekey("-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBAILMveJq+2yD2rTo8Fu9ZqtRyylzLyIUUkrUwmPGXLhlXV9mBi6J
ljvQ2JWrh2j+KtHUvzPGyW4BEyB+Bk9lWdkCAwEAAQJAe9YccSGYqUSs7FseNb08
Vzc5giTrmvhicTa+VHiZkHoIaEpeF+p26KJk2D1yiPNYMf7Rh0bGbZTStXOk2YTb
gQIhANhxQEImeYqUeM/xaXtlECYW1dUDZAFiOI/IXfbRVfbvAiEAmrSBoUyMnh/p
eMEpk9g0YlDAUc4OwX4jWKg9Qw+De7cCIQCXvOW4unJw5d/AoFU7zcFBgrbMTEE6
+xn+KxE87MsgfwIgc2uelzfkZYjLiGMc4QfaNUun4KCKk8PHHTsP0bt+TksCIBhI
5kCV3coTiRr5adifXBBZs4FFyU3WQQPraEoZ1TxV
-----END RSA PRIVATE KEY-----");
    openssl_private_decrypt(base64_decode($user), $user, $privateKey);
    openssl_private_decrypt(base64_decode($pass), $pass, $privateKey);
    $query = $GLOBALS['database']->query('SELECT COUNT(*) AS res FROM Students_info WHERE Student_ID ='. addslashes($user)." AND WEB_PASSWORD='".md5($pass)."'");
    $array = $GLOBALS['database']->fetch_array($query);
    session_start();
    $_SESSION['uid']=$user;
    $array['sid']=session_id();
    echo json_encode($array);
}

function absences(){
    $result = $GLOBALS['database']->query("SELECT COUNT(*), C.course_name_s FROM Student_Absence AS A LEFT JOIN sis_courses AS C ON A.course_no=C.course_no WHERE SEMESTER=20152 AND STUDENT_ID=".$_SESSION['uid']." GROUP BY A.COURSE_NO");
    $a = $GLOBALS['database']->fetch_array($result);
    $array=array();
    $i=0;
    while($row = $GLOBALS['database']->fetch_row()){
        $array[i] = array(0=>$row[0], 1=>$row[1]);
    }
    echo json_encode($GLOBALS['database']->fetch_array($result));
}

function info (){
    $result = $GLOBALS['database']->fetch_array($GLOBALS['database']->query("SELECT Students_Info.FIRST_NAME, Students_Info.MID_NAME, Students_Info.LAST_NAME, Students_Info.STUDENT_ID,
sis_major.MAJOR_NAME_S, ACADEMIC_RECORDS.CUM_GPA FROM Students_Info LEFT JOIN ACADEMIC_RECORDS ON ACADEMIC_RECORDS.STUDENT_ID=Students_Info.STUDENT_ID LEFT JOIN sis_major ON academic_records.MAJOR_ID=sis_major.MAJOR_NO WHERE Students_Info.STUDENT_ID=".$_SESSION['uid']));
    echo json_encode($result);
}

?>