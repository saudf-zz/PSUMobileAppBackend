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
global $database, $curr_semes;
$database = new DB();
$curr_semes = 20152;
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
function auth($sid){
    session_id($sid);
    session_start();
    return isset($_SESSION['uid']);
}
function sched(){
    $result = $GLOBALS['database']->query("SELECT * FROM STUDENT_MEETING WHERE COURSE_ID IN (SELECT COURSE_NO FROM Student_courses WHERE STUDENT_ID=".$_SESSION['sid']." AND SEMESTER=".$GLOBALS['curr_semes'].")");
    $array=array();
    for($i=0;$row = $GLOBALS['database']->fetch_row($result);$i++){
        $array[$i] = array(0=>$row[0], 1=>$row[1], 2=>$row[2], 3=>$row[3]);
    }
    echo json_encode($array);
}
function plan($status){
    switch(strtolower($status)){
        case 'completed':
            $status='S';
        case 'in progress':
            $status = "IP";
        case 'not completed':
        default:
            $status = 'U';
    }
    $result = $GLOBALS['database']->query("SELECT * FROM STUDENT_PLANS WHERE STATUS='$status' STUDENT_ID=".$_SESSION['sid']);
    $array=array();
    for($i=0;$row = $GLOBALS['database']->fetch_row($result);$i++){
        $array[$i] = array(0=>$row[0], 1=>$row[1], 2=>$row[2], 3=>$row[3]);
    }
    echo json_encode($array);
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
    $result = $GLOBALS['database']->query("SELECT C.COURSE_CODE, C.course_name_s, COUNT(*), (COUNT(*)*100)/(C.CONTACT_HRS*16) FROM Student_Absence AS A LEFT JOIN sis_courses AS C ON A.course_no=C.course_no WHERE SEMESTER=". $GLOBALS['curr_semes'] ." AND STUDENT_ID=".$_SESSION['uid']." GROUP BY A.COURSE_NO");
    $array=array();
    for($i=0;$row = $GLOBALS['database']->fetch_row($result);$i++){
        $array[$i] = array(0=>$row[0], 1=>$row[1], 2=>$row[2], 3=>$row[3]);
    }
    echo json_encode($array);
}
function info (){
    function setCollege(&$major){
        switch($major){
            case 'Computer Science':
            case 'Computer Science / Digital Media':
            case 'Software Engineering':
            case 'Information Systems':
            case 'Information Systems / Business Computing & E- Commerce':
                $major = "College of Computer Sciences and Information Systems";
            case 'Finance':
            case 'Marketing':
            case 'Accounting':
            case 'Aviation Management':
                $major = "College of Business Administation";
            case 'Engineering Management / Construction':
            case 'Engineering Management / Production and Manufacturing':
            case 'Networks Engineering':
            case 'Communication Engineering':
                $major = "College of Engineering";
            case 'Law':
                $major = "College of Law";
            default:
                $major = 'undef.';
        }
    }
    $result = $GLOBALS['database']->fetch_array($GLOBALS['database']->query("SELECT CONCAT(Students_Info.FIRST_NAME, ' ', Students_Info.MID_NAME, ' ', Students_Info.LAST_NAME), Students_Info.STUDENT_ID,
sis_major.MAJOR_NAME_S, ACADEMIC_RECORDS.CUM_GPA FROM Students_Info LEFT JOIN ACADEMIC_RECORDS ON ACADEMIC_RECORDS.STUDENT_ID=Students_Info.STUDENT_ID LEFT JOIN sis_major ON academic_records.MAJOR_ID=sis_major.MAJOR_NO WHERE Students_Info.STUDENT_ID=".$_SESSION['uid']));
    setCollege($result[4]);
    echo json_encode($result);
}
function exam_sched(){
    $result = $GLOBALS['database']->query("SELECT C.COURSE_CODE, C.course_name_s, T.FINALEXAM_DATE, T.EXAM_PERIOD FROM TimeTable AS T INNER JOIN SIS_COURSES AS C ON T.COURSE_NO=C.course_no WHERE T.COURSE_NO IN (SELECT COURSE_NO FROM Student_Course WHERE Student_ID=". $_SESSION['uid'] ." AND SEMESTER=". $GLOBALS['curr_semes'] .")");
    $array=array();
    for($i=0;$row=$GLOBALS['database']->fetch_row($result);$i++){
        $array[$i]=array(0=>$row[0], 1=>$row[1],2=>$row[2], 3=>$row[3]);
    }
    echo json_encode($array);
}
?>