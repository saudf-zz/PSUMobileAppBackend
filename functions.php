<?php
/**
 *
 * PSU Backend (functions file)
 *
 * @todo incrementally migrate functions to use web services instead of the mock database currently in use
 * @version 1.0
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
/**
 * takes session ID from client (app) and sets it then checks if session is expired or not
 * @param int $sid
 * @return boolean
 */
function auth($sid){
    session_id($sid);
    session_start();
    return isset($_SESSION['uid']);
}
/**
 * tests the PSU LMS calendar web servics
 * @todo better usage of it, ID should be provided from session or app,
 * the SoapClient should be in a wider scope (most likely global, should be safe nonetheless)
 * @throws SoapFault
 */
function calendar (){
    try{
        $client = new SoapClient("http://web.psu.edu.sa/psuws/StudentService.svc?wsdl",array('trace'=>1));
        //$client2 = new SoapClient();
        //$webService = $client->GetLMSCalendar1('214110962');
        $client->GetLMSCalendar1(array('StudentID'=>'214110962'));
        $parser = xml_parser_create();
        xml_parse_into_struct($parser,$client->__getLastResponse(),$output);
        echo print_r($output,true);
    }
    catch (SoapFault $e) {

        echo  'Caught exception: '.  $e->getMessage(). "\n";

    }
}
/**
 * Summary of sched
 */
function sched(){
    $result = $GLOBALS['database']->query("SELECT C.COURSE_CODE, M.DAY, M.START_TIME, M.END_TIME FROM courses_meeting AS M LEFT JOIN sis_courses AS C ON C.course_no=M.course_ID WHERE M.COURSE_ID IN (SELECT COURSE_NO FROM Student_course WHERE STUDENT_ID=".$_SESSION['uid']." AND SEMESTER=".$GLOBALS['curr_semes'].")");
    $array=array();
    for($i=0;$row = $GLOBALS['database']->fetch_row($result);$i++){
        $array[$i] = array(0=>$row[0], 1=>$row[1], 2=>$row[2], 3=>$row[3]);
    }
    echo json_encode($array);
}
/**
 * prints plan
 * @param mixed $status
 */
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
/**
 * Checks for user's credintials and logs them in if credintials are correct and
 * @todo remove private key from code in order to make it public, authenticartion should be via web services (waiting for university)
 * @param int $user User's student ID
 * @param mixed $pass User's password
 */
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
/**
 * Summary of absences
 */
function absences(){
    $result = $GLOBALS['database']->query("SELECT C.COURSE_CODE, C.course_name_s, COUNT(*), (COUNT(*)*100)/(C.CONTACT_HRS*16) FROM Student_Absence AS A LEFT JOIN sis_courses AS C ON A.course_no=C.course_no WHERE SEMESTER=". $GLOBALS['curr_semes'] ." AND STUDENT_ID=".$_SESSION['uid']." GROUP BY A.COURSE_NO");
    $array=array();
    for($i=0;$row = $GLOBALS['database']->fetch_row($result);$i++){
        $array[$i] = array(0=>$row[0], 1=>$row[1], 2=>$row[2], 3=>$row[3]);
    }
    echo json_encode($array);
}
/**
 * since the db schema we had didn't have any college information, this is/was a temporary solution
 * @param mixed $major
 * @deprecated should be provided by web services
 */
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
/**
 * provides college name based on the major or undef. otherwise
 * it was needed because university's database didn't
 */
function info (){
    $result = $GLOBALS['database']->fetch_array($GLOBALS['database']->query("SELECT CONCAT(Students_Info.FIRST_NAME, ' ', Students_Info.MID_NAME, ' ', Students_Info.LAST_NAME), Students_Info.STUDENT_ID,
sis_major.MAJOR_NAME_S, ACADEMIC_RECORDS.CUM_GPA FROM Students_Info LEFT JOIN ACADEMIC_RECORDS ON ACADEMIC_RECORDS.STUDENT_ID=Students_Info.STUDENT_ID LEFT JOIN sis_major ON academic_records.MAJOR_ID=sis_major.MAJOR_NO WHERE Students_Info.STUDENT_ID=".$_SESSION['uid']));
    setCollege($result[4]);
    echo json_encode($result);
}
/**
 * Summary of exam_sched
 */
function exam_sched(){
    $result = $GLOBALS['database']->query("SELECT C.COURSE_CODE, C.course_name_s, T.FINALEXAM_DATE, T.EXAM_PERIOD FROM TimeTable AS T INNER JOIN SIS_COURSES AS C ON T.COURSE_NO=C.course_no WHERE T.COURSE_NO IN (SELECT COURSE_NO FROM Student_Course WHERE Student_ID=". $_SESSION['uid'] ." AND SEMESTER=". $GLOBALS['curr_semes'] .")");
    $array=array();
    for($i=0;$row=$GLOBALS['database']->fetch_row($result);$i++){
        $array[$i]=array(0=>$row[0], 1=>$row[1],2=>$row[2], 3=>$row[3]);
    }
    echo json_encode($array);
}
?>