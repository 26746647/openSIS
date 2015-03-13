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
include 'modules/Grades/DeletePromptX.fnc.php';
//echo '<pre>'; var_dump($_REQUEST); echo '</pre>';
DrawBC("Gradebook > ".ProgramTitle());

    if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
    {
        foreach($_REQUEST['values'] as $id=>$columns)
        {
            if(!(isset($columns['GRADE']) && trim($columns['GRADE'])==''))
            {
                    if($id!='new')
                    {
                        $sql = "UPDATE standard_grades SET ";

                        foreach($columns as $column=>$value)
                        {
                            $value=trim(paramlib_validation($column,$value));
                            $sql .= $column."='".str_replace("\'","''",$value)."',";
                        }
                        $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                        //echo $sql.'<br>';
                        $sql = str_replace('&amp;', "", $sql);
                        $sql = str_replace('&quot', "", $sql);
                        $sql = str_replace('&#039;', "", $sql);
                        $sql = str_replace('&lt;', "", $sql);
                        $sql = str_replace('&gt;', "", $sql);
                        DBQuery($sql);

                    }
                    else
                    {
                            $sql = "INSERT INTO standard_grades ";
                            $fields = 'SCHOOL_ID,SYEAR,';
                            $values = "'".UserSchool()."','".UserSyear()."',";
                            $go = 0;
                            foreach($columns as $column=>$value)
                            {
                                if(trim($value))
                                {
                                $value=trim(paramlib_validation($column,$value));
                                $fields .= $column.',';
                                $values .= "'".str_replace("\'","''",$value)."',";
                                $go = true;
                                }
                            }
                            $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                            if($go)
                                DBQuery($sql);
                    }
            }
        }
        unset($_REQUEST['modfunc']);
    }

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove')
{

	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM student_standards WHERE grade_id=$_REQUEST[id]"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	
	if($has_assigned>0){
	UnableDeletePromptX('Cannot delete because standard grade is associated.');
	}else{
		if(DeletePromptX('Standard Grade'))
		{
			DBQuery("DELETE FROM standard_grades WHERE ID='$_REQUEST[id]'");
			unset($_SESSION['GR_scale_id']);
		}
		}
}

if(!$_REQUEST['modfunc'])
{
                            $sql = "SELECT ID,GRADE,DESCRIPTION,SORT_ORDER FROM standard_grades WHERE school_id='".  UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY sort_order,grade";
                            $functions = array('GRADE'=>'_makeTextInput',
                            'DESCRIPTION'=>'_makeTextInput',
                            'SORT_ORDER'=>'_makeIntInput');
		$LO_columns = array('GRADE'=>'Grade',
                            'DESCRIPTION'=>'Description',
                            'SORT_ORDER'=>'Order');

                        $link['add']['html'] = array('GRADE'=>_makeTextInput('','GRADE'),'DESCRIPTION'=>_makeTextInput('','DESCRIPTION'),'SORT_ORDER'=>_makeTextInput('','SORT_ORDER'),);
                        $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
                        $link['remove']['variables'] = array('id'=>'ID');
                        $link['add']['html']['remove'] = button('add');
	
	$LO_ret = DBGet(DBQuery($sql),$functions);

	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	ListOutput($LO_ret,$LO_columns,'Standard Grade','Standard Grades',$link,array(),array('search'=>false));
	echo '<BR>';
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_grade_grade();"').'</CENTER>';
	echo '</FORM>';
}

function _makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name!='GRADE')
		$extra = 'size=5 maxlength=10 class=cell_floating ';
		else # added else for the first textbox merlinvicki
		$extra = 'class=cell_floating';
                  if($name=='SORT_ORDER')
                      $extra = ' size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';
	if($name=='DESCRIPTION')
                        $extra = 'size=30';
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}
function _makeIntInput($value,$name)
{
                global $THIS_RET;
                if($THIS_RET['ID'])
                    $id = $THIS_RET['ID'];
                else
                    $id = 'new';
                if($value!='')
                    $extra = 'size=5 maxlength=10 class=cell_floating onkeydown=\"return numberOnly(event);\"';
                else
                    $extra = 'size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';

                return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}
?>