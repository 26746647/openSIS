<?php
//================course varification=============================
function VerifyFixedSchedule($columns,$columns_var,$update=false)
{
    $teacher=$columns['TEACHER_ID'];
    $mp_id=$columns['MARKING_PERIOD_ID'];
    $start_date=$columns['BEGIN_DATE'];
    $end_date=$columns['END_DATE'];
    $days=$columns_var['DAYS'];
    $period=$columns_var['PERIOD_ID'];
    $room=$columns_var['ROOM_ID'];
    if($columns_var['DAYS']=='' || $columns_var['PERIOD_ID']=='' || $columns_var['ROOM_ID']=='')
        return 'Input valid details';
    $period_time =  DBGet(DBQuery("SELECT START_TIME, END_TIME FROM school_periods WHERE period_id={$period}"));
    $period_time=$period_time[1];
    $start_time=$period_time['START_TIME'];
    $end_time=$period_time['END_TIME'];
    if($mp_id!='')
    {
        $mp_date=  DBGet(DBQuery("SELECT START_DATE,END_DATE FROM marking_periods where marking_period_id ={$mp_id}"));
        $mp_date=$mp_date[1];
        $end_date=$mp_date[END_DATE];
        $start_date=$mp_date[START_DATE];
    }
    $mp_append_sql=" AND begin_date<='$end_date' AND '$start_date'<=end_date AND IF (schedule_type='BLOCKED',course_period_date BETWEEN '$start_date' AND '$end_date','1')";

    $period_append_sql=" AND period_id IN(SELECT period_id from school_periods WHERE start_time<='$end_time' AND '$start_time'<=end_time)";
    $days_append_sql=' AND (';
    $days_room_append_sql=' AND (';
    $days_arr=str_split($days);
    foreach($days_arr as $day)
    {
        $days_append_sql.="DAYS LIKE '%$day%' OR ";
        $days_room_append_sql.="(DAYS LIKE '%$day%' AND room_id=$room) OR ";
    }
   $days_append_sql=  substr($days_append_sql,0,-4).')';
   $days_room_append_sql=  substr($days_room_append_sql,0,-4).')';
    if($update)
        $cp_id=" AND cp.COURSE_PERIOD_ID!={$columns['COURSE_PERIOD_ID']}";
        else
            $cp_id='';
    $cp_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods cp,course_period_var cpv WHERE cp.course_period_id=cpv.course_period_id AND teacher_id={$teacher}{$mp_append_sql}{$period_append_sql}{$days_append_sql}{$cp_id}"));
    if($cp_RET)
        return 'Teacher Not Available';
    else
    {
        $room_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods cp,course_period_var cpv WHERE cp.course_period_id=cpv.course_period_id{$mp_append_sql}{$period_append_sql}{$days_room_append_sql}{$cp_id}"));
        if($room_RET)
        {
            return 'Room Not Available';
        }
        return true;
    }
}

function VerifyVariableSchedule($columns)
{
    $teacher=$columns['TEACHER_ID'];
    $mp_id=$columns['MARKING_PERIOD_ID'];
    $start_date=$columns['BEGIN_DATE'];
    $end_date=$columns['END_DATE'];
    if(!$_REQUEST['course_period_variable'])
        return 'Please input valid data';
    if($mp_id!='')
    {
        $mp_date=  DBGet(DBQuery("SELECT START_DATE,END_DATE FROM marking_periods where marking_period_id ={$mp_id}"));
        $mp_date=$mp_date[1];
        $end_date=$mp_date[END_DATE];
        $start_date=$mp_date[START_DATE];
    }
    $mp_append_sql=" AND begin_date<='$end_date' AND '$start_date'<=end_date";
    
    $days_append_sql=' AND (';
    $days_room_append_sql=' AND (';

    foreach($_REQUEST['course_period_variable'] as $cp_id=>$days)
    {

            if($days['DAYS']=='' || ($days['PERIOD_ID']=='' || $days['ROOM_ID']==''))
                return 'Please input valid data';
            
            if($cp_id=='new')
            {
                $days_append_sql .="(days like '%$days[DAYS]%' AND start_time<='".$days['n']['END_TIME']."' AND '".$days['n']['START_TIME']."'<=end_time) OR ";
                $days_room_append_sql .="(days like '%$days[DAYS]%' AND room_id=$days[ROOM_ID] AND start_time<='".$days['n']['END_TIME']."' AND '".$days['n']['START_TIME']."'<=end_time) OR ";
        }
            else
            {
                $days_append_sql .="(days like '%$days[DAYS]%' AND start_time<='$days[END_TIME]' AND '$days[START_TIME]'<=end_time) OR ";
                $days_room_append_sql .="(days like '%$days[DAYS]%' AND room_id=$days[ROOM_ID] AND start_time<='$days[END_TIME]' AND '$days[START_TIME]'<=end_time) OR ";
    }
            

    }
    $days_append_sql =  substr($days_append_sql,0,-4).')';
    $days_room_append_sql =  substr($days_room_append_sql,0,-4).')';
    $sql_ch="SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql}";
    $cp_RET=  DBGet(DBQuery($sql_ch));
    if($cp_RET)
        return 'Teacher Not Available';
    elseif($day_RET)
            return 'Day Not Available';
    else
    {
        $room_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE 1{$mp_append_sql}{$days_room_append_sql}"));
        if($room_RET)
        {
            return 'Room Not Available';
        }
        return true;
    }
}

function VerifyVariableSchedule_Update($columns)
{
    
    $teacher=$columns['TEACHER_ID'];
    $mp_id=$columns['MARKING_PERIOD_ID'];
    $start_date=$columns['BEGIN_DATE'];
    $end_date=$columns['END_DATE'];
    $id=$columns['ID'];
    $per_id=$columns['PERIOD_ID'];
    if(!$_REQUEST['course_period_variable'] ||  $columns['PERIOD_ID']='' ||  $columns['ROOM_ID']=='')
        return 'Please input valid data';
    if($columns['CP_SECTION']=='cpv')
    {
        if($mp_id!='')
    {
        $mp_date=  DBGet(DBQuery("SELECT START_DATE,END_DATE FROM marking_periods where marking_period_id ={$mp_id}"));
        $mp_date=$mp_date[1];
        $end_date=$mp_date[END_DATE];
        $start_date=$mp_date[START_DATE];
    }
        $mp_append_sql=" AND begin_date<='$end_date' AND '$start_date'<=end_date";

        $days_append_sql=' AND (';
        $days_room_append_sql=' AND (';

                if($columns['SELECT_DAYS']=='' && ($columns['PERIOD_ID']=='' || $columns['ROOM_ID']==''))
                    return 'Please input valid data';
//                if($columns[CP_VAR_ID]=='n')
//                    $cpv_id='';
//                else {
//                    $cpv_id=' AND id=!\''.$columns[CP_VAR_ID].'\' ';
//                }
                $days_append_sql .="(days like '%$columns[DAYS]%' AND start_time<='$columns[END_TIME]' AND '$columns[START_TIME]'<=end_time) OR ";
                $days_room_append_sql .="(days like '%$columns[DAYS]%' AND room_id=$columns[ROOM_ID] AND start_time<='$columns[END_TIME]' AND '$columns[START_TIME]'<=end_time) OR ";


        $days_append_sql =  substr($days_append_sql,0,-4).')';
        $days_room_append_sql =  substr($days_room_append_sql,0,-4).')';

//       $sql_op="SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE cpv.ID!='".$id."' AND teacher_id={$teacher}{$mp_append_sql}{$days_append_sql}";
      $sql_op="SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE cpv.PERIOD_ID='".$per_id."' AND cp.COURSE_PERIOD_ID!='".$columns['COURSE_PERIOD_ID']."' AND teacher_id={$teacher}{$mp_append_sql}{$days_append_sql}";

       $cp_RET=  DBGet(DBQuery($sql_op));
        if($cp_RET)
            return 'Teacher Not Available';
        else
        {
            $room_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE 1{$mp_append_sql}{$days_room_append_sql} AND cp.course_period_id!={$columns['COURSE_PERIOD_ID']}"));
            if($room_RET)
            {
                return 'Room Not Available';
            }
            return true;
        }
    }
    else
    {
        if($mp_id!='')
    {
        $mp_date=  DBGet(DBQuery("SELECT START_DATE,END_DATE FROM marking_periods where marking_period_id ={$mp_id}"));
        $mp_date=$mp_date[1];
        $end_date=$mp_date[END_DATE];
        $start_date=$mp_date[START_DATE];
    }
        $mp_append_sql=" AND begin_date<='$end_date' AND '$start_date'<=end_date";
        
        if($columns[DAYS]!='n' && $columns[END_TIME]!='' && $columns[START_TIME]!='')
        {
        $days_append_sql=' AND (';
        //$days_room_append_sql=' AND (';

//                if($columns['SELECT_DAYS']=='Y' && ($columns['PERIOD_ID']=='' || $columns['ROOM_ID']==''))
//                    return 'Please input valid data';
        
                $days_append_sql .="(days like '%$columns[DAYS]%' AND start_time<='$columns[END_TIME]' AND '$columns[START_TIME]'<=end_time) OR ";
//                $days_room_append_sql .="(days like '%$columns[DAYS]%' AND room_id=$columns[ROOM_ID] AND start_time<='$columns[END_TIME]' AND '$columns[START_TIME]'<=end_time) OR ";
        

        $days_append_sql =  substr($days_append_sql,0,-4).')';
//        $cp_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql} AND cp.course_period_id!={$columns['COURSE_PERIOD_ID']}"));
        }
       // $days_room_append_sql =  substr($days_room_append_sql,0,-4).')';
//        echo "SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql} AND cp.course_period_id!={$columns['COURSE_PERIOD_ID']}";
//        echo '<br>';
//        $cp_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql} AND cpv.PERIOD_ID='".$per_id."'"));
        $cp_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql} AND cpv.PERIOD_ID='".$per_id."' AND cpv.DAYS='".$columns['SELECT_DAYS']."' AND cpv.ROOM_ID='".$columns['ROOM_ID']."'"));
        if($cp_RET)
            return "Teacher Not Available";
        else 
            return true;
//        return "SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql} AND cpv.PERIOD_ID='".$per_id."' AND cpv.DAYS='".$columns['SELECT_DAYS']."' AND cpv.ROOM_ID='".$columns['ROOM_ID']."'";
//        return print_r($columns);
        
    }
}
function VerifyBlockedSchedule($columns,$course_period_id,$sec,$edit=false)
{
    if($sec=='cpv')
    {
        $cp_det_RET=  DBGet(DBQuery("SELECT TEACHER_ID,MARKING_PERIOD_ID,BEGIN_DATE,END_DATE FROM course_periods WHERE course_period_id=$course_period_id"));
        $cp_det_RET=$cp_det_RET[1];
        $teacher=$cp_det_RET['TEACHER_ID'];
        $mp_id=$cp_det_RET['MARKING_PERIOD_ID'];
        $start_date=$cp_det_RET['BEGIN_DATE'];
        $end_date=$cp_det_RET['END_DATE'];
        if($edit)
        {
           $period=$columns['PERIOD_ID'];
            $room=$columns['ROOM_ID']; 
            $cp_id=" AND cp.COURSE_PERIOD_ID!={$course_period_id}";
        }
        else
        {
            $cp_id='';
            $period=$_REQUEST['values']['PERIOD_ID'];
            $room=$_REQUEST['values']['ROOM_ID'];
        }
        $period_time =  DBGet(DBQuery("SELECT START_TIME, END_TIME FROM school_periods WHERE period_id={$period}"));
        $period_time=$period_time[1];
        $start_time=$period_time['START_TIME'];
        $end_time=$period_time['END_TIME'];

        if($mp_id!='')
        {
            $mp_date=  DBGet(DBQuery("SELECT START_DATE,END_DATE FROM marking_periods where marking_period_id ={$mp_id}"));
            $mp_date=$mp_date[1];
            $end_date=$mp_date[END_DATE];
            $start_date=$mp_date[START_DATE];
        }
        $mp_append_sql=" AND begin_date<='$end_date' AND '$start_date'<=end_date";

        $days_append_sql .=" AND IF (schedule_type='BLOCKED', course_period_date ='".$_REQUEST['meet_date']."' AND start_time<='$end_time' AND '$start_time'<=end_time,'$_REQUEST[meet_date]' BETWEEN begin_date AND end_date AND days like '%".conv_day(date('D',strtotime($_REQUEST['meet_date'])),'key')."%' AND start_time<='$end_time' AND '$start_time'<=end_time)";
        $days_room_append_sql .=" AND IF (schedule_type='BLOCKED', course_period_date ='".$_REQUEST['meet_date']."' AND room_id=$room AND start_time<='$end_time' AND '$start_time'<=end_time,'$_REQUEST[meet_date]' BETWEEN begin_date AND end_date AND days like '%".conv_day(date('D',strtotime($_REQUEST['meet_date'])),'key')."%' AND room_id=$room AND start_time<='$end_time' AND '$start_time'<=end_time)";

        $cp_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql}{$cp_id}"));
        if($cp_RET)
            return 'Teacher Not Available';
        else
        {
            $room_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE 1{$mp_append_sql}{$days_room_append_sql}{$cp_id}"));
            if($room_RET)
            {
                return 'Room Not Available';
            }
            return true;
        }
    }
    else
    {
        $cp_dates=DBGet(DBQuery("SELECT COURSE_PERIOD_DATE,START_TIME,END_TIME,ROOM_ID FROM course_period_var WHERE COURSE_PERIOD_ID='$course_period_id'"));
        $teacher=$columns['TEACHER_ID'];
        $start_date=$columns['BEGIN_DATE'];
        if($columns['MARKING_PERIOD_ID'])
            $mp_id=$columns['MARKING_PERIOD_ID'];
        else
            $mp_id='';
        $end_date=$columns['END_DATE'];
        if($mp_id!='')
        {
            $mp_date=  DBGet(DBQuery("SELECT START_DATE,END_DATE FROM marking_periods where marking_period_id ={$mp_id}"));
            $mp_date=$mp_date[1];
            $end_date=$mp_date[END_DATE];
            $start_date=$mp_date[START_DATE];
        }
        foreach($cp_dates as $key=>$cp)
        {
            $period=$cp['PERIOD_ID'];
            $room=$cp['ROOM_ID']; 
            $meet_date=$cp['COURSE_PERIOD_DATE'];
            $start_time=$cp['START_TIME'];
            $end_time=$cp['END_TIME'];
            $mp_append_sql=" AND begin_date<='$end_date' AND '$start_date'<=end_date";

            $days_append_sql =" AND IF (schedule_type='BLOCKED', course_period_date ='$meet_date' AND start_time<='$end_time' AND '$start_time'<=end_time,'$meet_date' BETWEEN begin_date AND end_date AND days like '%".conv_day(date('D',strtotime($meet_date)),'key')."%' AND start_time<='$end_time' AND '$start_time'<=end_time)";
            $days_room_append_sql =" AND IF (schedule_type='BLOCKED', course_period_date ='$meet_date' AND room_id=$room AND start_time<='$end_time' AND '$start_time'<=end_time,'$meet_date' BETWEEN begin_date AND end_date AND days like '%".conv_day(date('D',strtotime($meet_date)),'key')."%' AND room_id=$room AND start_time<='$end_time' AND '$start_time'<=end_time)";
            $cp_id=" AND cp.COURSE_PERIOD_ID!={$course_period_id}";
            
            $cp_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE teacher_id={$teacher}{$mp_append_sql}{$days_append_sql}{$cp_id}"));
            if($cp_RET)
                return 'Teacher Not Available';
            else
            {
                $room_RET=  DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID FROM course_periods  cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id) WHERE 1{$mp_append_sql}{$days_room_append_sql}{$cp_id}"));
                if($room_RET)
                {
                    return 'Room Not Available';
                }
            }
        }
        $total_days=DBGet(DBQuery("SELECT COUNT(course_period_date) AS CP_DATES FROM course_period_var WHERE COURSE_PERIOD_ID={$course_period_id}"));
        $mp_RET=DBGet(DBQuery("SELECT COUNT(course_period_date) AS CP_DATES FROM course_period_var WHERE COURSE_PERIOD_ID={$course_period_id} AND course_period_date BETWEEN '$start_date' AND '$end_date'"));
        if($total_days[1]['CP_DATES']!=$mp_RET[1]['CP_DATES'])
        {
            if($mp_id!='')
                return 'Marking Period Cannot be Changed.';
            else
                return 'Begin or End Date Cannot be Changed.';
        }

        return true;
    }
    
}
//================Course varification ends=============================

//================ Student schedule varification=============================

function VerifyStudentSchedule($course_RET,$student_id='')
{
    if($student_id=='')
        $student_id=  UserStudentID ();
    if(!$course_RET)
    {
        return 'Incomplete course period';
    }
    if($course_RET[1]['TOTAL_SEATS']-$course_RET[1]['FILLED_SEATS']<=0)
    {
        return 'Seat not available';
    }
    $student_RET=DBGet(DBQuery("SELECT LEFT(GENDER,1) AS GENDER FROM students WHERE STUDENT_ID='".$student_id."'"));
    $student=$student_RET[1];
    if($course_RET[1]['GENDER_RESTRICTION']!='N' && $course_RET[1]['GENDER_RESTRICTION']!=$student['GENDER'])
    {
        return 'There is gender restriction';
    }
    $do_check=false;
    foreach($course_RET as $course)
    {
        if($course['IGNORE_SCHEDULING']!='Y')
        {
            $do_check=true;
            break;
        }
    }
    if($do_check==false)
        return true;
    $teacher=$course_RET[1]['TEACHER_ID'];
    $mp_id=$course_RET[1]['MARKING_PERIOD_ID'];
    //$start_date=$course_RET[1]['BEGIN_DATE'];
    $end_date=$course_RET[1]['END_DATE'];
    $start_date=date('Y-m-d');
    $mp_append_sql=" AND s.start_date<='$end_date' AND ('$start_date'<=s.end_date OR s.end_date IS NULL)";
    
    if($course_RET[1]['SCHEDULE_TYPE']=='FIXED')
    {
        $days=$course_RET[1]['DAYS'];
        $start_time=$course_RET[1]['START_TIME'];
        $end_time=$course_RET[1]['END_TIME'];
    
        $period_days_append_sql=" AND course_period_id IN(SELECT course_period_id from course_period_var cpv,school_periods sp WHERE cpv.period_id=sp.period_id AND ignore_scheduling IS NULL AND sp.start_time<='$end_time' AND '$start_time'<=sp.end_time AND (";
        $days_arr=str_split($days);
        foreach($days_arr as $day)
        {
            $period_days_append_sql.="DAYS LIKE '%$day%' OR ";
        }
        $period_days_append_sql=  substr($period_days_append_sql,0,-4).'))';
    }
    elseif($course_RET[1]['SCHEDULE_TYPE']=='VARIABLE')
    {
        $period_days_append_sql=" AND course_period_id IN(SELECT course_period_id from course_period_var cpv,school_periods sp WHERE cpv.period_id=sp.period_id AND ignore_scheduling IS NULL AND (";
        foreach($course_RET as $period_day)
        {
            $period_days_append_sql .="(sp.start_time<='$period_day[END_TIME]' AND '$period_day[START_TIME]'<=sp.end_time AND DAYS LIKE '%$period_day[DAYS]%') OR ";
        }
        $period_days_append_sql=  substr($period_days_append_sql,0,-4).'))';
    }
    elseif($course_RET[1]['SCHEDULE_TYPE']=='BLOCKED')
    {
        $period_days_append_sql=" AND course_period_id IN(SELECT course_period_id from course_period_var cpv,school_periods sp WHERE cpv.period_id=sp.period_id AND ignore_scheduling IS NULL AND (";
        foreach($course_RET as $period_date)
        {
            $period_days_append_sql .="(sp.start_time<='$period_date[END_TIME]' AND '$period_date[START_TIME]'<=sp.end_time AND IF(course_period_date IS NULL, DAYS LIKE '%$period_date[DAYS]%',course_period_date='$period_date[COURSE_PERIOD_DATE]')) OR ";
        }
        $period_days_append_sql=  substr($period_days_append_sql,0,-4).'))';
    }
    $exist_RET=  DBGet(DBQuery("SELECT s.ID FROM schedule s WHERE student_id=".  $student_id."{$mp_append_sql}{$period_days_append_sql} UNION SELECT s.ID FROM temp_schedule s WHERE student_id=".  $student_id."{$mp_append_sql}{$period_days_append_sql}"));
    if($exist_RET)
        return 'There is a Period Conflict ('.$course_RET[1]['CP_TITLE'].')';
    else
    {
        return true;
    }
}
?>