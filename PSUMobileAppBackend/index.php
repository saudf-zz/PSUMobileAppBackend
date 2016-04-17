<?php
include('db.php');
headers();
if(isset($_POST['UserID'])){
$result = $database->fetch_array($database->query("SELECT Students_Info.FIRST_NAME, Students_Info.MID_NAME, Students_Info.LAST_NAME, Students_Info.STUDENT_ID,
sis_major.MAJOR_NAME_S, ACADEMIC_RECORDS.CUM_GPA FROM Students_Info LEFT JOIN ACADEMIC_RECORDS ON ACADEMIC_RECORDS.STUDENT_ID=Students_Info.STUDENT_ID LEFT JOIN sis_major ON academic_records.MAJOR_ID=sis_major.MAJOR_NO WHERE Students_Info.STUDENT_ID=".$_POST['UserID']));
echo json_encode($result);
}else{
    //no data is posted
}
?>