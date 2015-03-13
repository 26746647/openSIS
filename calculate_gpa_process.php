<?php
error_reporting(0);
include('Redirect_root.php');
include 'Warehouse.php';
include 'data.php';

    DBQuery("CREATE TEMPORARY table temp_cum_gpa AS
    SELECT  * FROM student_report_card_grades srcg WHERE credit_attempted=
    (SELECT MAX(credit_attempted) FROM student_report_card_grades srcg1 WHERE srcg.course_period_id=srcg1.course_period_id and srcg.student_id=srcg1.student_id AND srcg1.course_period_id IS NOT NULL) 
        GROUP BY course_period_id,student_id,marking_period_id
     UNION SELECT * FROM student_report_card_grades WHERE course_period_id IS NULL AND report_card_grade_id IS NULL");
    
                DBQuery("SELECT CALC_CUM_GPA_MP('".$_REQUEST['mp']."')");
                DBQuery("SELECT SET_CLASS_RANK_MP('".$_REQUEST['mp']."')");
         unset($_REQUEST['modfunc']);      
echo '<br/><table><tr><td width="38"><img src="assets/icon_ok.png" /></td><td valign="middle"><span style="font-size:14px;">The grades for '.GetMP($_REQUEST['mp']).' has been calculated.</span></td></tr></table>';   
?>
