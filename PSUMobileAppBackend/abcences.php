<?php
include('db.php');
headers();
$database = new DB();
$database->connect();
if(isset($_POST['UserID']))
{
    $result = $database->query("SELECT COUNT(*), C.course_name_s FROM Student_Absence AS A LEFT JOIN sis_courses AS C ON A.course_no=C.course_no WHERE SEMESTER=20152 AND STUDENT_ID=".addslashes($_POST['UserID'])." GROUP BY A.COURSE_NO");
    echo json_encode($database->fetch_array($result));
}else{
    echo json_encode(array('0'=>-1));
}