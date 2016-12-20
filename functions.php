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
        echo $client->GetAdvisor(array('StudentID'=>$id))->GetAdvisorResult;
    }
    catch(SoapFault $e){
        echo  'Caught exception: '.  $e->getMessage(). "\n";
    }
}
/**
 * tests the PSU LMS calendar web servics
 * @param int $id
 * @throws SoapFault
 */
function calendar ($id){
    global $client;
    try{
        $client->GetLMSCalendar(array('StudentID'=>$id));
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
 * @param integer $id
 * @param integer $semester
 * @throws SoapFault
 */
function enrolledCourses ($id){
    global $client;
    try{
        $client->GetEnrolledCourses(array('StudentID'=>$id,'Term'=>20161))->GetEnrolledCoursesResult;
        $response = $client->__getLastResponse();
        $sxe = new SimpleXMLElement($response);
        $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
        $result = $sxe->xpath("//NewDataSet");
        $out = array();
        $counter = 0;
        foreach($result[0] as $entry){
            $out[$counter] = array('COURSE'=>$entry->{'COURSE'},'SECTION'=>$entry->{'SECTION'});
            $counter++;
        }
        return $out;
    }
    catch (SoapFault $e) {
        echo  'Caught exception: '.  $e->getMessage(). "\n";
        return null;
    }
}
/**
 * Returns the schedule of the student for a specific semester (Courses + start and end times)
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
 * Checks for user's credintials and logs them in if credintials are correct
 *
 * @param int $id User's student ID
 * @param mixed $pass User's password
 * @throws InvalidArgumentException if ID or password is invalid
 */
function login($id, $pass){
    if(!is_string($pass)||!is_int($id)){
        //The app shall do the validation, however someone might try to be clever and connect on their own
        throw new InvalidArgumentException('User ID or password are not in correct format');
    }
    throw new NotImplementedException();
}
/**
 * Gets student ID and term and return an array of the courses and absences
 * @param integer $id student's ID
 * @param integer $term selected term for absences, should be year followed semester number, fall is 1, spring is 2, summer is 3
 * @return array double dimension array where the first dimension is the courses and second is each course's information
 */
function absences($id, $term){
    global $client;
    try{
        $client->GetAbsences(array('StudentID'=>$id, 'Term'=>$term))->GetAbsencesResult;
        $response = $client->__getLastResponse();
        $sxe = new SimpleXMLElement($response);
        $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
        $result = $sxe->xpath("//NewDataSet");
        $out = array();
        $counter = 0;
        foreach ($result[0] as $title) {
            $out[$counter] = array('COURSE'=>$title->{'COURSE'},'ABSENCES'=>$title->{'ABSENCES'});
            $counter++;
        }
        return $out;
    }
    catch(SoapFault $e){
        echo  'Caught exception: '.  $e->getMessage(). "\n";
        return null;
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
        $client->GetFinalExamSchedule(array('StudentID'=>$id, 'Term'=>20161, 'Campus'=>1))->GetFinalExamScheduleResult;
        $response = $client->__getLastResponse();
        $sxe = new SimpleXMLElement($response);
        $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
        $result = $sxe->xpath("//NewDataSet");
        $out = array();
        $counter = 0;
        foreach ($result[0] as $title) {
            $out[$counter] = array('COURSE'=>$title->{'COURSE'},'ABSENCES'=>$title->{'ABSENCES'});
            $counter++;
        }
        return $out;
    }
    catch(SoapFault $e){
        echo  'Caught exception: '.  $e->getMessage(). "\n";
        return null;
    }
}
?>