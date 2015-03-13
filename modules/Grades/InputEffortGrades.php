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
DrawBC("Grades >> ".ProgramTitle());

$mp_RET=DBGet(DBQuery('SELECT MP FROM course_periods WHERE course_period_id = \''.UserCoursePeriod().'\''));
if($mp_RET[1]['MP']=='SEM')
{
    $sem = GetParentMP('SEM',UserMP());

    $pros = GetChildrenMP('PRO',UserMP());
}
if($mp_RET[1]['MP']=='FY')
{
    $sem = GetParentMP('SEM',UserMP());
    if($sem)
        $fy = GetParentMP('FY',$sem);
    else
        $fy = GetParentMP('FY',UserMP());

    $pros = GetChildrenMP('PRO',UserMP());
}
if($mp_RET[1]['MP']=='QTR')
{
    $pros = GetChildrenMP('PRO',UserMP());
}

// if the UserMP has been changed, the REQUESTed MP may not work
if(!$_REQUEST['mp'] || strpos($str="'".UserMP()."','".$sem."','".$fy."',".$pros,"'".ltrim($_REQUEST['mp'],'E')."'")===false)
	$_REQUEST['mp'] = UserMP();


//echo $_REQUEST['mp'];
//$mps_select = "<SELECT name=mp onChange='this.form.submit();'>";
$mps_select = "<SELECT name=mp onChange=\"window.location='Modules.php?modname=".$_REQUEST['modname']."&mp=' + this.options[this.selectedIndex].value;\">";
if($pros!='')
	foreach(explode(',',str_replace("'",'',$pros)) as $pro)
	{
		if($_REQUEST['mp']==$pro && GetMP($pro,'POST_START_DATE') && ($time>=strtotime(GetMP($pro,'POST_START_DATE')) && $time<=strtotime(GetMP($pro,'POST_END_DATE'))))
			$allow_edit = true;
		if(GetMP($pro,'DOES_GRADES')=='Y')
			$mps_select .= "<OPTION value=".$pro.(($pro==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($pro)."</OPTION>";
	}

if($_REQUEST['mp']==UserMP() && GetMP(UserMP(),'POST_START_DATE') && ($time>=strtotime(GetMP(UserMP(),'POST_START_DATE')) && $time<=strtotime(GetMP(UserMP(),'POST_END_DATE'))))
	$allow_edit = true;
$mps_select .= "<OPTION value=".UserMP().((UserMP()==$_REQUEST['mp'])?' SELECTED':'').">".GetMP(UserMP())."</OPTION>";

if(($_REQUEST['mp']==$sem || $_REQUEST['mp']=='E'.$sem) && GetMP($sem,'POST_START_DATE') && ($time>=strtotime(GetMP($sem,'POST_START_DATE')) && $time<=strtotime(GetMP($sem,'POST_END_DATE'))))
	$allow_edit = true;
if(GetMP($sem,'DOES_GRADES')=='Y')
	$mps_select .= "<OPTION value=$sem".(($sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)."</OPTION>";
//if(GetMP($sem,'DOES_EXAM')=='Y')
//	$mps_select .= "<OPTION value=E$sem".(('E'.$sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)." Exam</OPTION>";

if(($_REQUEST['mp']==$fy || $_REQUEST['mp']=='E'.$fy) && GetMP($fy,'POST_START_DATE') && ($time>=strtotime(GetMP($fy,'POST_START_DATE')) && $time<=strtotime(GetMP($fy,'POST_END_DATE'))))
	$allow_edit = true;
if(GetMP($fy,'DOES_GRADES')=='Y')
	$mps_select .= "<OPTION value=".$fy.(($fy==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($fy)."</OPTION>";
//if(GetMP($fy,'DOES_EXAM')=='Y')
//	$mps_select .= "<OPTION value=E".$fy.(('E'.$fy==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($fy)." Exam</OPTION>";

$mps_select .= '</SELECT>';
#####################################for effort grade portion######################
if($_REQUEST['modfunc']=='enter_standards')
{  
    //////////////////////////////// Grade Status Starts /////////////////////////
$grade_start_date=DBGet(DBQuery('SELECT `POST_START_DATE` FROM `marking_periods` WHERE `marking_period_id`='.UserMP()));
		$grade_end_date=DBGet(DBQuery('SELECT `POST_END_DATE` FROM `marking_periods` WHERE `marking_period_id`='.UserMP()));
                if($grade_start_date[1]['POST_START_DATE']!='')
		$grade_start_time=strtotime( $grade_start_date[1]['POST_START_DATE']);
                if($grade_end_date[1]['POST_END_DATE']!='')
		$grade_end_time=strtotime( $grade_end_date[1]['POST_END_DATE']);
		$current_time= strtotime(date("Y-m-d"));
        if($grade_start_time && $grade_end_time)
        {    
        if($current_time >=$grade_start_time && $current_time<=$grade_end_time){
		$grade_status='open';
		}else if($current_time >= $grade_end_time){
		$grade_status='closed';
		}else if($current_time <= $grade_start_time){
		$grade_status='not open yet';
		}
        }
        else
        {
           $grade_status='not_set'; 
        }    
  if($grade_status =='closed'){
echo '<FONT COLOR=red>Grade reporting ended for this marking period on : '.date("M d, Y ",strtotime( $grade_end_date[1]['POST_END_DATE'])).'</FONT>';
}
elseif($grade_status =='not_set'){
 echo '<FONT COLOR=red>Grade reporting date has not set for this marking period.</FONT>';
}
//////////////////////////////// Grade Status Ends /////////////////////////
  //  print_r($_REQUEST);  
    if($_REQUEST['student_efforts'])
    {
        if($_REQUEST['process']=='add')
        {
            foreach($_REQUEST['student_efforts'] as $key=>$value)
            {
              DBQuery("INSERT INTO student_efforts (STUDENT_ID,MARKING_PERIOD_ID,EFFORT_VALUE,GRADE_VALUE)
              VALUES($_REQUEST[student_id],'".$_REQUEST['mp']."',$key,'".$value['GRADE_ID']."')");
            }
        echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&mp='.$_REQUEST['mp'].'"; window.close();</script>';          
        }
        elseif($_REQUEST['process']=='edit') 
        {
           foreach($_REQUEST['student_efforts'] as $key=>$value)
           {
            $check_stu_effort=DBGet(DBQuery("SELECT ID FROM student_efforts WHERE STUDENT_ID=$_REQUEST[student_id] AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND EFFORT_VALUE=$key "));
            if(count($check_stu_effort)>0)
              DBQuery("UPDATE student_efforts SET GRADE_VALUE='".$value['GRADE_ID']."' WHERE STUDENT_ID=$_REQUEST[student_id] AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND EFFORT_VALUE=$key ");
            else
              DBQuery("INSERT INTO student_efforts (STUDENT_ID,MARKING_PERIOD_ID,EFFORT_VALUE,GRADE_VALUE) VALUES ($_REQUEST[student_id],'".$_REQUEST['mp']."',$key,'".$value['GRADE_ID']."') ");  
           }
      echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&mp='.$_REQUEST['mp'].'"; window.close();</script>';     
        }
    }    
    
    
    
    $_openSIS['allow_edit']=true;
    //-----effort grade scale----
     $effort_grade_scales_RET=DBGet(DBQuery('SELECT * FROM effort_grade_scales WHERE SCHOOL_ID="'.UserSchool().'" AND SYEAR="'.UserSyear().'" '));
     foreach ($effort_grade_scales_RET as $effort_key => $effort_value) {
     $effort_grade_scales[$effort_value['ID']]=$effort_value['VALUE'];  
     }
  
   $standards_ret = DBGet(DBQuery('SELECT eg.ID AS ID,eg.TITLE AS TITLE,egc.TITLE AS EFFORT_CATEGORY,"" AS EFFORT_GRADE_SCALE FROM effort_grades eg,effort_grade_categories egc WHERE eg.EFFORT_CAT=egc.ID AND eg.SCHOOL_ID="'.UserSchool().'" AND eg.SYEAR="'.UserSyear().'" ORDER BY egc.SORT_ORDER '),array('EFFORT_GRADE_SCALE'=>'_makeGradeForEffort'));
   $standards_columns = array('EFFORT_CATEGORY'=>'Effort Category','TITLE'=>'Effort Title','EFFORT_GRADE_SCALE'=>'Value');
   if(count($standards_ret)!=0)
   {    
   echo "<FORM name=effort id=effort action=for_window.php?modname=$_REQUEST[modname]&modfunc=enter_standards&process=$_REQUEST[process]&student_id=$_REQUEST[student_id]&mp=$_REQUEST[mp] method=POST>";
   ListOutput($standards_ret,$standards_columns,'Effort','Efforts',false,false,array('search'=>false,'sort'=>false));
   echo '<br>';
   if($grade_status !='closed' && $grade_status !='not_set'){
   echo "<br><CENTER><INPUT class='btn_medium' type=submit value=Save onclick=formload_ajax('popform') >&nbsp;<INPUT class='btn_medium' type=button value=Close onclick='window.close()'></CENTER>";
    }
   echo "</FORM>";
   }
   else
    echo "No effort found";   
 
}
else
{  
    echo '<table align="" style="margin:5px 0 0 15px; width:100%"><tr><td>';
 echo $mps_select;
 echo '</td></tr></table>';
 $extra['SELECT'] .= ",'' AS EFFORT ";
 $extra['functions'] = array('EFFORT'=>'_makeStandards');

$all_students = GetStuList($extra);
$columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','EFFORT'=>'Effort Grades');

//print_r($all_students);
echo "<center>";
PopTable_wo_header ('header');
echo '<div style="width:800px; overflow-x:scroll;">';
ListOutput($all_students,$columns,'Student','Students',false,false,array('yscroll'=>true));
echo '</div>';
PopTable ('footer');
echo "</center>";
}

function _makeStandards($value,$column)
{ 
       // global $THIS_RET,$current_RET,$import_comments_RET,$course_RET,$tabindex;
        global $THIS_RET;
        $standard_exists=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL FROM student_efforts WHERE STUDENT_ID=".$THIS_RET['STUDENT_ID']." AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' "));
        #echo '<pre>'; print_r($course_RET);echo '</pre>';$course_RET[1]['COURSE_ID']
        if($standard_exists[1]['TOTAL']>0){
        return "<center><a href=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=enter_standards&process=edit&student_id=".$THIS_RET['STUDENT_ID']."&mp=".$_REQUEST['mp']."\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><img src='assets/edit.png' title='Edit' /></a></center>";
        }else {
        return "<center><a href=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=enter_standards&process=add&student_id=".$THIS_RET['STUDENT_ID']."&mp=".$_REQUEST['mp']."\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><img src='assets/add.png' title='Add' /></a></center>";
        }
}

function _makeGradeForEffort($value,$column)
{
  global $THIS_RET,$effort_grade_scales;
  $student_effort=DBGet(DBQuery('SELECT GRADE_VALUE FROM student_efforts WHERE STUDENT_ID="'.$_REQUEST[student_id].'" AND MARKING_PERIOD_ID="'.$_REQUEST['mp'].'" AND EFFORT_VALUE="'.$THIS_RET[ID].'" '));
  return SelectInput($student_effort[1]['GRADE_VALUE'],"student_efforts[$THIS_RET[ID]][GRADE_ID]",'',$effort_grade_scales,'N/A','')." <br/>";
}



?>
