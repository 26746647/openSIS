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
 DrawBC("School Setup >> ".ProgramTitle());
  
 $subjects_RET = DBGet(DBQuery('SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' '));
 //print_r($subjects_RET);
 $subjects= CreateSelect($subjects_RET, 'subject_id', 'Select Subject', 'Modules.php?modname='.$_REQUEST['modname'].'&subject_id=');
 if($_REQUEST['subject_id'])
	{

            $courses_RET = DBGet(DBQuery('SELECT COURSE_ID,TITLE FROM courses WHERE SUBJECT_ID=\''.$_REQUEST['subject_id'].'\'  '));
            $courses = CreateSelect($courses_RET, 'course_id', 'Select Course', 'Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST['subject_id'].'&course_id=');
            $courses = 'Select Course '.$courses;
			
	}
 DrawHeaderHome('Select Subject '.$subjects.'  &nbsp; '.$courses.'');  
 //print_r($_REQUEST);
 
 if($_REQUEST['subject_id'] && $_REQUEST['course_id'] )
  {
     if($_REQUEST['modfunc'])
     {
         if($_REQUEST['modfunc']=='update')
         {
          foreach ($_REQUEST['values'] as $s_key => $s_value) {
             if($s_key=='new')
             { 
                if($s_value['STANDARD_REF_NO']!='' && $s_value['GRADE']!='' && $s_value['DOMAIN']!='' && $s_value['TOPIC']!='')     
                  DBQuery('INSERT INTO school_specific_standards (COURSE_ID,GRADE,DOMAIN,TOPIC,STANDARD_REF_NO,STANDARD_DETAILS) VALUES ("'.$_REQUEST['course_id'].'","'.$s_value['GRADE'].'","'.$s_value['DOMAIN'].'","'.$s_value['TOPIC'].'","'.$s_value['STANDARD_REF_NO'].'","'.$s_value['STANDARD_DETAILS'].'" ) ');   
             }
             else
             {
                   $update_sql = 'UPDATE school_specific_standards SET '; 
                foreach($s_value as $col=>$val){
				$update_sql .= $col."='".str_replace("\'","''",$val)."',";
				}
                   $update_sql = substr($update_sql,0,-1) . " WHERE COURSE_ID='".$_REQUEST['course_id']."' AND STANDARD_ID='".$s_key."'";             
                   $update_sql = str_replace('&amp;', "", $update_sql);
		   $update_sql = str_replace('&quot', "", $update_sql);
		   $update_sql = str_replace('&#039;', "", $update_sql);
		   $update_sql = str_replace('&lt;', "", $update_sql);
		   $update_sql = str_replace('&gt;', "", $update_sql);
		   DBQuery($update_sql);
                //DBQuery('UPDATE us_common_core_standards SET SUBJECT="'.$s_value['SUBJECT'].'",GRADE="'.$s_value['GRADE'].'",COURSE="'.$s_value['COURSE'].'",DOMAIN="'.$s_value['DOMAIN'].'",TOPIC="'.$s_value['TOPIC'].'",STANDARD_REF_NO="'.$s_value['STANDARD_REF_NO'].'",STANDARD_DETAILS="'.$s_value['STANDARD_DETAILS'].'" WHERE STANDARD_ID="'.$s_key.'" ');     
             }  
          }
        unset($_REQUEST['modfunc']);    
        }
         else if($_REQUEST['modfunc']=='remove')
            {
            if(DeletePrompt_CommonCore('school specific standard','delete',$_REQUEST['modname'],$_REQUEST['subject_id'],$_REQUEST['course_id']))
          // if(DeletePrompt_CommonCore('school specific standard',$_REQUEST[modname].'&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id]))
           {
             DBQuery('DELETE FROM school_specific_standards WHERE STANDARD_ID="'.$_REQUEST['standard_id'].'" ');  
             unset($_REQUEST['modfunc']); 
             echo "<script>window.location='Modules.php?modname=".$_REQUEST[modname]."&subject_id=".$_REQUEST['subject_id']."&course_id=".$_REQUEST['course_id']."'</script>";
           }              
        }
     }    
    
     if(!$_REQUEST['modfunc'])
     {  
         if($_REQUEST['course_id'] )
         {
      $sql_standard='SELECT * FROM school_specific_standards WHERE COURSE_ID=\''.$_REQUEST['course_id'].'\' ';
      $QI_standard = DBQuery($sql_standard);
      $standards_RET = DBGet($QI_standard,array('GRADE'=>'makeStandardInput','DOMAIN'=>'makeStandardInput','TOPIC'=>'makeStandardInput','STANDARD_REF_NO'=>'makeStandardInput','STANDARD_DETAILS'=>'makeStandardInput'));
      $standards_columns = array('GRADE'=>'Grade','DOMAIN'=>'Domain','TOPIC'=>'Topic','STANDARD_REF_NO'=>'Standard Ref No','STANDARD_DETAILS'=>'Standard Details');
      $link['add']['html'] = array('GRADE'=>makeStandardInput('','GRADE'),'DOMAIN'=>makeStandardInput('','DOMAIN'),'TOPIC'=>makeStandardInput('','TOPIC'),'STANDARD_REF_NO'=>makeStandardInput('','STANDARD_REF_NO'),'STANDARD_DETAILS'=>makeStandardInput('','STANDARD_DETAILS'));
      $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&modfunc=remove";
      $link['remove']['variables'] = array('standard_id'=>'STANDARD_ID');
      
      echo "<FORM name=sss id=sss action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&modfunc=update method=POST>";
      ListOutput($standards_RET,$standards_columns,'School Specific Standard for this course','School Specific Standards for this course',$link, true, array('search'=>false));
      echo '<br><CENTER><INPUT class="btn_medium" type=submit value=Save onclick="formcheck_school_specific_standards()"></CENTER>';    
      echo '</FORM>';
         }
    }
 
  }
 
function CreateSelect($val, $name, $opt, $link='')
{
       if($link!='')
        $html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
        else
        $html .= "<select name=".$name." id=".$name." >";
        $html .= "<option value=''>".$opt."</option>";

                        foreach($val as $key=>$value)
                        {
                                if($value[strtoupper($name)]==$_REQUEST[$name])
                                        $html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
                                else
                                        $html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";	
                        }
        $html .= "</select>";
        return $html;
}

 function makeStandardInput($value,$name)
 {
    global $THIS_RET;
	
	if($THIS_RET['STANDARD_ID'])
		$id = $THIS_RET['STANDARD_ID'];
	else
		$id = 'new';
        
        if($name=='TOPIC' || $name=='STANDARD_DETAILS' )
        {
           $extra = 'style=width:130px';
         return StandardTextAreaInput($value,'values['.$id.']['.$name.']','',$extra);  
        }
        elseif($name=='GRADE')
        {
	$grades_RET = DBGet(DBQuery('SELECT ID,TITLE FROM school_gradelevels WHERE SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));
		if(count($grades_RET))
		{
			foreach($grades_RET as $grade)
				$grades[$grade['ID']] = $grade['TITLE'];
		}
	
	return SelectInput($value,'values['.$id.']['.$name.']','',$grades,'N/A');
        }
	else
        {    
	$extra = 'class=cell_floating ';
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
        }
 } 

?>
