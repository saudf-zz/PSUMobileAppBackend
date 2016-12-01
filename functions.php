<?php
/**
 *
 * PSU Backend (functions file)
 *
 * @todo incrementally migrate functions to use web services instead of the mock database currently in use
 * @version 1.0
 **/
//Overriding default server PHP settings
ini_set('session.gc_maxlifetime', 68400);
session_set_cookie_params(86400);
//HTTP header
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
class NotImplementedException extends RuntimeException
{
    //This type of exceptions doesn't exist in PHP so we had to create it
    function __construct($message = "This feature is not yet implemented", $code = 0, $previous = NULL){
        parent::__construct($message.$code,$previous);
    }
}
$client = new SoapClient("http://web.psu.edu.sa/psuws/StudentService.svc?wsdl",array('trace'=>1));
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
function advisor($id){
    global $client;
    try{
        global $client;
        echo $client->GetAdvisor(array('StudentID'=>$id))->GetAdvisorResult;
    }
    catch(SoapFault $e){
        echo  'Caught exception: '.  $e->getMessage(). "\n";
    }
}
/**
 * tests the PSU LMS calendar web servics
 * @param int $id
 * @todo better usage of it, ID should be provided from session or app,
 * the SoapClient should be in a wider scope (most likely global, should be safe nonetheless)
 * @throws SoapFault
 */
function calendar ($id){
    try{
        global $client;
        $client->GetLMSCalendar/*1*/(array('StudentID'=>$id));
        $parser = xml_parser_create();
        xml_parse_into_struct($parser,$client->__getLastResponse(),$output);
        echo json_encode($output);
    }
    catch (SoapFault $e) {
        echo  'Caught exception: '.  $e->getMessage(). "\n";
    }
}
/**
 * Summary of enrolledCourses
 * @param int $id
 * @throws SoapFault
 */
function enrolledCourses ($id){
    try{
        global $client;
        $client->GetEnrolledCourses(array('StudentID'=>$id));
        $parser = xml_parser_create();
        xml_parse_into_struct($parser,$client->__getLastResponse(),$output);
        json_encode($output);
    }
    catch (SoapFault $e) {
        echo  'Caught exception: '.  $e->getMessage(). "\n";
    }
}
/**
 * Summary of sched
 * @throws NotImplementedException all the time since no PSU web service equivelent exists yet
 */
function sched(){
    throw new NotImplementedException();
}
/**
 * prints plan
 * @param mixed $status
 * @throws NotImplementedException all the time since no PSU web service equivelent exists yet
 */
function plan($status){
    throw new NotImplementedException();
}
/**
 * Checks for user's credintials and logs them in if credintials are correct and
 * @todo remove private key from code in order to make it public, authenticartion should be via web services (waiting for university)
 * @param int $id User's student ID
 * @param mixed $pass User's password
 * @throws InvalidArgumentException if ID or password is invzlid
 * @throws NotImplementedException all the time since no PSU web service equivelent exists yet
 */
function login($id, $pass){
    if(!is_string($pass)||!is_int($id)){
        //The app shall do the validation, however someone might try to be clever and connect on their own
        throw new InvalidArgumentException('User ID or password are not in correct format');
    }
    throw new NotImplementedException();
}
/**
 * Summary of absences
 * @throws NotImplementedException all the time since no PSU web service equivelent exists yet
 */
function absences($id){
    global $client;
    try{
        global $client;
        $client->GetAbsences(array('StudentID'=>$id, 'Term'=>20161));
        $parser = xml_parser_create();
        xml_parse_into_struct($parser,$client->__getLastResponse(),$output);
        echo json_encode($output);
    }
    catch(SoapFault $e){
        echo  'Caught exception: '.  $e->getMessage(). "\n";
    }
}
/**
 * since the db schema we had didn't have any college information, this is/was a temporary solution
 * @param mixed $major
 * @deprecated should be provided by web services, it's staying here just incase
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
function exam_sched($id){
    global $client;
    try{
        global $client;
        $client->GetFinalExamSchedule(array('StudentID'=>$id, 'Term'=>20161, 'Campus'=>1))->GetFinalExamScheduleResult;
        //$parser = xml_parser_create();
        //xml_parse_into_struct($parser,$client->__getLastResponse(),$output);
        //echo $client->GetFinalExamSchedule(array('StudentID'=>$id, 'Term'=>20161, 'Campus'=>1))->GetFinalExamScheduleResult;
        echo $client->__getLastResponse();

    }
    catch(SoapFault $e){
        echo  'Caught exception: '.  $e->getMessage(). "\n";
    }
}
?>