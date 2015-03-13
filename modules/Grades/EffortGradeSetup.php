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
 DrawBC("Gradebook >> ".ProgramTitle());
 
 if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
 {
     foreach ($_REQUEST['values'] as $id => $value) {
        if($id=='new' && $value['VALUE']!='' && $value['COMMENT']!='' ) 
        {
            DBQuery('INSERT INTO effort_grade_scales (SYEAR,SCHOOL_ID,VALUE,COMMENT,SORT_ORDER) VALUES(\''.UserSyear().'\',\''.UserSchool().'\',\''.$value['VALUE'].'\',\''.$value['COMMENT'].'\',\''.$value['SORT_ORDER'].'\' ) ');
        }
        elseif($id!='new') 
        {
             $sql = 'UPDATE effort_grade_scales SET ';
              foreach($value as $column=>$column_value)
              {
                   if(isset($column_value))
                    $sql .= $column . '=\''.str_replace("\'","'",trim($column_value)).'\',';
                  else
                   $sql .= $column . '=NULL ,';
              }
              $sql = substr($sql,0,-1) . ' WHERE ID=\''.$id.'\'';
              DBQuery($sql);
        }
         
     }
 }
 elseif(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove')
 {
     DBQuery("DELETE FROM effort_grade_scales WHERE ID='$_REQUEST[id]'");
 }





  $sql = 'SELECT * FROM  effort_grade_scales WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ';
  $functions = array('VALUE'=>'makeTextInput','COMMENT'=>'makeTextInput','SORT_ORDER'=>'makeTextInput');
  $LO_columns = array('VALUE'=>'Value','COMMENT'=>'Comment','SORT_ORDER'=>'Sort Order');

  $link['add']['html'] = array('VALUE'=>makeTextInput('','VALUE'),'COMMENT'=>makeTextInput('','COMMENT'),'SORT_ORDER'=>makeTextInput('','SORT_ORDER'));
  $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
  $link['remove']['variables'] = array('id'=>'ID');
  //$link['add']['html']['remove'] = button('add');
  $LO_ret = DBGet(DBQuery($sql),$functions);



        echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	echo '<BR>';
	PopTable_wo_header ('header');
	ListOutputMod($LO_ret,$LO_columns,'','',$link,array(),array('count'=>false,'download'=>false,'search'=>false));
	echo '<BR>';
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_grade_grade();"').'</CENTER>';
	PopTable ('footer');
	echo '</FORM>';


  function makeTextInput($value,$name)
{
        global $THIS_RET;

        if($THIS_RET['ID'])
            $id = $THIS_RET['ID'];
        else
            $id = 'new';
        if($name=='VALUE')
            $extra = 'size=15 maxlength=25';
        elseif($name=='COMMENT')
            $extra = 'size=15 maxlength=100';
        elseif($name=='SORT_ORDER')
        {
            if($id=='new' || $THIS_RET['SORT_ORDER']=='')
                $extra = 'size=5 maxlength=5 onkeydown="return numberOnly(event);"';
            else
                $extra = 'size=5 maxlength=5 onkeydown=\"return numberOnly(event);\"';
        }
        return TextInput($value,"values[$id][$name]",'',$extra);
}      
        
?>
