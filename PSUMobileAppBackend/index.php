<?php
/* Connection and includes */
include("config.php");
include($db['db_type'] . ".php");
$database = new DB();
if(!$database->connection){
	echo "false";
}
$result = mysqli_fetch_row($database->query("SELECT Students_Info.FIRST_NAME, Students_Info.MID_NAME, Students_Info.LAST_NAME, Students_Info.STUDENT_ID,
sis_major.MAJOR_NAME_S, ACADEMIC_RECORDS.CUM_GPA FROM Students_Info LEFT JOIN ACADEMIC_RECORDS ON ACADEMIC_RECORDS.STUDENT_ID=Students_Info.STUDENT_ID LEFT JOIN sis_major ON academic_records.MAJOR_ID=sis_major.MAJOR_NO WHERE Students_Info.STUDENT_ID=".$_GET['id']));
echo json_encode($result);
?>