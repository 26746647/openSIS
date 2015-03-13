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
  if($_REQUEST['modfunc'])
  {
       if($_REQUEST['modfunc']=='update')
       {
         // print_r($_REQUEST['values']);
          foreach ($_REQUEST['values'] as $s_key => $s_value) {
             if($s_key=='new')
             { 
                if($s_value['STANDARD_REF_NO']!='')     
                  DBQuery('INSERT INTO us_common_core_standards (SUBJECT,GRADE,COURSE,DOMAIN,TOPIC,STANDARD_REF_NO,STANDARD_DETAILS) VALUES ("'.$s_value['SUBJECT'].'","'.$s_value['GRADE'].'","'.$s_value['COURSE'].'","'.$s_value['DOMAIN'].'","'.$s_value['TOPIC'].'","'.$s_value['STANDARD_REF_NO'].'","'.$s_value['STANDARD_DETAILS'].'" ) ');   
             }
             else
             {
                   $update_sql = 'UPDATE us_common_core_standards SET '; 
                foreach($s_value as $col=>$val){
				$update_sql .= $col."='".str_replace("\'","''",$val)."',";
				}
                   $update_sql = substr($update_sql,0,-1) . " WHERE STANDARD_ID='".$s_key."'";             
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
           //if(DeletePrompt_CommonCore('common core standard',$_REQUEST[modname]))
            if(DeletePromptMod('common core standard' ))
           {
             DBQuery('DELETE FROM us_common_core_standards WHERE STANDARD_ID="'.$_REQUEST['standard_id'].'" ');
             unset($_REQUEST['modfunc']);
           }    
        }
        
  }    
 
 
 
 
  if(!$_REQUEST['modfunc'])
  {    
      $sql_standard='SELECT *FROM us_common_core_standards ';
      $QI_standard = DBQuery($sql_standard);
      $standards_RET = DBGet($QI_standard,array('SUBJECT'=>'makeStandardInput','GRADE'=>'makeStandardInput','COURSE'=>'makeStandardInput','DOMAIN'=>'makeStandardInput','TOPIC'=>'makeStandardInput','STANDARD_REF_NO'=>'makeStandardInput','STANDARD_DETAILS'=>'makeStandardInput'));
      $standards_columns = array('SUBJECT'=>'Subject','GRADE'=>'Grade','COURSE'=>'Course','DOMAIN'=>'Domain','TOPIC'=>'Topic','STANDARD_REF_NO'=>'Standard Ref No','STANDARD_DETAILS'=>'Standard Details');
      $link['add']['html'] = array('SUBJECT'=>makeStandardInput('','SUBJECT'),'GRADE'=>makeStandardInput('','GRADE'),'COURSE'=>makeStandardInput('','COURSE'),'DOMAIN'=>makeStandardInput('','DOMAIN'),'TOPIC'=>makeStandardInput('','TOPIC'),'STANDARD_REF_NO'=>makeStandardInput('','STANDARD_REF_NO'),'STANDARD_DETAILS'=>makeStandardInput('','STANDARD_DETAILS'));
      $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
      $link['remove']['variables'] = array('standard_id'=>'STANDARD_ID');
      
      echo "<FORM name=standard id=standard action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
      if(count($standards_RET)!=0)
      {
            $maxid=  DBGet(DBQuery("select max(standard_id) as max_id from us_common_core_standards"));            
            $maxid=$maxid[1]['MAX_ID']; 
      }
        else {
          $maxid=0;
      }
      echo "<input type=hidden id=count value=$maxid />";
      ListOutput($standards_RET,$standards_columns,'Common Core Standard','Common Core Standards',$link, true, array('search'=>false));
      echo '<br><CENTER><INPUT class="btn_medium" type=submit value=Save onclick="formcheck_common_standards();"></CENTER>';
      echo '</FORM>';
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
	else
        {    
	$extra = 'class=cell_floating ';
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
        }
 }     
?>
