<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include('../../../Redirect_includes.php');

include_once('modules/Students/includes/functions.php');


#########################################################ENROLLMENT##############################################

if(($_REQUEST['month_values'] && ($_POST['month_values'] || $_REQUEST['ajax'])) || ($_REQUEST['values']['student_enrollment'] && ($_POST['values']['student_enrollment'] || $_REQUEST['ajax'])))
{
	if(!$_REQUEST['values']['student_enrollment']['new']['ENROLLMENT_CODE'] && !$_REQUEST['month_values']['student_enrollment']['new']['START_DATE'])
	{
		unset($_REQUEST['values']['student_enrollment']['new']);
		unset($_REQUEST['day_values']['student_enrollment']['new']);
		unset($_REQUEST['month_values']['student_enrollment']['new']);
		unset($_REQUEST['year_values']['student_enrollment']['new']);
	}
	else
	{
		$date = $_REQUEST['day_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['month_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['year_values']['student_enrollment']['new']['START_DATE'];
		$found_RET = DBGet(DBQuery('SELECT ID FROM student_enrollment WHERE STUDENT_ID=\''.UserStudentID().'\' AND SYEAR=\''.UserSyear().'\' AND \'' . date("Y-m-d",strtotime($date)). '\' BETWEEN START_DATE AND END_DATE'));
		if(count($found_RET))
		{
			unset($_REQUEST['values']['student_enrollment']['new']);
			unset($_REQUEST['day_values']['student_enrollment']['new']);
			unset($_REQUEST['month_values']['student_enrollment']['new']);
			unset($_REQUEST['year_values']['student_enrollment']['new']);
			echo ErrorMessage(array('The student is already enrolled on that date, and could not be enrolled a second time on the date you specified.  Please fix, and try enrolling the student again.'));
		}
	}
        
	$iu_extra['student_enrollment'] = "STUDENT_ID='".UserStudentID()."' AND ID='__ID__'";
	$iu_extra['fields']['student_enrollment'] = 'SYEAR,STUDENT_ID,';
	$iu_extra['values']['student_enrollment'] = "'".UserSyear()."','".UserStudentID()."',";
        if(!$new_student)
        {
            if($_REQUEST['month_values'])
	{
		foreach($_REQUEST['month_values'] as $table=>$values)
		{
			foreach($values as $id=>$columns)
			{
				foreach($columns as $column=>$value)
				{
					#$_REQUEST['values'][$table][$id][$column] = $_REQUEST['day_values'][$table][$id][$column].'-'.$value.'-'.$_REQUEST['year_values'][$table][$id][$column];
					
					if($value == 'JAN')
						$value = '01';
					if($value == 'FEB')
						$value = '02';
					if($value == 'MAR')
						$value = '03';
					if($value == 'APR')
						$value = '04';
					if($value == 'MAY')
						$value = '05';
					if($value == 'JUN')
						$value = '06';
					if($value == 'JUL')
						$value = '07';
					if($value == 'AUG')
						$value = '08';
					if($value == 'SEP')
						$value = '09';
					if($value == 'OCT')
						$value = '10';
					if($value == 'NOV')
						$value = '11';
					if($value == 'DEC')
						$value = '12';


					
					$_REQUEST['values'][$table][$id][$column] = $_REQUEST['year_values'][$table][$id][$column].'-'.$value.'-'.$_REQUEST['day_values'][$table][$id][$column];
					
					if($_REQUEST['values'][$table][$id][$column]=='--')
						$_REQUEST['values'][$table][$id][$column] = '';
				}
			}
		}
	}
        
      
        if($_REQUEST['values']['student_enrollment'])
        {
            $sql='SELECT START_DATE FROM student_enrollment WHERE STUDENT_ID=\''.UserStudentID().'\'';
            $start_date=DBGet(DBQuery($sql));
            $start_date=$start_date[1]['START_DATE'];
            if($_REQUEST['values'][$table][$id][$column]=='')
            {
//                SaveData($iu_extra,'',$field_names);
            }
            else 
            {
                if($_REQUEST['values'][$table][$id][$column]!='' && strtotime($_REQUEST['values'][$table][$id][$column])>=strtotime($start_date))
                {
                    if($column=='END_DATE')
                    {
                    $e_date='1-'.$_REQUEST['month_values'][$table][$id][$column].'-'.$_REQUEST['year_values'][$table][$id][$column];
                    $num_days=date('t',strtotime($e_date));
                    
                    if($num_days<$_REQUEST['day_values'][$table][$id][$column])
                    {
                     $error=date('F',strtotime($e_date)).' has '.$num_days.' days';  
                    }
                    else 
                    {
                      unset($error);  
                    }
                    }
                if(isset($error))
                {
                  echo "<p align='center'><b style='color:red'>".$error."</b></p>";  
                }
                else    
                {
                    $sql='SELECT ID,COURSE_ID,COURSE_PERIOD_ID,MARKING_PERIOD_ID FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'';
                    $schedules=DBGet(DBQuery($sql));
                    $c=count($schedules);
                    if($c>0)
                    { 
                        for($i=1;$i<=count($schedules);$i++)
                        {
                           $cp_id[$i]=$schedules[$i]['COURSE_PERIOD_ID'];
                        }
                        $cp_id=implode(',',$cp_id);
                        $sql='SELECT MAX(SCHOOL_DATE) AS SCHOOL_DATE FROM attendance_period WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID IN ('.$cp_id.')';
                        $attendence=DBGet(DBQuery($sql));
                        $max_at_dt=$attendence[1]['SCHOOL_DATE'];
                        if(strtotime($_REQUEST['values'][$table][$id][$column])>=strtotime($max_at_dt))
                        {
                           
                            SaveData($iu_extra,'',$field_names);
                        }
                        else 
                        {
                            echo "<p align='center'><b style='color:red'>Student cannot be dropped because student has got attendance till ".date('m-d-Y',strtotime($max_at_dt))."</b></p>";
                        }
                            
                        
                    }
                    else 
                    {
                        $get_details=DBGet(DBQuery('SELECT * FROM student_enrollment WHERE ID='.UserStudentID()));
                        if(strtotime($get_details[1]['START_DATE'])>=strtotime($_REQUEST['values'][$table][$id][$column]))
                            echo "<p align='center'><b style='color:red'>Student drop date cannot be before student enrollment date </b></p>";
                        else
                        SaveData($iu_extra,'',$field_names);
                    }
                    }
                }
                else
                {
                     echo "<p align='center'><b style='color:red'>Please enter proper drop date.Drop date must be greater than student enrollment date.</b></p>";
                }
            }
        }
		
        }
}

$functions = array('START_DATE'=>'_makeStartInputDateenrl','ENROLLMENT_CODE'=>'_makeStartInputCodeenrl','END_DATE'=>'_makeEndInputDateenrl','DROP_CODE'=>'_makeEndInputCodeenrl','SCHOOL_ID'=>'_makeSchoolInput');
unset($THIS_RET);
$student_RET_qry='SELECT e.SYEAR, s.FIRST_NAME,s.LAST_NAME,s.GENDER, e.ID,e.GRADE_ID,e.ENROLLMENT_CODE,e.START_DATE,e.DROP_CODE,e.END_DATE,e.END_DATE AS END,e.SCHOOL_ID,e.NEXT_SCHOOL,e.CALENDAR_ID FROM student_enrollment e,students s WHERE e.STUDENT_ID=\''.UserStudentID().'\' AND e.SYEAR=\''.UserSyear().'\' AND e.STUDENT_ID=s.STUDENT_ID ORDER BY e.START_DATE';
$RET=DBGet(DBQuery($student_RET_qry));

$not_add = false;
if(count($RET))
{
	foreach($RET as $value)
	{
		if($value['DROP_CODE']=='' || !$value['DROP_CODE']){
			$not_add = true;
	}
}
}
if($not_add==false)
	$link['add']['html'] = array('START_DATE'=>_makeStartInputDate('','START_DATE'),'ENROLLMENT_CODE'=>_makeStartInputCode('','ENROLLMENT_CODE'),'SCHOOL_ID'=>_makeSchoolInput('','SCHOOL_ID'));


unset($THIS_RET);
$RET = DBGet(DBQuery('SELECT e.SYEAR, s.FIRST_NAME,s.LAST_NAME,s.GENDER, e.ID,e.GRADE_ID,e.ENROLLMENT_CODE,e.START_DATE,e.DROP_CODE,e.END_DATE,e.END_DATE AS END,e.SCHOOL_ID,e.NEXT_SCHOOL,e.CALENDAR_ID FROM student_enrollment e,students s WHERE e.STUDENT_ID=\''.UserStudentID().'\' AND e.SYEAR<=\''.UserSyear().'\' AND e.STUDENT_ID=s.STUDENT_ID ORDER BY e.START_DATE'),$functions);


$columns = array('START_DATE'=>'Start Date ','ENROLLMENT_CODE'=>'Enrollment Code','END_DATE'=>'Drop Date','DROP_CODE'=>'Drop Code','SCHOOL_ID'=>'School');

$schools_RET = DBGet(DBQuery('SELECT ID,TITLE FROM schools WHERE ID!=\''.UserSchool().'\''));
$next_school_options = array(UserSchool()=>'Next grade at current school','0'=>'Retain','-1'=>'Do not enroll after this school year');
if(count($schools_RET))
{
	foreach($schools_RET as $school)
		$next_school_options[$school['ID']] = $school['TITLE'];
}
 
if(!UserSchool()){
   $user_school_RET=DBGet(DBQuery('SELECT SCHOOL_ID FROM student_enrollment WHERE STUDENT_ID=\''.UserStudentID().'\' LIMIT 1'));
   $_SESSION['UserSchool']=$user_school_RET[1]['SCHOOL_ID'];
}
$calendars_RET = DBGet(DBQuery('SELECT CALENDAR_ID,DEFAULT_CALENDAR,TITLE FROM school_calendars WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY DEFAULT_CALENDAR DESC'));
if(count($calendars_RET))
{
	foreach($calendars_RET as $calendar)
		$calendar_options[$calendar['CALENDAR_ID']] = $calendar['TITLE'];
}

if($_REQUEST['student_id']!='new')
{
	if(count($RET))
		$id = $RET[count($RET)]['ID'];
	else
		$id = 'new';

	if($id!='new')
		$next_school = $RET[count($RET)]['NEXT_SCHOOL'];
	if($id!='new')
		$calendar = $RET[count($RET)]['CALENDAR_ID'];
	$div = true;
}
else
{
 	$id = 'new';
	$next_school = UserSchool();
	$calendar = $calendars_RET[1]['CALENDAR_ID'];
	$div = false;
}
################################################################################

#$ethnic_option = array('White, Non-Hispanic'=>'White, Non-Hispanic','Black, Non-Hispanic'=>'Black, Non-Hispanic','Amer. Indian or Alaskan Native'=>'Amer. Indian or Alaskan Native','Asian or Pacific Islander'=>'Asian or Pacific Islander','Hispanic'=>'Hispanic','Other'=>'Other');

//echo '<TABLE width=100% border=0 cellpadding=3>';
//echo '<TR><td height="30px" colspan=2 class=hseparator><b>School Information</b></td></tr><tr><td colspan="2">';
//echo '<TABLE border=0>';
//echo '<tr><td>Student ID</td><td>:</td><td>';
//if($_REQUEST['student_id']=='new')
//{
//echo NoInput('Will automatically be assigned','');
//	//echo TextInput('','assign_student_id','','maxlength=10 size=10 class=cell_medium onkeyup="usercheck_student_id(this)"');
//	echo '<span id="ajax_output_stid"></span>';
//}
//else
//	echo NoInput(UserStudentID(),'');
//
//// ----------------------------- Alternate id ---------------------------- //
//
//echo '<tr><td>Alternate ID</td><td>:</td><td>';
//echo TextInput($student['ALT_ID'],'students[ALT_ID]','','size=10 class=cell_medium maxlength=45');
//echo '</td></tr>';
//
//// ----------------------------- Alternate id ---------------------------- //
//
//echo'</td></tr>';
//echo '<tr><td>Grade<font color=red>*</font></td><td>:</td><td>';
//if($_REQUEST['student_id']!='new' && $student['SCHOOL_ID'])
//	$school_id = $student['SCHOOL_ID'];
//else
//	$school_id = UserSchool();
//$sql = 'SELECT ID,TITLE FROM school_gradelevels WHERE SCHOOL_ID=\''.$school_id.'\' ORDER BY SORT_ORDER';
//$QI = DBQuery($sql);
//$grades_RET = DBGet($QI);
//unset($options);
//if(count($grades_RET))
//{
//	foreach($grades_RET as $value)
//		$options[$value['ID']] = $value['TITLE'];
//}
//if($_REQUEST['student_id']!='new' && $student['SCHOOL_ID']!=UserSchool())
//{
//	$allow_edit = $_openSIS['allow_edit'];
//	$AllowEdit = $_openSIS['AllowEdit'][$_REQUEST['modname']];
//	$_openSIS['AllowEdit'][$_REQUEST['modname']] = $_openSIS['allow_edit'] = false;
//}
//
//if($_REQUEST['student_id']=='new')
//	$student_id = 'new';
//else
//	$student_id = UserStudentID();
//
//if($student_id=='new' && !VerifyDate($_REQUEST['day_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['month_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['year_values']['student_enrollment']['new']['START_DATE']))
//	unset($student['GRADE_ID']);
//
//echo SelectInput($student['GRADE_ID'],'values[student_enrollment]['.$student_id.'][GRADE_ID]',(!$student['GRADE_ID']?'<FONT color=red>':'').''.(!$student['GRADE_ID']?'</FONT>':''),$options,'','');
//echo'</td></tr>';
//echo '<tr><td>Calendar</td><td>:</td><td>'.SelectInput($calendar,"values[student_enrollment][$id][CALENDAR_ID]",(!$calendar||!$div?'':'').''.(!$calendar||!$div?'':''),$calendar_options,false,'',$div).'</td></tr>';
//echo '<tr><td>Rolling/Retention Options</td><td>:</td><td>'.SelectInput($next_school,"values[student_enrollment][$id][NEXT_SCHOOL]",(!$next_school||!$div?'':'').''.(!$next_school||!$div?'':''),$next_school_options,false,'',$div).'</td></tr>';
//echo'</table>';
//echo '</td></TR>';
//echo '<TR><td height="30px" colspan=2 class=hseparator><b>Access Information</b></td></tr><tr><td colspan="2">';
//echo '<TABLE border=0>';
//echo '<tr><td style=width:120px>Username</td><td>:</td><td>';
//echo TextInput($student['USERNAME'],'students[USERNAME]','','class=cell_medium onkeyup="usercheck_init_student(this)"');
//echo '<span id="ajax_output_st"></span>';
//echo'</td></tr>';
//echo '<tr><td>Password</td><td>:</td><td>';
//echo TextInput(array($student['PASSWORD'],str_repeat('*',strlen($student['PASSWORD']))),'students[PASSWORD]','','class=cell_medium onkeyup=passwordStrength(this.value)','AUTOCOMPLETE = off');
//echo '<div id="passwordStrength" style=display:none></div>';
//echo '</td></tr>';
//
//
//echo '<tr><td>Last Login</td><td>:</td><td>';
//echo NoInput(ProperDate(substr($student['LAST_LOGIN'],0,10)).substr($student['LAST_LOGIN'],10),'');
//echo '</td></tr>';
//if(User('PROFILE')=='admin'){
//echo '<tr><td>Disable Student</td><td>:</td><td>';
//echo CheckboxInput($student['IS_DISABLE'],'students[IS_DISABLE]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>');
//echo '</td></tr>';
//}
//echo'</table>';
//echo '</td></TR>';
echo '<TR><td height="30px" colspan=2 class=hseparator><b>Enrollment Information</b></td></tr><tr><td colspan="2">';
//echo '</td></tr></table>';
echo '</TD></TR>';
echo '<tr><td colspan="2"><table>';
echo '<input type=hidden id=cal_stu_id value='.$id.' />';
echo '<tr><td>Calendar<font color=red>*</font></td><td>:</td><td>'.SelectInput($calendar,"values[student_enrollment][$id][CALENDAR_ID]",(!$calendar||!$div?'':'').''.(!$calendar||!$div?'':''),$calendar_options,false,'',$div).'</td></tr>';
echo '<tr><td>Rolling/Retention Options</td><td>:</td><td>'.SelectInput($next_school,"values[student_enrollment][$id][NEXT_SCHOOL]",(!$next_school||!$div?'':'').''.(!$next_school||!$div?'':''),$next_school_options,false,'',$div).'</td></tr>';
echo '</table></td></tr>';
echo '<tr><td colspan="2">';
if($_REQUEST['student_id'] && $_REQUEST['student_id']!='new'){
	
	
	$sql_enroll_id = DBGet(DBQuery('SELECT MAX(ID) AS M_ID FROM student_enrollment WHERE STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
	
	$enroll_id = $sql_enroll_id[1]['M_ID'];
	
	$end_date=DBGet(DBQuery('SELECT END_DATE FROM student_enrollment WHERE STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND ID=\''.$enroll_id.'\''));
	
		#echo $end_date[1]['END_DATE'];
		if($end_date[1]['END_DATE']){
		$end_date=$end_date[1]['END_DATE'];
		DBQuery('UPDATE schedule SET END_DATE=\''.$end_date.'\' WHERE STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND (END_DATE IS NULL OR \''.$end_date.'\' < END_DATE )');
		DBQuery('CALL SEAT_COUNT()');
		}
	}
if($_REQUEST['student_id']!='new')
{
	if(count($RET))
		$id = $RET[count($RET)]['ID'];
	else
		$id = 'new';
		ListOutput($RET,$columns,'Enrollment Record','Enrollment Records',$link);
		if($id!='new')
		$next_school = $RET[count($RET)]['NEXT_SCHOOL'];
	if($id!='new')
		$calendar = $RET[count($RET)]['CALENDAR_ID'];
	$div = true;
}
else
{
 	$id = 'new';
	ListOutputMod($RET,$columns,'Enrollment Record','Enrollment Records',$link,array(),array('count'=>false));
	$next_school = UserSchool();
	$calendar = $calendars_RET[1]['CALENDAR_ID'];
	$div = false;
}

echo '</td></tr><tr><td colspan="2">&nbsp;</td></tr></TD></TR>';



?>
