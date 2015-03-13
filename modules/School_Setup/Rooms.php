<?php
#**************************************************************************
#  redPen is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  redPen is  web-based, open source, and comes packed with features that 
#  include student demographic info, Timetable, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the redPen web site at http://www.redPen.com to learn more.
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
    if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']) && AllowEdit())
    {
        foreach($_REQUEST['values'] as $id=>$columns)
        {
            $title='';
            if(!(isset($columns['TITLE']) && trim($columns['TITLE'])==''))
            {
                ##############################################################################################################
                    if($id!='new')
                    {
                        $sql = "UPDATE rooms SET ";

                        foreach($columns as $column=>$value)
                        {
                            $value=trim(paramlib_validation($column,$value));
                             if($column=='TITLE')
                                    {
                                        $title=str_replace("\'","''",$value);
                                    }
                            $sql .= $column."='".str_replace("\'","''",$value)."',";
                        }
                        $sql = substr($sql,0,-1) . " WHERE room_id='$id'";
                        //echo $sql.'<br>';
                        $sql = str_replace('&amp;', "", $sql);
                        $sql = str_replace('&quot', "", $sql);
                        $sql = str_replace('&#039;', "", $sql);
                        $sql = str_replace('&lt;', "", $sql);
                        $sql = str_replace('&gt;', "", $sql);
                         $validate_title=DBGet(DBQuery('SELECT *  FROM rooms WHERE  TITLE=\''.$title.'\''));
    if(count($validate_title)!=0)
    {
        $samedata=  DBGet(DBQuery("select TITLE from rooms  WHERE room_id='$id'"));
        $samedata=$samedata[1]['TITLE'];
        if($samedata!=$title)
            echo "<font color='red'><b>Unable to save data, because title already exists.</b></font>";
    }
    else{
                        DBQuery($sql);
    }

                    }
                    else
                    {
                            $sql = "INSERT INTO rooms ";
                            $fields = 'SCHOOL_ID,';
                            $values = "'".UserSchool()."',";
                            $go = 0;
                            foreach($columns as $column=>$value)
                            {   
                                    if($column=='TITLE')
                                    {
                                        $title=str_replace("\'","''",$value);
                                    }
                                $value=trim(paramlib_validation($column,$value));
                                $fields .= $column.',';
                                $values .= "'".str_replace("\'","''",$value)."',";
                                $go = true;
                                
                            }
                            $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                              $validate_title=DBGet(DBQuery('SELECT *  FROM rooms WHERE  TITLE=\''.$title.'\''));
    if(count($validate_title)!=0)
    {
        echo "<font color='red'><b>Unable to save data, because title already exists.</b></font>";
    }
    else{

                if($go)
                    DBQuery($sql);
    }
                    }
            }
        }
    }

DrawBC("School Setup > ".ProgramTitle());
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove' && AllowEdit())
{
                  $room_id=paramlib_validation($colmn=PERIOD_ID,$_REQUEST[id]);
	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM course_period_var WHERE room_id='$room_id'"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	if($has_assigned>0){
                        $qs='Modules.php?modname=School_Setup/Rooms.php';
                        UnableDeletePromptMod('Cannot delete because room are associated.','delete',$qs);
	}else{
                        $qs='Modules.php?modname=School_Setup/Rooms.php';
                        if(DeletePromptMod('room',$qs))
                        {
                            DBQuery("DELETE FROM rooms WHERE room_id='$room_id'");
                            unset($_REQUEST['modfunc']);
                        }
	}
}

if($_REQUEST['modfunc']!='remove')
{
$sql = "SELECT  ROOM_ID,TITLE,TITLE as NAME,CAPACITY,DESCRIPTION,SORT_ORDER FROM rooms WHERE school_id='".UserSchool()."' ORDER BY sort_order";
	$QI = DBQuery($sql);
$room_ids='';
$room_iv='';
$rooms_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','CAPACITY'=>'_makeIntInput','DESCRIPTION'=>'_makeTextInput','SORT_ORDER'=>'_makeIntInput'));
$columns = array('TITLE'=>'Title','CAPACITY'=>'Capacity','DESCRIPTION'=>'Description','SORT_ORDER'=>'Sort Order');
$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'CAPACITY'=>_makeTextInput('','CAPACITY'),'DESCRIPTION'=>_makeTextInput('','DESCRIPTION'),'SORT_ORDER'=>_makeTextInput('','SORT_ORDER'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'ROOM_ID');
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
//	foreach($rooms_RET as $ri=>$rd)
//        {
//            $room_ids[]=$rd['ROOM_ID'];
//            $room_iv[]=$rd['ROOM_ID'].'_'.trim($rd['NAME']);
//        }
//        $room_ids=implode(",",$room_ids);
//        $room_iv=implode(",",$room_iv);
//        echo '<input type=hidden id=room_ids value='.$room_ids.' />';
//        echo '<input type=hidden id=room_iv value=\''.$room_iv.'\' />';
        $count_room=count($rooms_RET);
        if($count_room>0)
        {
            $count_room=DBGet(DBQuery("Select max(ROOM_ID) as maxid FROM rooms"));
            $count_room=$count_room[1]['MAXID'];           
        }
        echo "<input type=hidden id=count_room value=$count_room />";
        ListOutput($rooms_RET,$columns,'Room','Rooms',$link);
	echo '<br><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="return formcheck_rooms();"').'</CENTER>';
	echo '</FORM>';
}

function _makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ROOM_ID'])
		$id = $THIS_RET['ROOM_ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
		$extra = 'size=5 maxlength=10  class=cell_floating  id='.$name.'_'.$id.'';
		else # added else for the first textbox merlinvicki
                {
                    //$extra = 'class=cell_floating id='.$name.'_'.$id.' ';
                    $extra = 'class=cell_floating id='.$name.'_'.$id.' ';
                    if($id!="new")
                    $extra .= ' onkeyup=\"fill_rooms(this,'.$id.');\" ';
                }
                  if($name=='SORT_ORDER')
                      $extra = ' size=5 maxlength=10 class=cell_floating id='.$name.'_'.$id.' onkeydown="return numberOnly(event);"';
                  if($name=='CAPACITY')
                      $extra = ' size=5 maxlength=10 class=cell_floating id='.$name.'_'.$id.' onkeydown="return numberOnly(event);"';
	if($name=='DESCRIPTION')
                        $extra = 'size=30';
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}
function _makeIntInput($value,$name)
{
                global $THIS_RET;
                if($THIS_RET['ROOM_ID'])
                    $id = $THIS_RET['ROOM_ID'];
                else
                    $id = 'new';  
                if($value!='')
                    $extra = 'size=5 maxlength=10 class=cell_floating onkeydown=\"return numberOnly(event);\"';
                else
                    $extra = 'size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';

                return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}
?>
