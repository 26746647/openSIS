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
echo '<style>
.selected td{ font-weight:bold; background:#d5f2fb;}
.selected td a{ display:block}
.category { background:#edfbff; border:1px solid #a4cada; padding:10px; padding-top:0px;}
.category table{ width:500px; float:left; vertical-align:top}
.category table td{ text-align:center;}
</style>';
include('../../Redirect_modules.php');
 DrawBC("School Setup >> ".ProgramTitle());
 
 if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
 {
     foreach ($_REQUEST['values'] as $id => $value) {
        if($id=='new' && $value['TITLE']!='' ) 
        {
            DBQuery('INSERT INTO effort_grades (SYEAR,SCHOOL_ID,EFFORT_CAT,TITLE,SHORT_NAME,SORT_ORDER) VALUES(\''.UserSyear().'\',\''.UserSchool().'\',\''.$_REQUEST['cat_id'].'\',\''.$value['TITLE'].'\',\''.$value['SHORT_NAME'].'\',\''.$value['SORT_ORDER'].'\' ) ');
        }
        elseif($id!='new') 
        {
             $sql = 'UPDATE effort_grades SET ';
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
   $check_eff=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_USED_EFFORT FROM student_efforts WHERE EFFORT_VALUE="'.$_REQUEST['id'].'" '));
   if($check_eff[1]['TOTAL_USED_EFFORT']>0)
     $cant_del_effort= true;
   else    
     DBQuery("DELETE FROM effort_grades WHERE ID='$_REQUEST[id]'");
 }
 elseif(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='create_cat')  //category insertion
 {
    if($_REQUEST['TITLE']!='')
    {   
     if($_REQUEST['cat_id']!='')
     {
         $cat_sql = 'UPDATE effort_grade_categories SET ';
         if(isset($_REQUEST['TITLE']))
         $cat_sql .= ' TITLE="'.$_REQUEST['TITLE'].'",';
         if(isset($_REQUEST['SORT_ORDER']))
         $cat_sql .= ' SORT_ORDER="'.$_REQUEST['SORT_ORDER'].'" ,';
         $cat_sql = rtrim($cat_sql,',') . ' WHERE ID="'.$_REQUEST['cat_id'].'" ';
         DBQuery($cat_sql);
     }
     else 
     {
       DBQuery('INSERT INTO effort_grade_categories (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER) VALUES(\''.UserSyear().'\',\''.UserSchool().'\',\''.$_REQUEST['TITLE'].'\',\''.$_REQUEST['SORT_ORDER'].'\' ) ');
     }
  }
 }
 elseif(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete_cat')  //category delete
 {
     //if(DeletePrompt_CommonCore('Effort Category',$_REQUEST[modname] ))
     if(DeletePromptMod('Effort Category' ))
     {
       $ceck_cat=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_USED_CAT FROM student_efforts se,effort_grades eg WHERE se.EFFORT_VALUE=eg.ID AND eg.EFFORT_CAT="'.$_REQUEST['cat_id'].'" '));
       if($ceck_cat[1]['TOTAL_USED_CAT']>0)
        $cant_del_cat = true;   
       else
       {    
     DBQuery('DELETE FROM effort_grade_categories WHERE ID="'.$_REQUEST['cat_id'].'" ');
     DBQuery('DELETE FROM effort_grades WHERE EFFORT_CAT="'.$_REQUEST['cat_id'].'" ');
      unset($_REQUEST['cat_id']);
       }
     unset($_REQUEST['modfunc']);
     }
 }
 
 if(isset($cant_del_cat))
 {    
   echo '<font color=red>You can not delete it.This effort category is associated with effort garde.</font>';     
   unset($cant_del_cat);
 }
 if(isset($cant_del_effort))
 {
   echo '<font color=red>You can not delete it.This effort is associated with effort garde.</font>';     
   unset($cant_del_effort);
 }    
 
 if($_REQUEST['modfunc']!='delete_cat')
 {    
 #####################category list#####################################
   $all_categories = DBGet(DBQuery('SELECT * FROM  effort_grade_categories ORDER BY SORT_ORDER '));
   if($_REQUEST['cat_id'])
    $cat_id=$_REQUEST['cat_id'];
  else
    $cat_id=$all_categories[1]['ID'];
 
        echo '<div id=pagediv style="width:100%;">';
        
        echo '<div id=left-nav style="margin:20px 0px 10px 10px; width:30%; float:left; border-right:px solid #3091bd; vertical-align:top;">';
        echo '<table width=100% cellspacing=3 border="1" bordercolor="#a9d5e9" class="grid" style="border-collapse:collapse; vertical-align:top;"  cellpadding=4 align= center>';
		echo '<tr><td colspan="3" class="subtabs">Effort Category</td></tr>';
        foreach($all_categories as $cat_index=>$cat_details)
        {
			
            echo "<tr ".($cat_details[ID]==$cat_id ? " class='selected' " : "")."><td class='even'>";
            echo "<a href=Modules.php?modname=$_REQUEST[modname]&cat_id=$cat_details[ID]  >$cat_details[TITLE]</a></td>
                <td class='even'><a class='edit_effort' href=Modules.php?modname=$_REQUEST[modname]&modfunc=show_catdiv&cat_id=$cat_details[ID]><img src='assets/edit_button.gif'/></a></td><td class='even'>
                <a href=Modules.php?modname=$_REQUEST[modname]&modfunc=delete_cat&cat_id=$cat_details[ID]><img src='assets/remove_button.gif'/></a>";
            echo '</td></tr>';
        }
        echo "<tr><td class='odd' align= center colspan='3'> 
              <a href=Modules.php?modname=$_REQUEST[modname]&modfunc=show_catdiv>".button('add') ."</a>
              </tr></td>";
        echo '</table >';
        echo '</div>';

 ########################create category portion###################
  if($_REQUEST['modfunc']=='show_catdiv')
  {
      if($_REQUEST['cat_id']!='')
       $selected_cat=  DBGet(DBQuery('SELECT * FROM effort_grade_categories WHERE ID="'.$_REQUEST['cat_id'].'" '));   
      
        echo '<div id=main-content style=" background:#edfbff;margin:20px 10px 10px 20px; width:60%; padding:10px; float:left; border:1px solid #a4cada;">';
        echo '<div id=show_error></div>';
        echo "<FORM name=cat id=cat action=Modules.php?modname=$_REQUEST[modname]&modfunc=create_cat&cat_id=$_REQUEST[cat_id] method=POST>";
        echo '<table width=100% ><tr><td width=30%>Effort Category Title:</td><td valign="top" width=100px >'.TextInput($selected_cat[1]['TITLE'],'TITLE','','size=40').'</td></tr>';
        echo '<tr><td>Sort Order</td><td>'.TextInput($selected_cat[1]['SORT_ORDER'],'SORT_ORDER','','size=40').'</td>';
        echo '</td></tr>';
        echo '<tr><td colspan=2 ><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="return check_effort_cat();" ').'</CENTER></td></tr></table>';
        echo '</FORM>';
        echo '</div >';
//	echo '<BR>';
//	PopTable_wo_header ('header');
//	ListOutputMod($LO_ret,$LO_columns,'','',$link,array(),array('count'=>false,'download'=>false,'search'=>false));
//	echo '<BR>';
//	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_grade_grade();"').'</CENTER>';
//	PopTable ('footer');
	  
  }
  else
  {    
 ////////////////////////effoert under selected category///////////////////////////
        
 if($_REQUEST['cat_id'])
    $cat_id=$_REQUEST['cat_id'];
 else
    $cat_id=$all_categories[1]['ID'];
 
 if($cat_id!='')
 {    
  $sql = 'SELECT * FROM  effort_grades WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND EFFORT_CAT=\''.$cat_id.'\' ORDER BY SORT_ORDER ';
  $functions = array('TITLE'=>'makeTextInput','SHORT_NAME'=>'makeTextInput','SORT_ORDER'=>'makeTextInput');
  $LO_columns = array('TITLE'=>'Title','SORT_ORDER'=>'Sort Order');

  $link['add']['html'] = array('TITLE'=>makeTextInput('','TITLE'),'SHORT_NAME'=>makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>makeTextInput('','SORT_ORDER'));
  $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
  $link['remove']['variables'] = array('id'=>'ID');
  //$link['add']['html']['remove'] = button('add');
  $LO_ret = DBGet(DBQuery($sql),$functions);



        echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&cat_id=$cat_id method=POST><div class='category' style='float:left;width:60%;margin:20px 10px 10px 20px;'>";
        echo '<p style="font-weight:bold">Effort Items</p>';
	//PopTable_wo_header ('header');
	ListOutputMod($LO_ret,$LO_columns,'','',$link,array(),array('count'=>false,'download'=>false,'search'=>false));
	echo '<BR>';
        $count=0;
        if(count($LO_ret)!=0)
        {
            $count=  DBGet(DBQuery('SELECT max(id) as max_id FROM  effort_grades WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND EFFORT_CAT=\''.$cat_id.'\' '));
            $count=$count[1]['MAX_ID'];
        }               
        echo "<input type=hidden id=count_item value=$count />";
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="check_effort_item();"').'</CENTER>';
	//PopTable ('footer');
	echo '</div></FORM>';
 }
  }
  
 }    
        
        

  function makeTextInput($value,$name)
{
        global $THIS_RET;

        if($THIS_RET['ID'])
            $id = $THIS_RET['ID'];
        else
            $id = 'new';
        if($name=='TITLE')
            $extra = 'size=15 ';
        elseif($name=='SHORT_NAME')
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
