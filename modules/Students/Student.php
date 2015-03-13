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
include('../../Redirect_modules.php');

ini_set('memory_limit', '1200000000M');
ini_set('max_execution_time','500000');

if($_REQUEST[address_error])
{
    echo $_REQUEST[address_error];
    unset($_REQUEST[address_error]);
}

if($_REQUEST['month_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']!='' && $_REQUEST['day_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']!='' && $_REQUEST['year_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']!='')
{
$months_arr=array("JAN"=>"01","FEB"=>"02","MAR"=>"03","APR"=>"04","MAY"=>"05","JUN"=>"06","JUL"=>"07","AUG"=>"08","SEP"=>"09","OCT"=>"10","NOV"=>"11","DEC"=>"12");    
$s_date=$_REQUEST['year_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE'].'-'.$months_arr[$_REQUEST['month_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']].'-'.$_REQUEST['day_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE'];

if($_REQUEST['values']['student_enrollment'][$_REQUEST['student_id']]['CALENDAR_ID']!='')
{
    $cal_id=$_REQUEST['values']['student_enrollment'][$_REQUEST['student_id']]['CALENDAR_ID'];
}
else
{
    $cal_id=DBGet(DBQuery('SELECT CALENDAR_ID FROM student_enrollment WHERE STUDENT_ID='.$_REQUEST['student_id'].' AND SYEAR='.UserSyear().' AND SCHOOL_ID='.UserSchool().' ORDER BY ID DESC LIMIT 0,1'));
    $cal_id=$cal_id[1]['CALENDAR_ID'];
}


    
    $get_c_dates=DBGet(DBQuery('SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE CALENDAR_ID='.$cal_id.' AND SCHOOL_ID='.UserSchool().' AND SYEAR='.UserSyear()));
    if(count($get_c_dates)>0)
    {
        if($get_c_dates[1]['START_DATE']>$s_date)
        {
            unset($_REQUEST['month_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            unset($_REQUEST['day_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            unset($_REQUEST['year_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            $err='Start date cannot be before school\'s start date';
        }
        elseif($get_c_dates[1]['END_DATE']<$s_date)
        {
            unset($_REQUEST['month_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            unset($_REQUEST['day_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            unset($_REQUEST['year_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            $err='Start date cannot be after school\'s end date';
        }
        else
        {
            $get_sch=DBGet(DBQuery('SELECT * FROM schedule WHERE STUDENT_ID='.$_REQUEST['student_id'].' AND SYEAR='.UserSyear().' AND SCHOOL_ID='.UserSchool().' AND (END_DATE IS NULL OR END_DATE>=\''.date('Y-m-d').'\') ORDER BY START_DATE ASC '));
            if(count($get_sch)>0)
            {
            foreach($get_sch as $gsi=>$gsd)
            {
            if(strtotime($s_date)>=strtotime($gsd['START_DATE']))
            {
            unset($_REQUEST['month_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            unset($_REQUEST['day_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            unset($_REQUEST['year_values']['student_enrollment'][$_REQUEST['student_id']]['START_DATE']);
            $err='Cannot change start date as student has association from '.date('F j, Y',strtotime($gsd['START_DATE']));
            break;
            }
            }
            }
        }
     }
echo '<font style=color:red><b>'.$err.'</b></font>';
unset($err);
unset($s_date);
unset($get_c_dates);
unset($cal_id);


}
# ----------------------------- DELETE Goal & Progress -------------------------------------------- #

if($_REQUEST['action'] == 'delete_goal' || $_REQUEST['action']=='delete_goal_can' || $_REQUEST['action']=='delete_goal_ok')
{
	$goal_id = $_REQUEST['gid'];

	if(!isset($_REQUEST['ans']))
	{

		PopTable('header','Delete Confirmation');
		echo "<div class=clear></div><b>Are you sure want to delete this Goal?</b><div class=clear></div><div class=clear></div><center><a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_goal_ok&gid=".$goal_id."&ans=yes' style='text-decoration:none; padding:6px 24px 6px 25px;' class=btn_medium><strong>OK</strong></a> 
		
		<a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_goal_can&gid=".$goal_id."&ans=no' style='text-decoration:none; padding:6px 15px 6px 15px;' class=btn_medium><strong>Cancel</strong></a></center>";
		
		PopTable('footer');
	}
	elseif(isset($_REQUEST['ans']) && $_REQUEST['ans']=='yes')
	{
		$sql_pro = 'SELECT progress_id FROM progress WHERE goal_id='.$goal_id;
		$res_pro = mysql_query($sql_pro);
		$row_pro_id = mysql_fetch_array($res_pro);
		$pro_final= $row_pro_id[0];
		if(!$pro_final)
		{
			DBQuery("DELETE FROM goal WHERE GOAL_ID = '".$goal_id."'");
			$_REQUEST['action'] = 'delete';
			$_REQUEST['goal_id'] = 'new';
			$_REQUEST['action'] = 'delete_goal_ok';
			unset($_REQUEST['modfunc']);
		}
		else
		{
			$_REQUEST['action'] = 'delete';
			$_REQUEST['goal_id'] = $goal_id;
			$_REQUEST['action'] = 'delete_goal_can';
			echo '<div align="center"><font color="red"><b>Unable to delete Goal. Please delete Progresses first.</b></div>';
			unset($_REQUEST['modfunc']);
		}
	}
	else
	{
		$_REQUEST['action'] = 'delete';
		$_REQUEST['goal_id'] = $goal_id;
		$_REQUEST['action'] = 'delete_goal_can';
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['action']=='delete' || $_REQUEST['action']=='delete_can' || $_REQUEST['action']=='delete_ok')
{

	
	$goal_id = $_REQUEST['gid'];
	$progress_id = $_REQUEST['pid'];




	if(!isset($_REQUEST['ans']))
	{
		$_REQUEST['goal_id'] = $_REQUEST['gid'];
		
		PopTable('header','Delete Confirmation');
		echo "<div class=clear></div><b>Are you sure want to delete this Progress?</b><div class=clear></div><div class=clear></div><center><a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_ok&gid=".$goal_id."&pid=".$progress_id."&ans=yes' style='text-decoration:none; padding:6px 24px 6px 25px;' class=btn_medium><strong>OK</strong></a> 
		
		<a href='Modules.php?modname=Students/Student.php&include=Goal&category_id=5&action=delete_can&gid=".$goal_id."&pid=".$progress_id."&ans=no' style='text-decoration:none; padding:6px 15px 6px 15px;' class=btn_medium><strong>Cancel</strong></a></center>";
		
		PopTable('footer');
	}
	elseif(isset($_REQUEST['ans']) && $_REQUEST['ans']=='yes')
	{
		DBQuery('DELETE FROM progress WHERE PROGRESS_ID = \''.$_REQUEST['pid'].'\'');
		$_REQUEST['action'] = 'delete';
		$_REQUEST['goal_id'] = $goal_id;
		$_REQUEST['action'] = 'delete_ok';
		unset($_REQUEST['modfunc']);
	}
	else
	{
		$_REQUEST['action'] = 'delete';
		$_REQUEST['goal_id'] = $goal_id;
		$_REQUEST['progress_id'] = $progress_id;
		$_REQUEST['action'] = 'delete_can';
		unset($_REQUEST['modfunc']);
	}

}


# ----------------------------------------------------------------------------------------------- #















if($_REQUEST['action']!='delete' && $_REQUEST['action']!='delete_goal')
{

    ####################
if(isset($_REQUEST['student_id']) && $_REQUEST['student_id']!='new' && $_REQUEST['modfunc']!='detail' && $_REQUEST['modfunc']!='lookup')
{
	$RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM students,student_enrollment WHERE students.STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND student_enrollment.STUDENT_ID = students.STUDENT_ID '));
	//$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM students"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) ');
        }
}
####################





if(User('PROFILE')=='admin')
{
    if($_REQUEST['student_id']=='new')
    {
        if(!$_REQUEST['include'])
        {
            unset($_SESSION['student_id']);
            unset($_SESSION['_REQUEST_vars']['student_id']);
        }
    }
}
//////////////////////////////////////////////////////////////////////////////////
if($_REQUEST['err_msg']==true)
    echo "<center><font color=red><b>Birthdate is invalid, data could not be saved.</b><font></center>";
if($_REQUEST['modfunc']=='detail' && $_REQUEST['student_id'] && $_REQUEST['student_id']!='new')
{
	if($_POST['button']=='Save' && AllowEdit())
	{   
                        if($_REQUEST['TRANSFER']['SCHOOL']){
                            $drop_code=$_REQUEST['drop_code'];
                            $_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_END_DATE']=date("Y-m-d",strtotime($_REQUEST['year_TRANSFER']['STUDENT_ENROLLMENT_END_DATE'].'-'.$_REQUEST['month_TRANSFER']['STUDENT_ENROLLMENT_END_DATE'].'-'.$_REQUEST['day_TRANSFER']['STUDENT_ENROLLMENT_END_DATE']));

                            $gread_exists = DBGet(DBQuery('SELECT COUNT(TITLE) AS PRESENT,ID FROM school_gradelevels WHERE SCHOOL_ID=\''.$_REQUEST['TRANSFER']['SCHOOL'].'\' AND TITLE=(SELECT TITLE FROM
                            school_gradelevels WHERE ID=(SELECT GRADE_ID FROM student_enrollment WHERE
                            STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SCHOOL_ID=\''.UserSchool().'\'  AND SYEAR=\''.UserSyear().'\'))'));  //pinki

                            DBQuery('UPDATE student_enrollment SET DROP_CODE=\''.$drop_code.'\',END_DATE=\''.$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_END_DATE'].'\' WHERE STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SCHOOL_ID=\''.UserSchool().'\'  AND SYEAR=\''.UserSyear().'\'');  //pinki
                            
                            $_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_START']=date("Y-m-d",strtotime($_REQUEST['year_TRANSFER']['STUDENT_ENROLLMENT_START'].'-'.$_REQUEST['month_TRANSFER']['STUDENT_ENROLLMENT_START'].'-'.$_REQUEST['day_TRANSFER']['STUDENT_ENROLLMENT_START']));
                            $syear_RET= DBGet(DBQuery("SELECT MAX(SYEAR) AS SYEAR,TITLE FROM school_years WHERE SCHOOL_ID=".$_REQUEST['TRANSFER']['SCHOOL']));
                            $syear=$syear_RET[1]['SYEAR'];
                            $enroll_code=  DBGet(DBQuery('SELECT id FROM student_enrollment_codes WHERE syear=\''.$syear.'\' AND type=\''.TrnE.'\''));  //pinki
                            $last_school_RET= DBGet(DBQuery('SELECT SCHOOL_ID FROM student_enrollment WHERE STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SYEAR=\''.UserSyear().'\'')); //pinki
                            $last_school=$last_school_RET[1]['SCHOOL_ID'];
                            if($gread_exists[1]['PRESENT']==1 && $gread_exists[1]['ID']){
                                DBQuery("INSERT INTO student_enrollment (SYEAR ,SCHOOL_ID ,STUDENT_ID ,GRADE_ID ,START_DATE ,END_DATE ,ENROLLMENT_CODE ,DROP_CODE ,NEXT_SCHOOL ,CALENDAR_ID ,LAST_SCHOOL) VALUES (".  $syear.",".$_REQUEST['TRANSFER']['SCHOOL'].",".$_REQUEST['student_id'].",".$gread_exists[1]['ID'].",'".$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_START']."','',".$enroll_code[1]['ID'].",'','".$_REQUEST['TRANSFER']['SCHOOL']."',NULL,$last_school)");
                            }else{   
                                DBQuery("INSERT INTO student_enrollment (SYEAR ,SCHOOL_ID ,STUDENT_ID ,GRADE_ID ,START_DATE ,END_DATE ,ENROLLMENT_CODE ,DROP_CODE ,NEXT_SCHOOL ,CALENDAR_ID ,LAST_SCHOOL) VALUES (".  $syear.",".$_REQUEST['TRANSFER']['SCHOOL'].",".$_REQUEST['student_id'].",'','".$_REQUEST['TRANSFER']['STUDENT_ENROLLMENT_START']."','',".$enroll_code[1]['ID'].",'','".$_REQUEST['TRANSFER']['SCHOOL']."',NULL,$last_school)");
                            }
                            $trans_school=$syear_RET[1]['TITLE'];

                            $trans_student_RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM students WHERE STUDENT_ID='".$_REQUEST['student_id']."'"));

                            $trans_student=$trans_student_RET[1]['LAST_NAME'].' '.$trans_student_RET[1]['FIRST_NAME'];
                            DBQuery('UPDATE medical_info SET SCHOOL_ID='.$_REQUEST['TRANSFER']['SCHOOL'].', SYEAR='.$syear.' WHERE STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                            unset($_REQUEST['modfunc']);
                          //  unset($_SESSION['student_id']);  //pinki
                            unset($_SESSION['_REQUEST_vars']['student_id']);
     // echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&student_id='.$_SESSION['student_id'].'&school_id='.UserSchool().'&transffer=done&trans_school='.$trans_school.'&trans_student='.$trans_student.'"; window.close();</script>';
    echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&student_id='.$_SESSION['student_id'].'&school_id='.UserSchool().'"; window.close();</script>';
                        }
                        else
                        {
                            echo '<SCRIPT language=javascript>window.close();</script>';
                        }

	}
	else
	{
		
		echo '<BR>';
		PopTableforWindow('header',$title);
		$sql = "SELECT ID,TITLE FROM schools WHERE ID !=".UserSchool();
		$QI = DBQuery($sql);
		$schools_RET = DBGet($QI);
		foreach($schools_RET as $school_array){
		$options[$school_array['ID']]=$school_array['TITLE'];
		}
		echo "<FORM name=popform id=popform action=for_window.php?modname=$_REQUEST[modname]&modfunc=detail&student_id=".UserStudentID()."&drop_code=".$_REQUEST['drop_code']." METHOD=POST>";
		echo '<TABLE>';
		echo '<TR><TD>Current school drop date</TD><TD>'.'  '.DateInput_for_EndInput('','TRANSFER[STUDENT_ENROLLMENT_END_DATE]','',$div,true).'</TD></TR>';
		echo '<TR><TD>Transferring to</TD><TD>'.SelectInput('','TRANSFER[SCHOOL]','',$options,false,'class=cell_medium').'</TD></TR>';
		echo '<TR><TD>New school\'s enrollment date</TD><TD>'.'  '.DateInput_for_EndInput('','TRANSFER[STUDENT_ENROLLMENT_START]','',$div,true).'</TD></TR>';
		
		
		
			echo '<TR><TD colspan=2 align=center><INPUT type=submit class=btn_medium name=button value=Save onclick="formload_ajax(\'popform\');">';
			echo '&nbsp;';
			echo '</TD></TR>';			

		echo '</TABLE>';
		PopTableWindow('footer');
		echo '</FORM>';

		unset($_REQUEST['values']);
		unset($_SESSION['_REQUEST_vars']['values']);
		unset($_REQUEST['button']);
		unset($_SESSION['_REQUEST_vars']['button']);
	}
}
elseif (clean_param($_REQUEST['modfunc'],PARAM_NOTAGS)=='lookup') 
{
    if(clean_param($_REQUEST['func'],PARAM_NOTAGS)=='search')
    {
        if($_REQUEST['button']=='Find' || $_REQUEST['nfunc']=='status')
        {
            if($_REQUEST['nfunc']=='status')
            {
                if($_REQUEST['button']=='Select' )
                {
                   $sel_staff = $_REQUEST['staff']; 
                   $people_info=  DBGet(DBQuery('SELECT * FROM people WHERE STAFF_ID='.$sel_staff));
                   $people_info=$people_info[1];
                   $people_address=  DBGet(DBQuery('SELECT * FROM student_address WHERE PEOPLE_ID='.$sel_staff));
                   $people_address=$people_address[1];
                   $people_loginfo=  DBGet(DBQuery('SELECT * FROM login_authentication WHERE USER_ID='.$sel_staff.' AND PROFILE_ID=4'));
                   $people_loginfo=$people_loginfo[1];                   
                   if($_REQUEST['type']=='primary')
                   {
                       if($people_loginfo['USERNAME']!='')    
                       {
                         //  echo '<SCRIPT language=javascript>opener.document.getElementById(\'pri_fname\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'pri_lname\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'pri_hphone\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'pri_wphone\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'pri_cphone\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'pri_email\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'prim_hideShow\').style.display =\'block\';opener.document.getElementById(\'rpn\').checked=true;opener.document.getElementById(\'pri_address\').value=\''.$people_address['ADDRESS'].'\';opener.document.getElementById(\'pri_street\').value=\''.$people_address['STREET'].'\';opener.document.getElementById(\'pri_city\').value=\''.$people_address['CITY'].'\';opener.document.getElementById(\'pri_state\').value=\''.$people_address['STATE'].'\';opener.document.getElementById(\'pri_zip\').value=\''.$people_address['ZIPCODE'].'\';opener.document.getElementById(\'portal_1\').checked=true;opener.document.getElementById(\'portal_div_1\').style.display=\'block\';opener.document.getElementById(\'primary_username\').value=\''.$people_loginfo['USERNAME'].'\';opener.document.getElementById(\'primary_password\').value=\''.$people_loginfo['PASSWORD'].'\';window.close();</script>';
                           if($_REQUEST['address_id']=='new')
                                echo '<SCRIPT language=javascript>opener.document.getElementById(\'values[people][PRIMARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'values[people][PRIMARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'values[people][PRIMARY][HOME_PHONE]\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'values[people][PRIMARY][WORK_PHONE]\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'values[people][PRIMARY][CELL_PHONE]\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'values[people][PRIMARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_1\').style.display=\'block\';opener.document.getElementById(\'portal_1\').checked=true;opener.document.getElementById(\'values[people][PRIMARY][USER_NAME]\').value=\''.$people_loginfo['USERNAME'].'\';var pwd=opener.document.getElementById(\'values[people][PRIMARY][PASSWORD]\'); var pwd2= pwd.cloneNode(false);pwd2.type=\'password\';pwd.parentNode.replaceChild(pwd2,pwd);opener.document.getElementById(\'values[people][PRIMARY][PASSWORD]\').value=\''.$people_loginfo['PASSWORD'].'\';</script>';
                           else
                           {
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'divvalues[people][PRIMARY][FIRST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][FIRST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][PRIMARY][LAST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][LAST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][PRIMARY][EMAIL]\').innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][EMAIL] class=cell_medium size=2 /> \';'
                               . 'var workphone=opener.document.getElementById(\'divvalues[people][PRIMARY][WORK_PHONE]\'); if(workphone!=null) workphone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][WORK_PHONE] class=cell_medium size=2 /> \';'
                               . 'var homephone=opener.document.getElementById(\'divvalues[people][PRIMARY][HOME_PHONE]\'); if(homephone!=null) homephone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][HOME_PHONE] class=cell_medium size=2 /> \';'
                               . 'var cellphone=opener.document.getElementById(\'divvalues[people][PRIMARY][CELL_PHONE]\'); if(cellphone!=null) cellphone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][CELL_PHONE] class=cell_medium size=2 /> \';'
                               . 'var cellphone=opener.document.getElementById(\'divvalues[people][PRIMARY][CELL_PHONE]\'); if(cellphone!=null) cellphone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][CELL_PHONE] class=cell_medium size=2 /> \';'
                               .'</script>';                           
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'inputvalues[people][PRIMARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';'
                               . 'opener.document.getElementById(\'inputvalues[people][PRIMARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';'
                               . 'var home_phone=opener.document.getElementById(\'inputvalues[people][PRIMARY][HOME_PHONE]\'); if(home_phone==null) home_phone=opener.document.getElementById(\'values[people][PRIMARY][HOME_PHONE]\');  home_phone.value=\''.$people_info['HOME_PHONE'].'\';'
                               . 'var work_phone=opener.document.getElementById(\'inputvalues[people][PRIMARY][WORK_PHONE]\'); if(work_phone==null) work_phone=opener.document.getElementById(\'values[people][PRIMARY][WORK_PHONE]\');  work_phone.value=\''.$people_info['WORK_PHONE'].'\';'
                               . 'var cell_phone=opener.document.getElementById(\'inputvalues[people][PRIMARY][CELL_PHONE]\'); if(cell_phone==null) cell_phone=opener.document.getElementById(\'values[people][PRIMARY][CELL_PHONE]\');  cell_phone.value=\''.$people_info['CELL_PHONE'].'\';'
                               . 'opener.document.getElementById(\'inputvalues[people][PRIMARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_1\').style.display=\'block\';'
                               . 'opener.document.getElementById(\'portal_1\').checked=true;opener.document.getElementById(\'values[people][PRIMARY][USER_NAME]\').value=\''.$people_loginfo['USERNAME'].'\';'
                               . 'var pwd=opener.document.getElementById(\'values[people][PRIMARY][PASSWORD]\'); '
                               . 'var pwd2= pwd.cloneNode(false);pwd2.type=\'password\';'
                               . 'pwd.parentNode.replaceChild(pwd2,pwd);'
                               . 'opener.document.getElementById(\'values[people][PRIMARY][PASSWORD]\').value=\''.$people_loginfo['PASSWORD'].'\';</script>';
                           }
                       }
                       else
                       {
                       //    echo '<SCRIPT language=javascript>opener.document.getElementById(\'pri_fname\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'pri_lname\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'pri_hphone\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'pri_wphone\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'pri_cphone\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'pri_email\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'prim_hideShow\').style.display =\'block\';opener.document.getElementById(\'rpn\').checked=true;opener.document.getElementById(\'pri_address\').value=\''.$people_address['ADDRESS'].'\';opener.document.getElementById(\'pri_street\').value=\''.$people_address['STREET'].'\';opener.document.getElementById(\'pri_city\').value=\''.$people_address['CITY'].'\';opener.document.getElementById(\'pri_state\').value=\''.$people_address['STATE'].'\';opener.document.getElementById(\'pri_zip\').value=\''.$people_address['ZIPCODE'].'\';window.close();</script>';
                           if($_REQUEST['address_id']=='new')
                                echo '<SCRIPT language=javascript>opener.document.getElementById(\'values[people][PRIMARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'values[people][PRIMARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'values[people][PRIMARY][HOME_PHONE]\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'values[people][PRIMARY][WORK_PHONE]\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'values[people][PRIMARY][CELL_PHONE]\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'values[people][PRIMARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_1\').style.display=\'none\';opener.document.getElementById(\'portal_1\').checked=false;opener.document.getElementById(\'values[people][PRIMARY][USER_NAME]\').value=\' \';</script>';
                            else
                           {
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'divvalues[people][PRIMARY][FIRST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][FIRST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][PRIMARY][LAST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][LAST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][PRIMARY][EMAIL]\').innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][EMAIL] class=cell_medium size=2 /> \';'
                               . 'var workphone=opener.document.getElementById(\'divvalues[people][PRIMARY][WORK_PHONE]\'); if(workphone!=null) workphone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][WORK_PHONE] class=cell_medium size=2 /> \';'
                               . 'var homephone=opener.document.getElementById(\'divvalues[people][PRIMARY][HOME_PHONE]\'); if(homephone!=null) homephone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][HOME_PHONE] class=cell_medium size=2 /> \';'
                               . 'var cellphone=opener.document.getElementById(\'divvalues[people][PRIMARY][CELL_PHONE]\'); if(cellphone!=null) cellphone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][CELL_PHONE] class=cell_medium size=2 /> \';'
                               . 'var workphone=opener.document.getElementById(\'divvalues[people][PRIMARY][WORK_PHONE]\'); if(workphone!=null) workphone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][WORK_PHONE] class=cell_medium size=2 /> \';'
                               . 'var homephone=opener.document.getElementById(\'divvalues[people][PRIMARY][HOME_PHONE]\'); if(homephone!=null) homephone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][HOME_PHONE] class=cell_medium size=2 /> \';'
                               . 'var cellphone=opener.document.getElementById(\'divvalues[people][PRIMARY][CELL_PHONE]\'); if(cellphone!=null) cellphone.innerHTML=\'<INPUT type=text id=inputvalues[people][PRIMARY][CELL_PHONE] class=cell_medium size=2 /> \';'
                               .'var chk1=opener.document.getElementById(\'checked_1\');  if(chk1!=null) chk1.innerHTML=\'<input type="checkbox" width="25" name="primary_portal" value="Y" id="portal_1" onClick="portal_toggle(1);" /> \' ;'
                               .'var uname1=opener.document.getElementById(\'uname1\'); if(uname1!=null) uname1.innerHTML=\'<INPUT type=text id=values[people][PRIMARY][USER_NAME] class=cell_medium size=2 /> \' ;'
                               .'var pwd1=opener.document.getElementById(\'pwd1\'); if(pwd1!=null) pwd1.innerHTML=\'<INPUT type=text id=values[people][PRIMARY][PASSWORD] class=cell_medium size=2  /> \' '
                               .'</script>';                           
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'inputvalues[people][PRIMARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';'
                               . 'opener.document.getElementById(\'inputvalues[people][PRIMARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';'
                               . 'opener.document.getElementById(\'inputvalues[people][PRIMARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_1\').style.display=\'none\';'
                               . 'var home_phone=opener.document.getElementById(\'inputvalues[people][PRIMARY][HOME_PHONE]\'); if(home_phone==null) home_phone=opener.document.getElementById(\'values[people][PRIMARY][HOME_PHONE]\');  home_phone.value=\''.$people_info['HOME_PHONE'].'\';'
                               . 'var work_phone=opener.document.getElementById(\'inputvalues[people][PRIMARY][WORK_PHONE]\'); if(work_phone==null) work_phone=opener.document.getElementById(\'values[people][PRIMARY][WORK_PHONE]\');  work_phone.value=\''.$people_info['WORK_PHONE'].'\';'
                               . 'var cell_phone=opener.document.getElementById(\'inputvalues[people][PRIMARY][CELL_PHONE]\'); if(cell_phone==null) cell_phone=opener.document.getElementById(\'values[people][PRIMARY][CELL_PHONE]\');  cell_phone.value=\''.$people_info['CELL_PHONE'].'\';'                               
                               . 'opener.document.getElementById(\'portal_1\').checked=false;'
                               . 'opener.document.getElementById(\'values[people][PRIMARY][USER_NAME]\').value=\' \';'                               
                               . 'opener.document.getElementById(\'values[people][PRIMARY][PASSWORD]\').value=\' \';</script>';
                           }
                       }
                   }
                   elseif ($_REQUEST['type']=='secondary') 
                   {
                       if($people_loginfo['USERNAME']!='')   
                       {
                            if($_REQUEST['address_id']=='new')
                                echo '<SCRIPT language=javascript>opener.document.getElementById(\'values[people][SECONDARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'values[people][SECONDARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'values[people][SECONDARY][HOME_PHONE]\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'values[people][SECONDARY][WORK_PHONE]\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'values[people][SECONDARY][CELL_PHONE]\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'values[people][SECONDARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_2\').style.display=\'block\';opener.document.getElementById(\'portal_2\').checked=true;opener.document.getElementById(\'values[people][SECONDARY][USER_NAME]\').value=\''.$people_loginfo['USERNAME'].'\';var pwd=opener.document.getElementById(\'values[people][SECONDARY][PASSWORD]\'); var pwd2= pwd.cloneNode(false);pwd2.type=\'password\';pwd.parentNode.replaceChild(pwd2,pwd);opener.document.getElementById(\'values[people][SECONDARY][PASSWORD]\').value=\''.$people_loginfo['PASSWORD'].'\';</script>';
                            else
                           {
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'divvalues[people][SECONDARY][FIRST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][FIRST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][SECONDARY][LAST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][LAST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][SECONDARY][EMAIL]\').innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][EMAIL] class=cell_medium size=2 /> \';'
                               . 'var workphone=opener.document.getElementById(\'divvalues[people][SECONDARY][WORK_PHONE]\'); if(workphone!=null) workphone.innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][WORK_PHONE] class=cell_medium size=2 /> \';'
                               . 'var homephone=opener.document.getElementById(\'divvalues[people][SECONDARY][HOME_PHONE]\'); if(homephone!=null) homephone.innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][HOME_PHONE] class=cell_medium size=2 /> \';'
                               . 'var cellphone=opener.document.getElementById(\'divvalues[people][SECONDARY][CELL_PHONE]\'); if(cellphone!=null) cellphone.innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][CELL_PHONE] class=cell_medium size=2 /> \';'                               
                               .'</script>';                           
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'inputvalues[people][SECONDARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';'
                               . 'opener.document.getElementById(\'inputvalues[people][SECONDARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';'
                               . 'var home_phone=opener.document.getElementById(\'inputvalues[people][SECONDARY][HOME_PHONE]\'); if(home_phone==null) home_phone=opener.document.getElementById(\'values[people][SECONDARY][HOME_PHONE]\');  home_phone.value=\''.$people_info['HOME_PHONE'].'\';'
                               . 'var work_phone=opener.document.getElementById(\'inputvalues[people][SECONDARY][WORK_PHONE]\'); if(work_phone==null) work_phone=opener.document.getElementById(\'values[people][SECONDARY][WORK_PHONE]\');  work_phone.value=\''.$people_info['WORK_PHONE'].'\';'
                               . 'var cell_phone=opener.document.getElementById(\'inputvalues[people][SECONDARY][CELL_PHONE]\'); if(cell_phone==null) cell_phone=opener.document.getElementById(\'values[people][SECONDARY][CELL_PHONE]\');  cell_phone.value=\''.$people_info['CELL_PHONE'].'\';'                                                              
                              . 'opener.document.getElementById(\'inputvalues[people][SECONDARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_2\').style.display=\'block\';'
                               . 'opener.document.getElementById(\'portal_2\').checked=true;opener.document.getElementById(\'values[people][SECONDARY][USER_NAME]\').value=\''.$people_loginfo['USERNAME'].'\';'
                               . 'var pwd=opener.document.getElementById(\'values[people][PRIMARY][PASSWORD]\'); '
                               . 'var pwd2= pwd.cloneNode(false);pwd2.type=\'password\';'
                               . 'pwd.parentNode.replaceChild(pwd2,pwd);'
                               . 'opener.document.getElementById(\'values[people][SECONDARY][PASSWORD]\').value=\''.$people_loginfo['PASSWORD'].'\';</script>';
                           }
                       }
                       else
                       {
//                           echo '<SCRIPT language=javascript>opener.document.getElementById(\'sec_fname\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'sec_lname\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'sec_hphone\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'sec_wphone\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'sec_cphone\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'sec_email\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'sec_hideShow\').style.display =\'block\';opener.document.getElementById(\'rsn\').checked=true;opener.document.getElementById(\'sec_address\').value=\''.$people_address['ADDRESS'].'\';opener.document.getElementById(\'sec_street\').value=\''.$people_address['STREET'].'\';opener.document.getElementById(\'sec_city\').value=\''.$people_address['CITY'].'\';opener.document.getElementById(\'sec_state\').value=\''.$people_address['STATE'].'\';opener.document.getElementById(\'sec_zip\').value=\''.$people_address['ZIPCODE'].'\';window.close();</script>';
//                            echo '<SCRIPT language=javascript>opener.document.getElementById(\'values[people][PRIMARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';window.close();</script>';
                          if($_REQUEST['address_id']=='new')
                                echo '<SCRIPT language=javascript>opener.document.getElementById(\'values[people][SECONDARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'values[people][SECONDARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'values[people][SECONDARY][HOME_PHONE]\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'values[people][SECONDARY][WORK_PHONE]\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'values[people][SECONDARY][CELL_PHONE]\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'values[people][SECONDARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_2\').style.display=\'none\';opener.document.getElementById(\'portal_2\').checked=false;opener.document.getElementById(\'values[people][PRIMARY][USER_NAME]\').value=\' \';</script>';
                          else
                           {
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'divvalues[people][SECONDARY][FIRST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][FIRST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][SECONDARY][LAST_NAME]\').innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][LAST_NAME] class=cell_medium size=2 /> \';'
                               . 'opener.document.getElementById(\'divvalues[people][SECONDARY][EMAIL]\').innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][EMAIL] class=cell_medium size=2 /> \';' 
                               . 'var workphone=opener.document.getElementById(\'divvalues[people][SECONDARY][WORK_PHONE]\'); if(workphone!=null) workphone.innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][WORK_PHONE] class=cell_medium size=2 /> \';'
                               . 'var homephone=opener.document.getElementById(\'divvalues[people][SECONDARY][HOME_PHONE]\'); if(homephone!=null) homephone.innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][HOME_PHONE] class=cell_medium size=2 /> \';'
                               . 'var cellphone=opener.document.getElementById(\'divvalues[people][SECONDARY][CELL_PHONE]\'); if(cellphone!=null) cellphone.innerHTML=\'<INPUT type=text id=inputvalues[people][SECONDARY][CELL_PHONE] class=cell_medium size=2 /> \';'                                                              
                               .'var chk2=opener.document.getElementById(\'checked_2\'); if(chk2!=null) chk2.innerHTML=\'<input type="checkbox" name="secondary_portal" value="Y" id="portal_2" onClick="portal_toggle(2);" /> \' ;'
                               .'var uname2=opener.document.getElementById(\'uname2\'); if(uname2!=null) uname2.innerHTML=\'<INPUT type=text id=values[people][SECONDARY][USER_NAME] class=cell_medium size=2 /> \' ;'
                               .'var pwd2=opener.document.getElementById(\'pwd2\'); if(pwd2!=null) pwd2.innerHTML=\'<INPUT type=text id=values[people][SECONDARY][PASSWORD] class=cell_medium size=2  /> \' '                               
                               .'</script>';                           
                               echo '<SCRIPT language=javascript>'
                               . 'opener.document.getElementById(\'inputvalues[people][SECONDARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';'
                               . 'opener.document.getElementById(\'inputvalues[people][SECONDARY][LAST_NAME]\').value=\''.$people_info['LAST_NAME'].'\';'
                               . 'opener.document.getElementById(\'inputvalues[people][SECONDARY][EMAIL]\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'portal_div_2\').style.display=\'none\';'
                               . 'var home_phone=opener.document.getElementById(\'inputvalues[people][SECONDARY][HOME_PHONE]\'); if(home_phone==null) home_phone=opener.document.getElementById(\'values[people][SECONDARY][HOME_PHONE]\');  home_phone.value=\''.$people_info['HOME_PHONE'].'\';'
                               . 'var work_phone=opener.document.getElementById(\'inputvalues[people][SECONDARY][WORK_PHONE]\'); if(work_phone==null) work_phone=opener.document.getElementById(\'values[people][SECONDARY][WORK_PHONE]\');  work_phone.value=\''.$people_info['WORK_PHONE'].'\';'
                               . 'var cell_phone=opener.document.getElementById(\'inputvalues[people][SECONDARY][CELL_PHONE]\'); if(cell_phone==null) cell_phone=opener.document.getElementById(\'values[people][SECONDARY][CELL_PHONE]\');  cell_phone.value=\''.$people_info['CELL_PHONE'].'\';'                                                                                            
                               . 'opener.document.getElementById(\'portal_1\').checked=false;'
                               . 'opener.document.getElementById(\'values[people][SECONDARY][USER_NAME]\').value=\' \';'                               
                               . 'opener.document.getElementById(\'values[people][SECONDARY][PASSWORD]\').value=\' \';</script>';
                           }
                       }
                   }
                   else
                   {
                       if($people_loginfo['USERNAME']!='') 
                       {
                           echo '<SCRIPT language=javascript>opener.document.getElementById(\'oth_fname\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'oth_lname\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'oth_hphone\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'oth_wphone\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'oth_cphone\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'oth_email\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'addn_hideShow\').style.display =\'block\';opener.document.getElementById(\'ron\').checked=true;opener.document.getElementById(\'oth_address\').value=\''.$people_address['ADDRESS'].'\';opener.document.getElementById(\'oth_street\').value=\''.$people_address['STREET'].'\';opener.document.getElementById(\'oth_city\').value=\''.$people_address['CITY'].'\';opener.document.getElementById(\'oth_state\').value=\''.$people_address['STATE'].'\';opener.document.getElementById(\'oth_zip\').value=\''.$people_address['ZIPCODE'].'\';'.($people_address['BUS_PICKUP']=='Y' ?'opener.document.getElementById(\'values[student_address][OTHER][BUS_PICKUP]\').checked=true;':'').($people_address['BUS_DROPOFF']=='Y' ?'opener.document.getElementById(\'values[student_address][OTHER][BUS_DROPOFF]\').checked=true;':'').'opener.document.getElementById(\'oth_busno\').value=\''.$people_address['BUS_NO'].'\';opener.document.getElementById(\'portal_2\').checked=true;opener.document.getElementById(\'portal_div_2\').style.display=\'block\';opener.document.getElementById(\'other_username\').value=\''.$people_loginfo['USERNAME'].'\';opener.document.getElementById(\'other_password\').value=\''.$people_loginfo['PASSWORD'].'\';window.close();</script>';
                            echo '<SCRIPT language=javascript>opener.document.getElementById(\'values[people][PRIMARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';window.close();</script>';
                       }
                       else
                       {
                           echo '<SCRIPT language=javascript>opener.document.getElementById(\'oth_fname\').value=\''.$people_info['FIRST_NAME'].'\';opener.document.getElementById(\'oth_lname\').value=\''.$people_info['LAST_NAME'].'\';opener.document.getElementById(\'oth_hphone\').value=\''.$people_info['HOME_PHONE'].'\';opener.document.getElementById(\'oth_wphone\').value=\''.$people_info['WORK_PHONE'].'\';opener.document.getElementById(\'oth_cphone\').value=\''.$people_info['CELL_PHONE'].'\';opener.document.getElementById(\'oth_email\').value=\''.$people_info['EMAIL'].'\';opener.document.getElementById(\'addn_hideShow\').style.display =\'block\';opener.document.getElementById(\'ron\').checked=true;opener.document.getElementById(\'oth_address\').value=\''.$people_address['ADDRESS'].'\';opener.document.getElementById(\'oth_street\').value=\''.$people_address['STREET'].'\';opener.document.getElementById(\'oth_city\').value=\''.$people_address['CITY'].'\';opener.document.getElementById(\'oth_state\').value=\''.$people_address['STATE'].'\';opener.document.getElementById(\'oth_zip\').value=\''.$people_address['ZIPCODE'].'\';'.($people_address['BUS_PICKUP']=='Y' ?'opener.document.getElementById(\'values[student_address][OTHER][BUS_PICKUP]\').checked=true;':'').($people_address['BUS_DROPOFF']=='Y' ?'opener.document.getElementById(\'values[student_address][OTHER][BUS_DROPOFF]\').checked=true;':'').'opener.document.getElementById(\'oth_busno\').value=\''.$people_address['BUS_NO'].'\';window.close();</script>';
                            echo '<SCRIPT language=javascript>opener.document.getElementById(\'values[people][PRIMARY][FIRST_NAME]\').value=\''.$people_info['FIRST_NAME'].'\';window.close();</script>';
                       }
                   }   
                   echo '<SCRIPT language=javascript>window.close();</script>';   print_r($people_info);
                }
                if($_REQUEST['button']=='Cancel' )
                {
                    echo '<SCRIPT language=javascript>window.close();</script>';
                }
            }
            else
            {
                if($_REQUEST['USERINFO_FIRST_NAME'] || $_REQUEST['USERINFO_LAST_NAME'] || $_REQUEST['USERINFO_EMAIL'] || $_REQUEST['USERINFO_MOBILE'] || $_REQUEST['USERINFO_SADD'] || $_REQUEST['USERINFO_CITY'] || $_REQUEST['USERINFO_STATE'] || $_REQUEST['USERINFO_ZIP'])
                {
                    $stf_ids='';
                    $sql='SELECT distinct stf.STAFF_ID AS BUTTON , stf.STAFF_ID,CONCAT(stf.FIRST_NAME," ",stf.LAST_NAME) AS FULLNAME, CONCAT(s.FIRST_NAME," ",s.LAST_NAME) AS STUFULLNAME,stf.PROFILE,stf.EMAIL FROM people stf';
                    $sql_where='WHERE stf.PROFILE_ID=4 ';
                    if($_REQUEST['USERINFO_FIRST_NAME'] || $_REQUEST['USERINFO_LAST_NAME'] || $_REQUEST['USERINFO_EMAIL'] || $_REQUEST['USERINFO_MOBILE'])
                    {
                        if($_REQUEST['USERINFO_FIRST_NAME'])
                            $sql_where.= 'AND LOWER(stf.FIRST_NAME) LIKE \''.str_replace("'","''",strtolower(trim($_REQUEST['USERINFO_FIRST_NAME']))).'%\' ';
                        if($_REQUEST['USERINFO_LAST_NAME'])
                            $sql_where.= 'AND LOWER(stf.LAST_NAME) LIKE \''.str_replace("'","''",strtolower(trim($_REQUEST['USERINFO_LAST_NAME']))).'%\' '; 
                        if($_REQUEST['USERINFO_EMAIL'])
                            $sql_where.= 'AND LOWER(stf.EMAIL) = \''.str_replace("'","''",strtolower(trim($_REQUEST['USERINFO_EMAIL']))).'\' '; 
                        if($_REQUEST['USERINFO_MOBILE'])
                            $sql_where.= 'AND stf.CELL_PHONE = \''.str_replace("'","''",trim($_REQUEST['USERINFO_MOBILE'])).'\' '; 
    //                    if($_REQUEST['USERINFO_USER_NAME'])
    //                    {
    //                        $usr_info=  DBGet(DBQuery('SELECT USER_ID FROM login_authentication WHERE LOWER(USERNAME)=\''.str_replace("'","''",strtolower(trim($_REQUEST['USERINFO_USER_NAME']))).'\' AND PROFILE_ID=3'));
    //                        if(count($usr_info)>0)
    //                            $stf_ids=$usr_info[1]['USER_ID'].',';
    //                           // $sql_where.= 'AND stf.STAFF_ID = '.$usr_info[1]['USER_ID'].' '; 
    //                    }
                    }
                    if($_REQUEST['USERINFO_SADD'] || $_REQUEST['USERINFO_CITY'] || $_REQUEST['USERINFO_STATE'] || $_REQUEST['USERINFO_ZIP'])
                    {
//                        $sql.=',student_address sa';
                          $sql.=' LEFT OUTER JOIN student_address sa on sa.PEOPLE_ID=stf.STAFF_ID';
//                        $sql_where.=' AND sa.PEOPLE_ID=stf.STAFF_ID AND sa.TYPE IN (\'Primary\',\'Secondary\',\'Other\') ';
                        $sql_where.='  AND sa.TYPE IN (\'Primary\',\'Secondary\',\'Other\') ';
    //                    $stud_sql='SELECT s.STUDENT_ID FROM students s,student_enrollment ssm WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR ='.UserSyear();
                        if($_REQUEST['USERINFO_SADD'])
                            $sql_where.= ' AND LOWER(ADDRESS) LIKE \''.str_replace("'","''",strtolower(trim($_REQUEST['USERINFO_SADD']))).'%\' ';
                        if($_REQUEST['USERINFO_CITY'])
                            $sql_where.= ' AND LOWER(CITY) LIKE \''.str_replace("'","''",strtolower(trim($_REQUEST['USERINFO_CITY']))).'%\' ';
                        if($_REQUEST['USERINFO_STATE'])
                            $sql_where.= ' AND LOWER(STATE) LIKE \''.str_replace("'","''",strtolower(trim($_REQUEST['USERINFO_STATE']))).'%\' ';
                        if($_REQUEST['USERINFO_ZIP'])
                            $sql_where.= ' AND ZIPCODE = \''.str_replace("'","''",trim($_REQUEST['USERINFO_ZIP'])).'\' ';
    //                    $ids=  DBGet(DBQuery($stud_sql));
    //                    foreach ($ids as $key => $value) 
    //                    {
    //                        $stud_ids.=$value['STUDENT_ID'].',';
    //                    }
    //                    if($stud_ids)
    //                    {
    //                        $stud_ids=  substr($stud_ids, 0,-1);
    //                        $stf_id=  DBGet(DBQuery('SELECT PERSON_ID AS STAFF_ID FROM students_join_people WHERE STUDENT_ID IN ('.$stud_ids.')'));
    //                        if(count($stf_id))
    //                        {
    //                            foreach ($stf_id as $key => $value) 
    //                            {
    //                                $stf_ids.=$value['STAFF_ID'].',';
    //                            }
    //
    //                        }
    //                    }
                    }
    //                if($stf_ids!='')
    //                {
    //                    $stf_ids= substr($stf_ids, 0, -1);
    //                    $sql_where.= 'AND stf.STAFF_ID IN ('.$stf_ids.') ';
    //                }
//                     $sql.=',students s,students_join_people sju ';
//                     $sql_where.=' AND s.STUDENT_ID = sju.STUDENT_ID AND stf.STAFF_ID=sju.PERSON_ID ';
                     $sql.=' Left outer join students_join_people sju on stf.STAFF_ID=sju.PERSON_ID Left outer join students s on s.STUDENT_ID = sju.STUDENT_ID  ';
                     //$sql_where.=' AND s.STUDENT_ID = sju.STUDENT_ID AND stf.STAFF_ID=sju.PERSON_ID ';
                    // echo $sql.$sql_where;
                     $searched_staffs=  DBGet(DBQuery($sql.$sql_where),array('BUTTON'=>'makeChooseCheckbox')) ;
                     foreach ($searched_staffs as $key => $value) {
                        $stf_usrname=  DBGet(DBQuery('SELECT USERNAME FROM login_authentication WHERE USER_ID='.$value['STAFF_ID'].' AND PROFILE_ID=4'));
                        $searched_staffs[$key]['USERNAME']=$stf_usrname[1]['USERNAME'];
                    }
                }
                else
                {
//                    $sql='SELECT stf.STAFF_ID AS BUTTON , stf.STAFF_ID,CONCAT(stf.FIRST_NAME," ",stf.LAST_NAME) AS FULLNAME, CONCAT(s.FIRST_NAME," ",s.LAST_NAME) AS STUFULLNAME,stf.PROFILE,stf.EMAIL FROM students s,students_join_people sju,people stf
//                          WHERE  s.STUDENT_ID = sju.STUDENT_ID AND stf.STAFF_ID=sju.PERSON_ID';
                     $sql='SELECT stf.STAFF_ID AS BUTTON , stf.STAFF_ID,CONCAT(stf.FIRST_NAME," ",stf.LAST_NAME) AS FULLNAME, CONCAT(s.FIRST_NAME," ",s.LAST_NAME) AS STUFULLNAME,stf.PROFILE,stf.EMAIL FROM people stf left outer join students_join_people sju on stf.STAFF_ID=sju.PERSON_ID left outer join students s on s.STUDENT_ID = sju.STUDENT_ID';
                    $searched_staffs=  DBGet(DBQuery($sql),array('BUTTON'=>'makeChooseCheckbox')) ;
                    foreach ($searched_staffs as $key => $value) {
                        $stf_usrname=  DBGet(DBQuery('SELECT USERNAME FROM login_authentication WHERE USER_ID='.$value['STAFF_ID'].' AND PROFILE_ID=4'));
                        $searched_staffs[$key]['USERNAME']=$stf_usrname[1]['USERNAME'];
                    }

                }
            }
            $singular = 'User';
            $plural = 'Users';
            $options['save'] = false;
            $options['print'] = false;
            $options['search'] =false;
            $columns=array('BUTTON'=>'Select any one','FULLNAME'=>'Name','USERNAME'=>'Username','EMAIL'=>'Email','STUFULLNAME'=>'Associated Student\'s Name');
            echo '<FORM name=sel_staff id=sel_staff action="for_window.php?modname='.$_REQUEST[modname].'&modfunc=lookup&type='.$_REQUEST['type'].'&func=search&nfunc=status&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'" METHOD=POST>';
            echo '<center><span id="sel_err"></span><br/><br/></center>';
            ListOutput($searched_staffs,$columns,$singular,$plural,false,$group=false,$options,'for_window');
            echo '<br>';
            echo '<center>';
            if(count($searched_staffs)>0) echo '<INPUT type=submit name=button value=Select class=btn_medium onclick="return sel_staff_val();">&nbsp;&nbsp;';
            echo '<INPUT type=submit name=button value=Cancel class=btn_medium></center>';
            echo '</form>';
            
        }
        else
            echo '<SCRIPT language=javascript>window.close();</script>';
    }
    else
    {
                echo '<BR>';
		PopTableforWindow('header','');
//		$sql = "SELECT ID,TITLE FROM schools WHERE ID !=".UserSchool();
//		$QI = DBQuery($sql);
//		$schools_RET = DBGet($QI);
//		foreach($schools_RET as $school_array){
//		$options[$school_array['ID']]=$school_array['TITLE'];
//		}
                echo '<center><h3>Search for an existing portal user (parent/guardian) <br/> to associate with this student.</h3>';
                echo '<font color="red">Fill out one or more fields to look up an individual.</font><br/><br/>';
		echo "<FORM name=popform id=popform action=for_window.php?modname=$_REQUEST[modname]&modfunc=lookup&type=".$_REQUEST['type']."&func=search&ajax=".$_REQUEST['ajax']."&address_id=".$_REQUEST['address_id']." METHOD=POST>";
		echo '<TABLE>';
		echo '<TR><TD>First Name</TD><TD>'.'  '.TextInput('','USERINFO_FIRST_NAME','','class=cell_medium',true).'</TD></TR>';
                echo '<TR><TD>Last Name</TD><TD>'.'  '.TextInput('','USERINFO_LAST_NAME','','class=cell_medium',true).'</TD></TR>';
                echo '<TR><TD>Email</TD><TD>'.'  '.TextInput('','USERINFO_EMAIL','','class=cell_medium',true).'</TD></TR>';
                echo '<TR><TD>Mobile Phone</TD><TD>'.'  '.TextInput('','USERINFO_MOBILE','','class=cell_medium',true).'</TD></TR>';
                echo '<TR><TD>Street Address</TD><TD>'.'  '.TextInput('','USERINFO_SADD','','class=cell_medium',true).'</TD></TR>';
		echo '<TR><TD>City</TD><TD>'.'  '.TextInput('','USERINFO_CITY','','class=cell_medium',true).'</TD></TR>';
                echo '<TR><TD>State</TD><TD>'.'  '.TextInput('','USERINFO_STATE','','class=cell_medium',true).'</TD></TR>';
                echo '<TR><TD>Zip</TD><TD>'.'  '.TextInput('','USERINFO_ZIP','','class=cell_medium',true).'</TD></TR>';
		
			echo '<TR><TD align=center>';
			echo '&nbsp;&nbsp;';
			echo '</TD><TD align=center><INPUT type=submit class=btn_medium name=button value=Find onclick="formload_ajax(\'popform\');">&nbsp;&nbsp;<INPUT type=submit class=btn_medium name=button value=Cancel></TD></TR>';			

		echo '</TABLE></center>';
		PopTableWindow('footer');
		echo '</FORM>';
    }
}
else{

/////////////////////////////////////////////////////////////////////////////////

if(!$_REQUEST['include'])
{
    $_REQUEST['include'] = 'General_Info';
    $_REQUEST['category_id'] = '1';
}
elseif(!$_REQUEST['category_id'])
if($_REQUEST['include']=='General_Info')
$_REQUEST['category_id'] = '1';
elseif($_REQUEST['include']=='Address')
$_REQUEST['category_id'] = '3';
elseif($_REQUEST['include']=='Medical')
$_REQUEST['category_id'] = '2';
elseif($_REQUEST['include']=='Comments')
$_REQUEST['category_id'] = '4';
#elseif($_REQUEST['include']=='Food_Service')
#$_REQUEST['category_id'] = '6';
elseif($_REQUEST['include']=='Goal')
$_REQUEST['category_id'] = '5';
elseif($_REQUEST['include']=='Enrollment_Info')
$_REQUEST['category_id'] = '6';
elseif($_REQUEST['include']=='Files')
$_REQUEST['category_id'] = '7';
elseif($_REQUEST['include']!='Other_Info')
{
    $include = DBGet(DBQuery("SELECT ID FROM student_field_categories WHERE INCLUDE='$_REQUEST[include]'"));
    $_REQUEST['category_id'] = $include[1]['ID'];
}
if($_REQUEST['category_id']==3 && !isset($_REQUEST['address_id']))
{
//    $address_id = DBGet(DBQuery("SELECT ADDRESS_ID FROM address WHERE STUDENT_ID='".UserStudentID()."'"));
//    $address_id = $address_id[1]['ADDRESS_ID'];
//    if(count($address_id)>0)
//    $_REQUEST['address_id'] = $address_id;
//    else
//    $_REQUEST['address_id'] = 'new';
    
    $address_id = DBGet(DBQuery("SELECT ID as ADDRESS_ID FROM student_address WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TYPE='Home Address' "));
    $address_id = $address_id[1]['ADDRESS_ID'];
    if(count($address_id)>0)
    $_REQUEST['address_id'] = $address_id;
    else
    $_REQUEST['address_id'] = 'new';
}

if($_REQUEST['category_id']==5 && !isset($_REQUEST['goal_id']))
	{
	$goal_id = DBGet(DBQuery("SELECT GOAL_ID,START_DATE,END_DATE FROM goal WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."'"));
	$goal_id = $goal_id[1]['GOAL_ID'];
	if(count($goal_id)>0)
	$_REQUEST['goal_id'] = $goal_id;
	else
	$_REQUEST['goal_id'] = 'new';
	}
//if(strpos($_REQUEST['modname'],'?include='))
//	$_REQUEST['modname'] = substr($_REQUEST['modname'],0,strpos($_REQUEST['modname'],'?include='));

if(User('PROFILE')!='admin')
{
    if(User('PROFILE')!='student')
    if(User('PROFILE_ID'))
    $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
    else
    $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM staff_exceptions WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
    else
    $can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID='3' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
    if($can_edit_RET)
    $_openSIS['allow_edit'] = true;
}

if($_REQUEST['modfunc']=='update' && AllowEdit())
{ 
    if(count($_REQUEST['month_students']))
    {
        foreach($_REQUEST['month_students'] as $column=>$value)
        {
            $_REQUEST['students'][$column] = $_REQUEST['day_students'][$column].'-'.$_REQUEST['month_students'][$column].'-'.$_REQUEST['year_students'][$column];
//            $date=$_REQUEST['students'][$column];
            if($_REQUEST['students'][$column]=='--')
            {
            $_REQUEST['students'][$column] = '';
            $day_valid=true;
            }
            elseif(!VerifyDate($_REQUEST['students'][$column]))
            {
                $day_valid=false;
                unset($_REQUEST['students']);
                $_REQUEST['students_id']='new';
                $_REQUEST['include']='General_Info';
                echo "<script>window.location.href='Modules.php?modname=Students/Student.php&include=General_Info&student_id=new&err_msg=true'</script>";
                
            }
            else
                $day_valid=true;
        }
    }
    unset($_REQUEST['day_students']); unset($_REQUEST['month_students']); unset($_REQUEST['year_students']);
    if($_REQUEST['student_id'] && $_REQUEST['student_id']!='new')
    {
    $stud_rec=DBGet(DBQuery("SELECT BIRTHDATE,FIRST_NAME,MIDDLE_NAME,LAST_NAME FROM students WHERE 
                            STUDENT_ID=".UserStudentID()));
    if(isset($_REQUEST['students']['BIRTHDATE']))
    {
    $stud_rec[1]['BIRTHDATE']=date('Y-m-d',strtotime($_REQUEST['students']['BIRTHDATE']));
    }
    if(isset($_REQUEST['students']['FIRST_NAME']))
    {
    $stud_rec[1]['FIRST_NAME']=str_replace("'","''",$_REQUEST['students']['FIRST_NAME']);     
    }
    if(isset($_REQUEST['students']['LAST_NAME']))
    {
    $stud_rec[1]['LAST_NAME']=str_replace("'","''",$_REQUEST['students']['LAST_NAME']);
    }
//    $qry="SELECT COUNT(1) AS COUNT FROM students WHERE BIRTHDATE='".$stud_rec[1]['BIRTHDATE']."'
//            AND FIRST_NAME='".$stud_rec[1]['FIRST_NAME']."'
//            AND LAST_NAME='".$stud_rec[1]['LAST_NAME']."' AND STUDENT_ID!='".UserStudentID()."'";
    $qry="SELECT COUNT(1) AS COUNT FROM students s,student_enrollment se WHERE s.BIRTHDATE='".$stud_rec[1]['BIRTHDATE']."'
        AND s.FIRST_NAME='".$stud_rec[1]['FIRST_NAME']."'
        AND s.LAST_NAME='".$stud_rec[1]['LAST_NAME']."' AND s.STUDENT_ID!='".UserStudentID()."' 
        AND se.GRADE_ID=(SELECT GRADE_ID FROM student_enrollment WHERE STUDENT_ID='".UserStudentID()."'
        AND SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND ID=(SELECT MAX(ID) FROM student_enrollment WHERE STUDENT_ID='".UserStudentID()."')) AND se.SYEAR='".UserSyear()."' AND s.STUDENT_ID=se.STUDENT_ID";
    if(isset($_REQUEST['students']['MIDDLE_NAME']))
    {
    $stud_rec[1]['MIDDLE_NAME']=str_replace("'","''",$_REQUEST['students']['MIDDLE_NAME']);
    if($_REQUEST['students']['MIDDLE_NAME']!='')
    $qry.=" AND s.MIDDLE_NAME='".$stud_rec[1]['MIDDLE_NAME']."'";
    }    
    $qry_exec=DBGet(DBQuery($qry));
    unset($qry);
        if($qry_exec[1]['COUNT']>0)
        {   
           $flag=true;
           $n=DuplicateStudent("duplicate records","update");
           if($n==1)
           {
               unset($_REQUEST['delete_ok']);
               $_REQUEST['ajax']=1;
           }
        }
        else
        {
            $flag=false;
            $n=1;
        }
    }
    else
    {
    $n=1;    
    $flag=true;    
    }
    if($n==1)
    {$flag=false;
    if((count($_REQUEST['students']) || count($_REQUEST['values'])) && AllowEdit())
    {
        if($_REQUEST['student_id'] && $_REQUEST['student_id']!='new')
        {
            
			//print_r($_REQUEST['students']);
			//$mycustom_RET=DbGet(DbQuery("select ID,TITLE from custom_fields WHERE TYPE='multiple' AND SYSTEM_FIELD='N'"));
            if(count($_REQUEST['students']))
            {
                            $log_go=false;
                $sql = "UPDATE students SET ";
                $log_sql='UPDATE login_authentication SET ';
                foreach($_REQUEST['students'] as $column_name=>$value)
                {
                                                                                //$value=trim($value);
                                                                                if(substr($column_name,0,6)=='CUSTOM'){
                                    
                                                                                $custom_id=str_replace("CUSTOM_","",$column_name);
                                                                                $custom_RET=DBGet(DBQuery("SELECT TITLE,TYPE FROM custom_fields WHERE ID=".$custom_id));

                                                                                $custom=DBGet(DBQuery("SHOW COLUMNS FROM students WHERE FIELD='".$column_name."'"));
                                                                                $custom=$custom[1];
                                                                                if($custom['NULL']=='NO' && trim($value)=='' && $custom['DEFAULT']){
                                                                                    $value=$custom['DEFAULT'];
                                                                                }else if($custom['NULL']=='NO' && $value==''){
                                                                                    $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                                    echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                                                                    $error=true;
                                                                                }else if($custom_RET[1]['TYPE']=='numeric' &&  (!is_numeric($value) && $value!='')){
                                                                                    $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                                    echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is Numeric type.</b></font><br/>';
                                                                                    $error=true;
                                                                                }else{
                                                                                    $m_custom_RET=DBGet(DBQuery("select ID,TITLE,TYPE from custom_fields WHERE ID='".$custom_id."' AND TYPE='multiple'"));
                                                                                    if($m_custom_RET)
                                                                                    {
                                                                                        $str="";
                                                                                        foreach($value as $m_custom_val)
                                                                                        {
                                                                                            if($m_custom_val)
                                                                                            $str.="||".$m_custom_val;
                                                                                        }
                                                                                        if($str)
                                                                                        $value=$str."||";
                                                                                        else {
                                                                                             $value='';
                                                                                    }
                                                                                    }

                                                                                    /*$c_custom_RET=DBGet(DBQuery("select ID,TITLE,TYPE from custom_fields WHERE ID='".$custom_id."' AND TYPE='codeds'"));
                                                                                    if($c_custom_RET)
                                                                                    {

                                                                                    }*/
                                                                                }  ###Myelse ends#####
					
				}  ###Custom Ends#####
                      
                    $value=paramlib_validation($column_name,trim($value));
                    if($column_name=='PASSWORD' && $value!=''){
                        $log_go=true;
                    if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$log_sql .= "$column_name='".str_replace("'","''",str_replace("`","''",md5(trim($value))))."',";
					}else
					$log_sql .= "$column_name='".str_replace("'","''",str_replace("`","''",md5(trim($value))))."',";
                    }
                    elseif($column_name=='USERNAME' && $value!=''){
                        $log_go=true;
                    if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$log_sql .= "$column_name='".str_replace("'","''",str_replace("`","''",trim($value)))."',";
					}else
					$log_sql .= "$column_name='".str_replace("'","''",str_replace("`","''",trim($value)))."',";
                    }
                    else{
                        if($column_name!='USERNAME')
                        {
                            if($column_name!='PASSWORD')
                            {
					if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$sql .= "$column_name='".str_replace("'","\'",str_replace("`","''",trim($value)))."',";
					}else
                                         $sql .= "$column_name='".str_replace("'","''",str_replace("'`","''",trim($value)))."',";
					
                    }
                }
                                        }
					if($column_name=='IS_DISABLE' && $value!='Y' ){
					DBQuery("UPDATE login_authentication SET FAILED_LOGIN=NULL,LAST_LOGIN=NOW() WHERE USER_ID=".$_REQUEST[student_id]." AND PROFILE_ID=0");
					}
                }
                $sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$_REQUEST[student_id]'";
                $log_sql = substr($log_sql,0,-1) . " WHERE USER_ID='$_REQUEST[student_id]' AND PROFILE_ID=3";
			if(!$error){                
			DBQuery($sql);}
                        if($log_go)
                        {
                            DBQuery($log_sql);
                        }
            }
            if(count($_REQUEST['medical_info']))
            {
                $sql = "UPDATE medical_info SET ";
                foreach($_REQUEST['medical_info'] as $column_name=>$value)
                {
                    $value=paramlib_validation($column_name,trim($value));
                    if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$sql .= "$column_name='".str_replace("'","\'",str_replace("`","''",trim($value)))."',";
					}else
                                        $sql .= "$column_name='".str_replace("'","''",str_replace("'`","''",trim($value)))."',";
                }
                $sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$_REQUEST[student_id]' AND SYEAR=".UserSyear()." AND SCHOOL_ID=".UserSchool()."";
			if(!$error){                
			DBQuery($sql);}
            }
            $stu_enroll_id = DBGet(DBQuery('SELECT MAX(ID) AS M_ID FROM student_enrollment WHERE STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
            if(!$_REQUEST['enrollment_id'])
            $e_id=$stu_enroll_id[1]['M_ID'];
            else
            $e_id=$_REQUEST['enrollment_id'];
            
            if($_REQUEST['month_values']['student_enrollment'][$e_id]['START_DATE']!='' && $_REQUEST['day_values']['student_enrollment'][$e_id]['START_DATE']!='' && $_REQUEST['year_values']['student_enrollment'][$e_id]['START_DATE']!='')
            {
                $mon=$_REQUEST['month_values']['student_enrollment'][$e_id]['START_DATE'];
                $day=$_REQUEST['day_values']['student_enrollment'][$e_id]['START_DATE'];
                $year=$_REQUEST['year_values']['student_enrollment'][$e_id]['START_DATE'];
                if($mon == 'JAN')
                $mon = '01';
                if($mon == 'FEB')
                $mon = '02';
                if($mon == 'MAR')
                $mon = '03';
                if($mon == 'APR')
                $mon = '04';
                if($mon == 'MAY')
                $mon = '05';
                if($mon == 'JUN')
                $mon = '06';
                if($mon == 'JUL')
                $mon = '07';
                if($mon == 'AUG')
                $mon = '08';
                if($mon == 'SEP')
                $mon = '09';
                if($mon == 'OCT')
                $mon = '10';
                if($mon == 'NOV')
                $mon = '11';
                if($mon == 'DEC')
                $mon = '12';
                $_REQUEST['values']['student_enrollment'][$e_id]['START_DATE']=$year.'-'.$mon.'-'.$day;
                unset($mon);
                unset($year);
                unset($day);
            }
            if($_REQUEST['month_values']['student_enrollment'][$e_id]['END_DATE']!='' && $_REQUEST['day_values']['student_enrollment'][$e_id]['END_DATE']!='' && $_REQUEST['year_values']['student_enrollment'][$e_id]['END_DATE']!='')
            {
                $mon=$_REQUEST['month_values']['student_enrollment'][$e_id]['END_DATE'];
                $day=$_REQUEST['day_values']['student_enrollment'][$e_id]['END_DATE'];
                $year=$_REQUEST['year_values']['student_enrollment'][$e_id]['END_DATE'];
                if($mon == 'JAN')
                $mon = '01';
                if($mon == 'FEB')
                $mon = '02';
                if($mon == 'MAR')
                $mon = '03';
                if($mon == 'APR')
                $mon = '04';
                if($mon == 'MAY')
                $mon = '05';
                if($mon == 'JUN')
                $mon = '06';
                if($mon == 'JUL')
                $mon = '07';
                if($mon == 'AUG')
                $mon = '08';
                if($mon == 'SEP')
                $mon = '09';
                if($mon == 'OCT')
                $mon = '10';
                if($mon == 'NOV')
                $mon = '11';
                if($mon == 'DEC')
                $mon = '12';
                $_REQUEST['values']['student_enrollment'][$e_id]['END_DATE']=$year.'-'.$mon.'-'.$day;
                unset($mon);
                unset($year);
                unset($day);
            }
            if( $_REQUEST['values']['student_enrollment'][$e_id]['END_DATE']!='')
            {
                if($_REQUEST['values']['student_enrollment'][$e_id]['START_DATE']!='' && $_REQUEST['values']['student_enrollment'][$e_id]['START_DATE']>$_REQUEST['values']['student_enrollment'][$e_id]['END_DATE'])
                {
                unset($_REQUEST['values']['student_enrollment'][$e_id]['END_DATE']);
                unset($_REQUEST['values']['student_enrollment'][$e_id]['START_DATE']);
                }
                if($_REQUEST['values']['student_enrollment'][$e_id]['START_DATE']=='')
                {
                    $get_sd=DBGet(DBQuery('SELECT START_DATE FROM student_enrollment WHERE ID='.$e_id));
                    if($_REQUEST['values']['student_enrollment'][$e_id]['END_DATE']<=$get_sd[1]['START_DATE'])
                    unset($_REQUEST['values']['student_enrollment'][$e_id]['END_DATE']);    
                }    
            }
            
                
            if(count($_REQUEST['values']['student_enrollment'][$e_id]))
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
                        $st_cp_id=implode(',',$cp_id);
                        $sql='SELECT MAX(SCHOOL_DATE) AS SCHOOL_DATE FROM attendance_period WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID IN ('.$st_cp_id.')';
                        $attendence=DBGet(DBQuery($sql));
                        $max_at_dt=$attendence[1]['SCHOOL_DATE'];
                    }  
                $sql = "UPDATE student_enrollment SET ";
                foreach($_REQUEST['values']['student_enrollment'][$e_id] as $column_name=>$value)
                {
                    if($column_name=='START_DATE' || $column_name=='END_DATE')
                    {
                    $sql .= "$column_name='".str_replace("\'","''",date('Y-m-d',strtotime($value)))."',";
                    $error=false;
                    }
                    elseif($column_name=='GRADE_ID')
                    {
                        if($value!='')
                        {
                        $sql .= "$column_name='".str_replace("\'","''",str_replace('&#39;',"''",$value))."',";
                        $error=false;
                        }
                        else
                        {
                        $msg='Grade cannot be blank';    
                        $error=true;   
                        }   
                    }
                    else
                    {
                    $sql .= "$column_name='".str_replace("\'","''",str_replace('&#39;',"''",$value))."',";
                    $error=false;
                    }
                }
                $sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$_REQUEST[student_id]' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'";
				if(!$error){
                 if($_REQUEST['values']['student_enrollment'][$e_id]['END_DATE']!='')   
                 {
                        if(strtotime($_REQUEST['values']['student_enrollment'][$e_id]['END_DATE'])>=strtotime($max_at_dt))                
                            DBQuery($sql);
                 }
                 elseif(!$_REQUEST['values']['student_enrollment'][$e_id]['END_DATE'] && $_REQUEST['values']['student_enrollment'][$e_id]['DROP_CODE']!='')
                 {
                     if(strtotime(date('Y-m-d'))>=strtotime($max_at_dt))                
                            DBQuery($sql);
                 }
                 else
                     DBQuery($sql);
                
                }
                


            }
            if($msg)
                echo '<font style="color:red"><b>'.$msg.'</b></font>';
        }
        else
        {
            if($_REQUEST['assign_student_id'])
            {
                $student_id = $_REQUEST['assign_student_id'];
                if(count(DBGet(DBQuery("SELECT STUDENT_ID FROM students WHERE STUDENT_ID='$student_id'"))))
                BackPrompt('That Student ID is already taken. Please select a different one.');
            }
            else
            {
                do
                {
                   //  $student_id = DBGet(DBQuery('SELECT '.db_seq_nextval('STUDENTS_SEQ').' AS STUDENT_ID '.FROM_DUAL));
                    $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'students'"));
                    $student_id[1]['STUDENT_ID']= $id[1]['AUTO_INCREMENT'];
                    $student_id = $student_id[1]['STUDENT_ID'];
                }
                while(count(DBGet(DBQuery("SELECT STUDENT_ID FROM students WHERE STUDENT_ID='$student_id'"))));
            }

            $sql = "INSERT INTO students ";
            $log_sql = 'INSERT INTO login_authentication ';
            $fields = '';
            $values = "";
            $log_fields = 'PROFILE_ID,USER_ID,';
            $log_values = '3,'.$student_id.',';
            foreach($_REQUEST['students'] as $column=>$value)
            {
				//$value=trim($value);
                                                                        if(substr($column,0,6)=='CUSTOM'){
                                                                         $custom_id=str_replace("CUSTOM_","",$column);
                                                                         $custom_RET=DBGet(DBQuery("SELECT TITLE,TYPE FROM custom_fields WHERE ID=".$custom_id));

                                                                        $custom=DBGet(DBQuery("SHOW COLUMNS FROM students WHERE FIELD='".$column."'"));
				$custom=$custom[1];
				if($custom['NULL']=='NO' && $value=='' && !$custom['DEFAULT']){
				$custom_TITLE=$custom_RET[1]['TITLE'];
                                                                            $required_faild_error=true;
                                                                            echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                                                            $error=true;
				}elseif($custom_RET[1]['TYPE']=='numeric' &&  (!is_numeric($value) && $value!='')){
                                                                            $type_faild_error=true;
                                                                            $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                            echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is Numeric type.</b></font><br/>';
                                                                            $error=true;
                                                                        }else{
                                                                            $m_custom_RET=DBGet(DBQuery("select ID,TITLE,TYPE from custom_fields WHERE ID='".$custom_id."' AND TYPE='multiple'"));
                                                                            if($m_custom_RET)
                                                                            {
                                                                                    $str="";
                                                                                    foreach($value as $m_custom_val)
                                                                                    {
                                                                                        if($m_custom_val)
                                                                                            $str.="||".$m_custom_val;
                                                                                    }
                                                                                    if($str)
                                                                                    $value=$str."||";
                                                                                    else
                                                                                        $value='';
                                                                            }
				
				}
                                                                }
                if($value)
                {
                  $value= paramlib_validation($column,trim($value));
                  if(strtoupper($column)!='PASSWORD' && strtoupper($column)!='USERNAME')
                    $fields .= $column.',';
                  if(strtoupper($column)=='PASSWORD' || strtoupper($column)=='USERNAME')
                    $log_fields .= $column.',';
                    if($column=='PASSWORD'){

                        if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                            $log_values .= "'".str_replace("'","''",md5(trim($value)))."',";
                        }else
                        $log_values .= "'".str_replace("'","''",md5(trim($value)))."',";
                    }
                  if(strtoupper($column)=='USERNAME')
                        $log_values .= "'".str_replace("'","''",$value)."',";
                  else{
                      if($column!='PASSWORD'){
                        if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                             $values .= "'".str_replace("'","\'",trim($value))."',";
                         }else
                         $values .= "'".str_replace("'","''",trim($value))."',";
                      }
                    }
                }
            }
            $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			
		$log_sql .= '(' . substr($log_fields,0,-1) . ') values(' . substr($log_values,0,-1) . ')';	
			$un = $_REQUEST['students']['USERNAME'];
			
                        if($un!='')
                        {
			$un_chk = "SELECT COUNT(*) FROM login_authentication WHERE username = '$un'";
			$res_chk = mysql_query($un_chk);
			$row_chk = mysql_fetch_array($res_chk);
                        }
			if($row_chk[0] > 0)
			{ $un_chl_res = 'exist'; }
			
			
//			$un_chk_user = "SELECT COUNT(*) FROM staff WHERE username = '$un'";
//			$res_chk_user = mysql_query($un_chk_user);
//			$row_chk_user = mysql_fetch_array($res_chk_user);
//			
//			if($row_chk_user[0] > 0)
//			{ $un_chl_res = 'exist'; }
			
			
				
			if(!$error){
				
				if($un_chl_res != 'exist' &&  $day_valid!=false)
				{
                                    
				DBQuery($sql);
                                DBQuery($log_sql);
				$max_stId=DBGet(DBQuery('SELECT MAX(STUDENT_ID) AS STU_ID FROM students'));
				$query='INSERT INTO address(STUDENT_ID) VALUES('.$max_stId[1]['STU_ID'].') ';
				DBQuery($query);
				$query='INSERT INTO students_join_address(STUDENT_ID) VALUES('.$max_stId[1]['STU_ID'].') ';
				DBQuery($query);
                                DBQuery('INSERT INTO medical_info (STUDENT_ID,SYEAR,SCHOOL_ID) VALUES ('.$max_stId[1]['STU_ID'].','.UserSyear().','.UserSchool().')');
                                $_SESSION['total_stu']=$_SESSION['total_stu']+1;
				}
				#$_REQUEST['student_id'] = 'new';
				
				
				}elseif($error==true){
				$error_new_student=true;
				}
			   // $studentemrollment_id = DBGet(DBQuery('SELECT '.db_seq_nextval('STUDENT_ENROLLMENT_SEQ').' AS STUDENTENROLL_ID '.FROM_DUAL));
					$id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'student_enrollment'"));
					$studentemrollment_id[1]['STUDENTENROLL_ID']= $id[1]['AUTO_INCREMENT'];
					$studentemrollment_id = $studentemrollment_id[1]['STUDENTENROLL_ID'] ;
	
	
				$sql = "INSERT INTO student_enrollment ";
				$fields = 'STUDENT_ID,SYEAR,SCHOOL_ID,';
				$values = "'$student_id','".UserSyear()."','".UserSchool()."',";
	
				if($_REQUEST['day_values'])
				$_REQUEST['values']['student_enrollment']['new']['START_DATE'] = $_REQUEST['day_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['month_values']['student_enrollment']['new']['START_DATE'].'-'.$_REQUEST['year_values']['student_enrollment']['new']['START_DATE'];
				else
				$_REQUEST['values']['student_enrollment']['new']['START_DATE'] = '';
	
				foreach($_REQUEST['values']['student_enrollment']['new'] as $column=>$value)
				{
					if($value)
					{
                                                $value= paramlib_validation($column,$value);
						
						if($column=='START_DATE' || $column=='END_DATE') 
                                                {
                                                    if(VerifyDate($value))
                                                        $values .= "'".date('Y-m-d',strtotime($value))."',";
                                                    else
                                                    {
                                                        $err="Invalid Enrollment date could not be saved";
                                                        continue;
                                                    }
                                                        
                                                }
						else
						$values .= "'".str_replace("\'","''",str_replace('&#39;',"''",$value))."',";
                                                $fields .= $column.',';
					}
				}
				$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
				if(!$error){
				if($un_chl_res != 'exist' && $day_valid!=false){
				DBQuery($sql);
				}
			}
			
		
		
            if($required_faild_error==true || $type_faild_error==true)
            {
                $_REQUEST['student_id']='new';
                unset ($value);
            }
            if($openSISModules['Food_Service'])
            {
                // create default food service account for this student
                #$sql = "INSERT INTO FOOD_SERVICE_ACCOUNTS (ACCOUNT_ID,BALANCE,TRANSACTION_ID) values('$student_id','0.00','0')";
                #DBQuery($sql);

                // associate with default food service account and assign other defaults
                #$sql = "INSERT INTO FOOD_SERVICE_STUDENT_ACCOUNTS (STUDENT_ID,DISCOUNT,BARCODE,ACCOUNT_ID) values('$student_id','','','$student_id')";
                #DBQuery($sql);
            }
            if(!$error_new_student){
                if($un_chl_res != 'exist'){
                    $_SESSION['student_id'] = $_REQUEST['student_id'] = $student_id;
                }
                else
                {
                    $_REQUEST['student_id'] = "new";
                    unset($value);
                    echo "<font color=red><b>User name already exist. Please try with a different user name.</b></font>";
                }
            }
            $new_student = true;
        }
    }

    if($_REQUEST['values'] && $_REQUEST['include']=='Medical')
    // SaveData(array('STUDENT_MEDICAL_ALERTS'=>"ID='__ID__'",'STUDENT_MEDICAL'=>"ID='__ID__'",'STUDENT_MEDICAL_VISITS'=>"ID='__ID__'",'fields'=>array('STUDENT_MEDICAL'=>'ID,STUDENT_ID,','STUDENT_MEDICAL_ALERTS'=>'ID,STUDENT_ID,','STUDENT_MEDICAL_VISITS'=>'ID,STUDENT_ID,'),'values'=>array('STUDENT_MEDICAL'=>db_seq_nextval('STUDENT_MEDICAL_SEQ').",'".UserStudentID()."',",'STUDENT_MEDICAL_ALERTS'=>db_seq_nextval('STUDENT_MEDICAL_ALERTS_SEQ').",'".UserStudentID()."',",'STUDENT_MEDICAL_VISITS'=>db_seq_nextval('STUDENT_MEDICAL_VISITS_SEQ').",'".UserStudentID()."',")));
    // possible error 5.11.2009
    //SaveData(array('STUDENT_MEDICAL_ALERTS'=>"ID='__ID__'",'STUDENT_MEDICAL'=>"ID='__ID__'",'STUDENT_MEDICAL_VISITS'=>"ID='__ID__'",'fields'=>array('STUDENT_ID,STUDENT_ID,STUDENT_ID,'),'values'=>array("'".UserStudentID()."','".UserStudentID()."','".UserStudentID()."',")));
    SaveData(array('student_medical_notes'=>"ID='__ID__'",'student_medical_alerts'=>"ID='__ID__'",'student_immunization'=>"ID='__ID__'",'student_medical_visits'=>"ID='__ID__'",'fields'=>array('student_medical_notes'=>'STUDENT_ID,','student_immunization'=>'STUDENT_ID,','student_medical_alerts'=>'STUDENT_ID,','student_medical_visits'=>'STUDENT_ID,'),'values'=>array('student_medical_notes'=>"'".UserStudentID()."',",'student_immunization'=>"'".UserStudentID()."',",'student_medical_alerts'=>"'".UserStudentID()."',",'student_medical_visits'=>"'".UserStudentID()."',")));
     if($_REQUEST['values'] && $_REQUEST['include']=='Comments')
    SaveData(array('student_mp_comments'=>"ID='__ID__'",'fields'=>array('student_mp_comments'=>'STUDENT_ID,SYEAR,MARKING_PERIOD_ID,STAFF_ID,'),'values'=>array('student_mp_comments'=>"'".UserStudentID()."','".UserSyear()."','".UserMP()."','".User('STAFF_ID')."',")));
   
    if($_REQUEST['include']!='General_Info' && $_REQUEST['include']!='Address' && $_REQUEST['include']!='Medical' &&  $_REQUEST['include']!='Goal' && $_REQUEST['include']!='Other_Info'  && $_REQUEST['include']!='Enrollment_Info' && $_REQUEST['include']!='Files' )
    if(!strpos($_REQUEST['include'],'/'))
    include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
    else
    include('modules/'.$_REQUEST['include'].'.inc.php');

    unset($_REQUEST['modfunc']);
    // SHOULD THIS BE HERE???
    if(!UserStudentID())
    unset($_REQUEST['values']);
    unset($_SESSION['_REQUEST_vars']['modfunc']);
    unset($_SESSION['_REQUEST_vars']['values']);
}

}
if($flag!=true)
{
if($err)
    echo '<center><font color=red><b>'.$err.'</b></font></center>';
if($_REQUEST['student_id']=='new')
DrawBC('Students > Add a Student');
else
DrawBC("Students > ".ProgramTitle());

Search('student_id');

/*$Select=" AND LOWER(s.FIRST_NAME) LIKE '".strtolower($_REQUEST['first'])."%' AND LOWER(s.LAST_NAME) LIKE '".strtolower($_REQUEST['last'])."%' AND ssm.STUDENT_ID = '".$_REQUEST['stuid']."' AND s.ALT_ID = '$_REQUEST[altid]' AND ssm.GRADE_ID = '".$_REQUEST['grade']."' ";*/
if($_REQUEST['stuid'])
	{
		$select .= " AND ssm.STUDENT_ID = '".str_replace("'","\'",$_REQUEST[stuid])."' ";
		
	}
   if($_REQUEST['altid'])
	{
		$select .= " AND s.ALT_ID = '".str_replace("'","\'",$_REQUEST[altid])."' ";
		
	}
   if($_REQUEST['last'])
	{
		$select .= " AND LOWER(s.LAST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['last'])))."%' ";
		
	}
   if($_REQUEST['first'])
	{
		$select .= " AND LOWER(s.FIRST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['first'])))."%' ";
		
	}
	if($_REQUEST['grade'])
	{
		$select .= " AND ssm.GRADE_ID IN(SELECT id FROM school_gradelevels WHERE title= '".str_replace("'","\'",$_REQUEST[grade])."') ";
		
	}
	if($_REQUEST['addr'])
		{
		$select .= " AND (LOWER(a.ADDRESS) LIKE '%".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.CITY) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.STATE)='".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."' OR ZIPCODE LIKE '".trim(str_replace("'","\'",$_REQUEST['addr']))."%')";
		
	}
	
	
	if($_REQUEST['mp_comment'])
	{
		$select .= " AND LOWER(smc.COMMENT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['mp_comment']))."%' AND s.STUDENT_ID=smc.STUDENT_ID ";
	}
	if($_REQUEST['goal_title'])
	{
		$select .= " AND LOWER(g.GOAL_TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_title']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
	}
		if($_REQUEST['goal_description'])
	{
		$select .= " AND LOWER(g.GOAL_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_description']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
	}
		if($_REQUEST['progress_name'])
	{
		$select .= " AND LOWER(p.PROGRESS_NAME) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_name']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
	}
	if($_REQUEST['progress_description'])
	{
		$select .= " AND LOWER(p.PROGRESS_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_description']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
	}
	if($_REQUEST['doctors_note_comments'])
	{
		$select .= " AND LOWER(smn.DOCTORS_NOTE_COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['doctors_note_comments']))."%' AND s.STUDENT_ID=smn.STUDENT_ID ";
	}
	if($_REQUEST['type'])
	{
		$select .= " AND LOWER(sm.TYPE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['type']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
	}
	if($_REQUEST['imm_comments'])
	{
		$select .= " AND LOWER(sm.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['imm_comments']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
	}
	if($_REQUEST['imm_day']&& $_REQUEST['imm_month']&& $_REQUEST['imm_year'])
	{
$imm_date=$_REQUEST['imm_year'].'-'.$_REQUEST['imm_month'].'-'.$_REQUEST['imm_day'];
		$select .= " AND sm.MEDICAL_DATE ='".date('Y-m-d',strtotime($imm_date))."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	}elseif($_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'])
	{
		if($_REQUEST['imm_day']){
		$select .= " AND SUBSTR(sm.MEDICAL_DATE,9,2) ='".$_REQUEST['imm_day']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$imm_date.=" Day :".$_REQUEST['imm_day'];
		}
		if($_REQUEST['imm_month']){
		$select .= " AND SUBSTR(sm.MEDICAL_DATE,6,2) ='".$_REQUEST['imm_month']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$imm_date.=" Month :".$_REQUEST['imm_month'];
		}
		if($_REQUEST['imm_year']){
		$select .= " AND SUBSTR(sm.MEDICAL_DATE,1,4) ='".$_REQUEST['imm_year']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$imm_date.=" Year :".$_REQUEST['imm_year'];
		}
	}
	if($_REQUEST['med_day']&&$_REQUEST['med_month']&&$_REQUEST['med_year'])
	{
$med_date=$_REQUEST['med_year'].'-'.$_REQUEST['med_month'].'-'.$_REQUEST['med_day'];
		$select .= " AND smn.DOCTORS_NOTE_DATE ='".date('Y-m-d',strtotime($med_date))."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	}elseif($_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
	if($_REQUEST['med_day']){
	$select .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,9,2) ='".$_REQUEST['med_day']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Day :".$_REQUEST['med_day'];
	}
	if($_REQUEST['med_month']){
	$select .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,6,2) ='".$_REQUEST['med_month']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Month :".$_REQUEST['med_month'];
	}
	if($_REQUEST['med_year']){
	$select .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,1,4) ='".$_REQUEST['med_year']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Year :".$_REQUEST['med_year'];
	}
	}
	if($_REQUEST['ma_day']&&$_REQUEST['ma_month']&&$_REQUEST['ma_year'])
	{
$ma_date=$_REQUEST['ma_year'].'-'.$_REQUEST['ma_month'].'-'.$_REQUEST['ma_day'];
		$select .= " AND sma.ALERT_DATE ='".date('Y-m-d',strtotime($ma_date))."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	}elseif($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
	if($_REQUEST['ma_day']){
	$select .= " AND SUBSTR(sma.ALERT_DATE,9,2) ='".$_REQUEST['ma_day']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Day :".$_REQUEST['ma_day'];
	}
	if($_REQUEST['ma_month']){
	$select .= " AND SUBSTR(sma.ALERT_DATE,6,2) ='".$_REQUEST['ma_month']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Month :".$_REQUEST['ma_month'];
	}
	if($_REQUEST['ma_year']){
	$select .= " AND SUBSTR(sma.ALERT_DATE,1,4) ='".$_REQUEST['ma_year']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Year :".$_REQUEST['ma_year'];
	}
	}
	if($_REQUEST['nv_day']&&$_REQUEST['nv_month']&&$_REQUEST['nv_year'])
	{
$nv_date=$_REQUEST['nv_year'].'-'.$_REQUEST['nv_month'].'-'.$_REQUEST['nv_day'];
		$select .= " AND smv.SCHOOL_DATE ='".date('Y-m-d',strtotime($nv_date))."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}elseif($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
	if($_REQUEST['nv_day']){
	$select .= " AND SUBSTR(smv.SCHOOL_DATE,9,2) ='".$_REQUEST['nv_day']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Day :".$_REQUEST['nv_day'];
	}
	if($_REQUEST['nv_month']){
	$select .= " AND SUBSTR(smv.SCHOOL_DATE,6,2) ='".$_REQUEST['nv_month']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Month :".$_REQUEST['nv_month'];
	}
	if($_REQUEST['nv_year']){
	$select .= " AND SUBSTR(smv.SCHOOL_DATE,1,4) ='".$_REQUEST['nv_year']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Year :".$_REQUEST['nv_year'];
	}
	}
	
	
	if($_REQUEST['med_alrt_title'])
	{
		$select .= " AND LOWER(sma.TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_alrt_title']))."%' AND s.STUDENT_ID=sma.STUDENT_ID ";
	}
	if($_REQUEST['reason'])
	{
		$select .= " AND LOWER(smv.REASON) LIKE '".str_replace("'","\'",strtolower($_REQUEST['reason']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}
	if($_REQUEST['result'])
	{
		$select .= " AND LOWER(smv.RESULT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['result']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}
	if($_REQUEST['med_vist_comments'])
	{
		$select .= " AND LOWER(smv.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_vist_comments']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
	}
	if($_REQUEST['day_to_birthdate']&&$_REQUEST['month_to_birthdate']&&$_REQUEST['day_from_birthdate']&&$_REQUEST['month_from_birthdate'])
	{
	$date_to=$_REQUEST['month_to_birthdate'].'-'.$_REQUEST['day_to_birthdate'];
	$date_from=$_REQUEST['month_from_birthdate'].'-'.$_REQUEST['day_from_birthdate'];
		$select .= " AND (SUBSTR(s.BIRTHDATE,6,2) BETWEEN ".$_REQUEST['month_from_birthdate']." AND ".$_REQUEST['month_to_birthdate'].") ";
		$select .= " AND (SUBSTR(s.BIRTHDATE,9,2) BETWEEN ".$_REQUEST['day_from_birthdate']." AND ".$_REQUEST['day_to_birthdate'].") ";
	}

   if(User('PROFILE')=='admin')
	{	
	   $admin_COMMON_FROM=" FROM students s, address a,student_enrollment ssm ";
           if($_REQUEST['_search_all_schools']=='Y' || $_SESSION['_search_all']==1)
           {
//               if(User('SCHOOLS'))
			$admin_COMMON_WHERE=" WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID IN (".GetUserSchools(UserID(),true).") ";
                $_SESSION['_search_all']=1;
           }
           else 
           {
                 $admin_COMMON_WHERE=" WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
           }
	   if($_REQUEST['mp_comment'] || $_SESSION['smc'])
		{
			$admin_COMMON_FROM .=" ,student_mp_comments smc";
			$admin_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		  if($_REQUEST['goal_description'] || $_REQUEST['goal_title'] || $_SESSION['g'])
		{
			$admin_COMMON_FROM .=" ,goal g ";
			$admin_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  if($_REQUEST['progress_name'] || $_REQUEST['progress_description'] || $_SESSION['p'])
		{
			$admin_COMMON_FROM .=" ,progress p ";
			$admin_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		  if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year'] || $_SESSION['smn'])
		{
			$admin_COMMON_FROM .=" ,student_medical_notes smn ";
			$admin_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  if($_REQUEST['type'] || $_REQUEST['imm_comments'] || $_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'] || $_SESSION['sm'])
		{
//                      $admin_COMMON_FROM .=" ,student_medical sm ";
			$admin_COMMON_FROM .=" ,student_immunization sm ";
			$admin_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		  if($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year'] || $_REQUEST['med_alrt_title'] || $_SESSION['sma'])
		{
			$admin_COMMON_FROM .=" ,student_medical_alerts sma  ";
			$admin_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		  if($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year'] || $_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments'] || $_SESSION['smv'])
		{
			$admin_COMMON_FROM .=" ,student_medical_visits smv   ";
			$admin_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$admin_COMMON= $admin_COMMON_FROM . $admin_COMMON_WHERE;
		
	}
	/////////////////////////////////// Teacher section ///////////////////////////////////
	if(User('PROFILE')=='teacher')
	{
		   $teacher_COMMON_FROM=" FROM students s, student_enrollment ssm, course_periods cp,
	schedule ss,address a ";
	   $teacher_COMMON_WHERE=" WHERE a.STUDENT_ID=s.STUDENT_ID AND s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
						AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
		
           if($_REQUEST['_search_all_schools']=='Y' || $_SESSION['_search_all']==1)
           {
//               if(User('SCHOOLS'))
			$teacher_COMMON_WHERE=" WHERE a.STUDENT_ID=s.STUDENT_ID AND s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
						AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID IN (".GetUserSchools(UserID(),true).") ";
                $_SESSION['_search_all']=1;
           }
           else 
           {
                 $teacher_COMMON_WHERE=" WHERE a.STUDENT_ID=s.STUDENT_ID AND s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
						AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
           }
	   if($_REQUEST['mp_comment'] || $_SESSION['smc'])
		{
			$teacher_COMMON_FROM .=" ,student_mp_comments smc";
			$teacher_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		  if($_REQUEST['goal_description'] || $_REQUEST['goal_title'] || $_SESSION['g'])
		{
			$teacher_COMMON_FROM .=" ,goal g ";
			$teacher_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  if($_REQUEST['progress_name'] || $_REQUEST['progress_description'] || $_SESSION['p'])
		{
			$teacher_COMMON_FROM .=" ,progress p ";
			$teacher_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		  if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year'] || $_SESSION['smn'])
		{
			$teacher_COMMON_FROM .=" ,student_medical_notes smn ";
			$teacher_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  if($_REQUEST['type'] || $_REQUEST['imm_comments'] || $_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'] || $_SESSION['sm'])
		{
			$teacher_COMMON_FROM .=" ,student_immunization sm ";
			$teacher_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		  if($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year'] || $_REQUEST['med_alrt_title'] || $_SESSION['sma'])
		{
			$teacher_COMMON_FROM .=" ,student_medical_alerts sma  ";
			$teacher_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		  if($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year'] || $_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments'] || $_SESSION['smv'])
		{
			$teacher_COMMON_FROM .=" ,student_medical_visits smv   ";
			$teacher_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$teacher_COMMON= $teacher_COMMON_FROM . $teacher_COMMON_WHERE;
 }
	
	////////////////////////////////// End Of Teacher Section /////////////////////////////
	
	
	
	
	
	if(!UserStudentID())
	{
	#$sql="SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID FROM students s, address a, student_enrollment ssm WHERE s.STUDENT_ID=ssm.STUDENT_ID ".$select;
	if(User('PROFILE')=='admin')
	{
		$sql="SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$admin_COMMON_FROM.$admin_COMMON_WHERE.$select;
	}
	elseif(User('PROFILE')=='teacher')
	{
			$sql="SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$teacher_COMMON_FROM.$teacher_COMMON_WHERE.$select;

	}
	
	$val=DBGet(DBQuery($sql));

	if($val[1]['STUDENT_ID']>1 && !$_SESSION['stu_search']['sql'])
        {
            unset($_SESSION['s']);
            unset($_SESSION['custom_count_sql']);
            unset($_SESSION['inactive_stu_filter']);
        }

	}
	if(!$_SESSION['s'])
	{
	   	$_SESSION['s']=$select;
        }

        if($_SESSION['inactive_stu_filter'])
        {
            $_SESSION['s'] .= $_SESSION['inactive_stu_filter'];
        }
   #$admin_COMMON=" FROM students s, address a,student_enrollment ssm WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";

/*  $teacher_COMMON="  FROM students s, student_enrollment ssm, course_periods cp,
schedule ss
 WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND (ssm.START_DATE IS NOT NULL AND ('".DBDate()."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
*/					
	if($_REQUEST['v'] && isset($_REQUEST['student_id']))
	{
		$val=$_REQUEST['v'];
		if($val==1)
		 {
		 	if(User('PROFILE')=='admin')
			 {
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1 "));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1 "));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		elseif($val==2)
		 {
			if(User('PROFILE')=='admin')
			 {
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID) <'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 	$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$teacher_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID) <'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		elseif($val==3)
		 {
			if(User('PROFILE')=='admin')
			 {
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID)>'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$teacher_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID) >'".$ln."' ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		 elseif($val==4)
		 {
		 	if(User('PROFILE')=='admin')
			 {
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
	 }
 

if(UserStudentID() || $_REQUEST['student_id']=='new')
{
	if($_REQUEST['student_id']!='new')
	{
		if(User('PROFILE')=='admin')
		  {
                  
		$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()."  ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			
		$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
                if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                $ln=  str_replace("'","\'",$ln);
                }else{
                    $ln=str_replace("'","\'",$ln);
                }
		
		$s1_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
		$s2_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
		$count_STU=DBGet(DBQuery("SELECT COUNT(LAST_NAME) AS STUDENT ".$admin_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID)<'".$ln."' AND LAST_NAME LIKE '".strtolower($_REQUEST['last'])."%'".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		$count=$count_STU[1]['STUDENT'] + 1;
		$total=DBGet(DBQuery("SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$admin_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		  }
		  elseif(User('PROFILE')=='teacher')
		   {
		   
		   	/*$total=DBGet(DBQuery("SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID FROM students s, student_enrollment ssm, course_periods cp,
schedule ss
 WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND (ssm.START_DATE IS NOT NULL AND ('".DBDate()."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ".$_SESSION['s']));*/
			
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME,FIRST_NAME,s.STUDENT_ID ".$teacher_COMMON." AND s.STUDENT_ID ='".UserStudentID()."'  ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
			
		$ln = $s_ln[1]['LAST_NAME'].$s_ln[1]['FIRST_NAME'].$s_ln[1]['STUDENT_ID'];
		
		$s1_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) ASC LIMIT 1"));
		
		$s2_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON.$_SESSION['s']." ".$_SESSION['custom_count_sql']." ORDER BY CONCAT(s.LAST_NAME, s.FIRST_NAME,s.STUDENT_ID) DESC LIMIT 1"));
		
		$count_STU=DBGet(DBQuery("SELECT COUNT(LAST_NAME) AS STUDENT ".$teacher_COMMON." AND CONCAT(LAST_NAME,FIRST_NAME,s.STUDENT_ID)<'".$ln."' AND LAST_NAME LIKE '".strtolower($_REQUEST['last'])."%'".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		$count=$count_STU[1]['STUDENT']+1;
				
			$total=DBGet(DBQuery("SELECT COUNT(s.STUDENT_ID) AS STUDENT_ID ".$teacher_COMMON." ".$_SESSION['s']." ".$_SESSION['custom_count_sql']));
		   }
		  
		 echo '<br/>';
		 if(User('PROFILE')=='admin' || User('PROFILE')=='teacher')
		  {
				echo "<div style='text-align:right; padding-left:10px;'><table width='100%' cellpadding='0' cellspacing='0'><tr><td align='right'>";
				echo "<div style='margin-right:15px; font-weight:bold; font-size:14px;'>"."Showing ".$count." of ".$_SESSION['total_stu']."</div>";
				echo "</td><td align='right' width='250px' style='padding-top:4px;'>";
                                echo '<div style="margin-right:15px; margin-bottom:8px;">';
				if($total[1]['STUDENT_ID']>1)
				{
				 if(UserStudentID()!=$s1_id[1]['STUDENT_ID'])
				 {
					echo "<span class='pg-prev' style='margin-right:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=1&student_id=".UserStudentID()." >&laquo; First</A></span>";
					//echo '&nbsp;&nbsp;&nbsp;';
					echo "<span class='pg-prev' style='margin-right:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=2&student_id=".UserStudentID()." >&lsaquo; Previous</A></span>";
				 }
				 if(UserStudentID()!=$s2_id[1]['STUDENT_ID'])
				 {
					//echo '&nbsp;&nbsp;&nbsp;';
					echo "<span class='pg-nxt' style='margin-left:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=3&student_id=".UserStudentID()." >Next &rsaquo;</A></span>";
					//echo '&nbsp;&nbsp;&nbsp;';
					echo "<span class='pg-nxt' style='margin-left:10px; font-size:14px; font-weight:normal;'><A HREF=Modules.php?modname=Students/Student.php&v=4&student_id=".UserStudentID()." >Last &raquo;</A></span>";
				 }
			}
			echo "</div></td></tr></table></div>";
	  	 }
		 
		 
	}
	
	/*echo "<A HREF=Modules.php?modname=Students/Student.php> Next >></A>";*/
    if($_REQUEST['modfunc']!='delete' || $_REQUEST['delete_ok']=='1')
    {
	
        if($_REQUEST['student_id']!='new')
        {
            $sql = "SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.NAME_SUFFIX,la.USERNAME,la.PASSWORD,la.LAST_LOGIN,s.IS_DISABLE,s.ESTIMATED_GRAD_DATE,s.GENDER,s.ETHNICITY,s.COMMON_NAME,s.BIRTHDATE,s.LANGUAGE,s.ALT_ID,s.EMAIL,s.PHONE,(SELECT SCHOOL_ID FROM student_enrollment WHERE SYEAR='".UserSyear()."' AND STUDENT_ID=s.STUDENT_ID ORDER BY START_DATE DESC,END_DATE DESC LIMIT 1) AS SCHOOL_ID,
                        (SELECT GRADE_ID FROM student_enrollment WHERE SYEAR='".UserSyear()."' AND STUDENT_ID=s.STUDENT_ID ORDER BY START_DATE DESC,END_DATE DESC LIMIT 1) AS GRADE_ID
                    FROM students s , login_authentication la
                    WHERE s.STUDENT_ID='".UserStudentID()."' AND s.STUDENT_ID=la.USER_ID AND la.PROFILE_ID=3";
            $QI = DBQuery($sql);
            $student = DBGet($QI);
            $student = $student[1];
            $stu_Medical_info=  DBGet(DBQuery('SELECT PHYSICIAN,PHYSICIAN_PHONE,PREFERRED_HOSPITAL FROM medical_info WHERE STUDENT_ID='.UserStudentID().' AND SYEAR='.UserSyear().' AND SCHOOL_ID='.UserSchool().''));
            $student+=$stu_Medical_info[1];
            $school = DBGet(DBQuery("SELECT SCHOOL_ID,GRADE_ID FROM student_enrollment WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND ('".DBDate()."' BETWEEN START_DATE AND END_DATE OR END_DATE IS NULL)"));
            $_REQUEST['modname'] = str_replace('?student_id=new','',$_REQUEST['modname']);
            echo "<FORM name=student enctype='multipart/form-data' action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&student_id=".UserStudentID()."&modfunc=update method=POST>";
        }
        else
        echo "<FORM id=student_isertion enctype='multipart/form-data' name=student action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=update method=POST>";
		
        $name = $student['FIRST_NAME'].' '.$student['MIDDLE_NAME'].' '.$student['LAST_NAME'].' '.$student['NAME_SUFFIX'];

        if($_REQUEST['student_id']!='new')
        $name .= ' - '.$student['STUDENT_ID'];
        

        if(User('PROFILE')!='student')
        if(User('PROFILE_ID')!='')
        $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
        else
        $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM staff_exceptions WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
        else
        $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID='3' AND CAN_USE='Y'"),array(),array('MODNAME'));
        $categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM student_field_categories ORDER BY SORT_ORDER,TITLE"));

        foreach($categories_RET as $category)
        {
            if($can_use_RET['Students/Student.php&category_id='.$category['ID']])
            {
                if($category['ID']=='1')
                $include = 'General_Info';
                elseif($category['ID']=='3')
                $include = 'Address';
                elseif($category['ID']=='2')
                $include = 'Medical';
                elseif($category['ID']=='4')
                $include = 'Comments';
               # elseif($category['ID']=='5')
               # $include = 'Food_Service';
                elseif($category['ID']=='5')
				$include = 'Goal';
                elseif($category['ID']=='6')
                $include = 'Enrollment_Info';
                 elseif($category['ID']=='7')
                $include = 'Files';
                elseif($category['INCLUDE'])
                $include = $category['INCLUDE'];
                else
                $include = 'Other_Info';

                $tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&include=$include&category_id=".$category['ID']);
            }
        }
        unset($new_tabs);
        unset($ti);
        unset($td);
        $swap_tabs='n';
        foreach($tabs as $ti=>$td)
        {
            if($td['title']=='Enrollment Info')
                $swap_tabs='y';
        }
        if($swap_tabs=='y')
        {
        foreach($tabs as $ti=>$td)
        {
            if($td['title']=='General Info')
               $new_tabs[0]=$td;
            elseif($td['title']=='Enrollment Info')
               $new_tabs[1]=$td;
            else
                $new_tabs[$ti+1]=$td;
        }
        }
//        print_r($new_tabs);
        echo '<br><br>';
        if(count($new_tabs))
        {
        unset($tabs);
        $tabs=$new_tabs;
        ksort($tabs);
        }
//        print_r($tabs);
        
        unset($new_tabs);
        unset($ti);
        unset($td);
        $swap_tabs='n';
        $_openSIS['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]";
        if($_REQUEST['category_id'])
        $_openSIS['selected_tab'] .= '&category_id='.$_REQUEST['category_id'];

        echo '<BR>';
        echo PopTable('header',$tabs,'');

        if(!strpos($_REQUEST['include'],'/'))
        include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
        else
        {
            include('modules/'.$_REQUEST['include'].'.inc.php');
            $separator = '<HR>';
            include('modules/Students/includes/Other_Info.inc.php');
        }
        echo PopTable('footer');

      if(isset($_REQUEST['goal_id']) && $_REQUEST['goal_id'] != 'new' && !isset($_REQUEST['progress_id']))
       echo '<CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER>';
   else
   {
            echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_student_student();"').'</CENTER>';
   }
        echo '</FORM>';
    }
    else
    if(!strpos($_REQUEST['include'],'/'))
    include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
    else
    {
		
        include('modules/'.$_REQUEST['include'].'.inc.php');
        $separator = '<div class=break></div>';
        include('modules/Students/includes/Other_Info.inc.php');
    }
}
}

}
}
function makeChooseCheckbox($value,$title)
{        global $THIS_RET;
         if($THIS_RET['BUTTON'])
         {
            return "<INPUT type=radio name=staff value=".$THIS_RET['BUTTON']."><input type=hidden name=sel_staff_id value=".$THIS_RET['BUTTON'].">";
         }

}
?>
