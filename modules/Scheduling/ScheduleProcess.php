<?php
include('../../Redirect_root.php');
include('../../Warehouse.php');
$course_period_id=$_REQUEST['cp_id'];
$insert=$_REQUEST['insert'];
$date=  DBDate();
if($insert=='true')
{
    $course_RET=DBGet(DBQuery("SELECT *,cp.title AS CP_TITLE FROM course_periods cp,course_period_var cpv,school_periods sp WHERE cp.course_period_id=cpv.course_period_id AND cpv.period_id=sp.period_id AND cp.course_period_id=$course_period_id"));
    $course=$course_RET[1];
    
    $varified=VerifyStudentSchedule($course_RET);
    if($varified===true)
    {
        DBQuery("INSERT INTO temp_schedule(SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,MODIFIED_BY,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$date."','".User('STAFF_ID')."','$course[COURSE_ID]','".$course_period_id."','$course[MP]','$course[MARKING_PERIOD_ID]')");
        $html = 'resp';
        $html .= '<tr id="selected_course_tr_'.$course["COURSE_PERIOD_ID"].'"><td align=left><INPUT type="checkbox" id="selected_course_'.$course["COURSE_PERIOD_ID"].'" name="selected_course_periods[]" checked="checked" value="'.$course["COURSE_PERIOD_ID"].'"></td><td><b> '.$course["CP_TITLE"].'</b></td></tr>';
        $_SESSION['course_periods'][$course_period_id]=$course['CP_TITLE'];
    }
    else
    {
        $html='conf<strong>'.$varified.'</strong>';
        $html .='<input type=hidden id=conflicted_cp value='.$course_period_id.'>';
    }
}
elseif($insert=='false')
{
    DBQuery("DELETE FROM temp_schedule WHERE course_period_id=$course_period_id");
    unset($_SESSION['course_periods'][$course_period_id]);
}
echo $html;
?>
