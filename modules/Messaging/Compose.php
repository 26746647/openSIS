<?php
include_once("fckeditor/fckeditor.php") ;
PopTable('header','Compose Message');
$userName=  User('USERNAME');
$_SESSION['course_period_id']='';
echo "<FORM name=ComposeMail id=Compose action=Modules.php?modname=Messaging/Inbox.php&count=$c  METHOD=POST enctype=multipart/form-data >";
if($_REQUEST['modfunc']!='choose_course')
{
    if(User('PROFILE')=='admin' || User('PROFILE')=='teacher')
    {
        echo "<DIV id=course_div>";
    } 
if(isset($_REQUEST['mod']) && $_REQUEST['mod']=='draft')
{
    $mail_id=$_REQUEST['mail_id'];
   $query="select * from msg_inbox where mail_id='$mail_id'";
   $result=DBGet(DBQuery($query));

foreach($result as $v)
{
    $to_user=$v['TO_USER'];
    $to_cc=$v['TO_CC'];
       $to_bcc=$v['TO_BCC'];
       $mail_subject=$v['MAIL_SUBJECT'];
       $mail_id=$v['MAIL_ID'];
       $mail_body=$v['MAIL_BODY'];
}
}
if(!isset($_REQUEST['modto']) && !isset($_REQUEST['mod']))
{
       $to_user='';
    $to_cc='';
    $to_bcc='';
    $mail_subject='';
    $mail_id='';
        $mail_body='';
}
if(isset($_REQUEST['modto']) && $_REQUEST['m']=='reply')
{
   $to_user=$_REQUEST['modto'];
   $mail_subject=  base64_decode($_REQUEST['sub']);
  // echo $to_user;
}

echo '<table width="100%">
    
   <tr>
      <td align="left" width="50px">
            To :
       </td>
       <td align="left">';

 echo  TextInput_mail($to_user,'txtToUser','','onkeyup="nameslist(this.value)" class=mail_input');
 echo ' &nbsp; <a href="#" onclick="show_cc()">CC</a>';
 echo ' &nbsp; <a href="#" onclick="show_bcc()">BCC</a>';
 $groupList = DBGet(DBQuery("SELECT GROUP_ID,GROUP_NAME FROM mail_group where user_name='".$userName."'"));
        
     echo "&nbsp; <SELECT name='groups' style='max-width:250;' onChange=\"list_of_groups(this.options[this.selectedIndex].value);\"><OPTION value=''>Select Group</OPTION>";
     foreach($groupList as $groupArr)
     {	
         $option=$groupArr['GROUP_NAME'];
         $value=$groupArr['GROUP_ID'];
     
         if($_REQUEST['sel_group']==$value)  
         echo "<OPTION selected='selected' value=\"$value\">$option</OPTION>";
         else
         echo "<OPTION value=\"$option\">$option</OPTION>";   

      }
      echo '</SELECT>';

      
 echo'&nbsp; ';
if(User('PROFILE')=='teacher')
{
  echo "<a href='#' style='background:#efefef; border:1px solid #7f9db9; text-decoration:none; padding:2px 7px; display:inline-block;' onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Message My Class</a>";
 // echo "<a HREF=# class='btn_wide' onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Message my class</a>";
}
echo " <div id=ajax_response></div>";

    echo   '</td>
        </tr>';
   
     
        echo '<tr>
      <td colspan="2">
	  <div id="cc" style="display:none"><table width="100%" cellpadding="0"><tr><td align="left" width="50px">
            CC :
       </td>
       <td align="left" style="padding-bottom:5px">&nbsp;&nbsp;
'.  TextInput_mail($to_cc,'txtToCCUser','','class=mail_input').'
        </td>
        </tr></table></div>
        </tr>
          <tr>
      <td colspan="2">
      <div id="bcc" style="display:none"><table width="100%" cellpadding="0"><tr><td width="50px">
            BCC :
       </td>
       <td align="left">&nbsp;&nbsp;
'.  TextInput_mail($to_bcc,'txtToBCCUser','','class=mail_input').'
        </td>
        </tr></table></div></td>
        </tr>';
      //echo '</DIV>'; 
      // echo'<table width="100%">';  
    echo '<tr>
       <td align="left" width="50px">
           Subject:
        </td>
        <td align="left">
            '.  TextInput_mail($mail_subject,'txtSubj','','class=mail_input_full').'
       </td>
   </tr>
   
<tr><td colspan="2">';

                        $oFCKeditor = new FCKeditor("txtBody") ;
			$oFCKeditor->BasePath = "modules/Messaging/fckeditor/" ;
			$oFCKeditor->Value = '';
			$oFCKeditor->Height = "350px";
			$oFCKeditor->Width = "600px";
                        $oFCKeditor->ToolbarSet	= 'Mytoolbar ';
			$oFCKeditor->Create() ;


echo '</td></tr>
       <tr><td  align="left" width="60px">Attach file: </td><td><input id="up1" type="file" name="f[]" onchange="attachfile()" multiple/>';
echo '<a hef=# id=del1 name=del1 style="display:none" onclick="clearfile1()">Clear</a>';
echo '<input type="button" value="Attach Another File" id="attach1" style="display:none" onclick="showdiv()">';

echo  '<div id="hideShow" style="display:none">';
echo '<input type="file" id="up2" name="f[]" onchange="attachanotherfile()" multiple/>';
echo '<a hef=# id=del2 name=del2 style="display:none" onclick="clearfile2()">Clear</a>';
echo '<input type="button" value="Attach Another File" id="attach2" style="display:none" onclick="addn_showdiv()">';
echo '</div>';

echo '<div id="addn_hideShow" style="display:none">';
echo '<input type="file" name="f[]" multiple/>';
echo '</div>';
echo '</td></tr>';
echo'</table>';
DrawHeader('','',"<br /><INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Send' onClick='validate_email();' />");
}
if($_REQUEST['modfunc']=='choose_course')
{
   
                     
        if(!$_REQUEST['course_period_id'])
		include 'modules/Scheduling/CoursesforWindow.php';
	else
	{
		$_SESSION['MassSchedule.php']['subject_id'] = $_REQUEST['subject_id'];
		$_SESSION['MassSchedule.php']['course_id'] = $_REQUEST['course_id'];
		$_SESSION['MassSchedule.php']['course_period_id'] = $_REQUEST['course_period_id'];

		$course_title = DBGet(DBQuery('SELECT TITLE FROM courses WHERE COURSE_ID=\''.$_SESSION['MassSchedule.php']['course_id'].'\''));
		$course_title = $course_title[1]['TITLE'];
		$period_title_RET = DBGet(DBQuery('SELECT TITLE,MARKING_PERIOD_ID,GENDER_RESTRICTION FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
		$period_title = $period_title_RET[1]['TITLE'];
		$mperiod = $period_title_RET[1]['MARKING_PERIOD_ID'];
                $gender_res=$period_title_RET[1]['GENDER_RESTRICTION'];
                $_SESSION['MassSchedule.php']['gender']=$gender_res;
                $_SESSION['course_period_id']=$_REQUEST['course_period_id'];
                $grp=DBGet(DBQuery("select * from mail_group")); 
                $title=$course_title.'&nbsp;&nbsp;'.$period_title;
                if($gender_res=='N')
                {
                    echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"<INPUT type=checkbox id=list_gpa_student name=list_gpa_student value=Y CHECKED>Only Students<INPUT type=checkbox name=list_gpa_parent id=list_gpa_parent value=Y CHECKED>Only Parents".(User('PROFILE')!='teacher'?'<INPUT type=checkbox name=list_gpa_teacher id=list_gpa_teacher value=Y CHECKED>Only Teachers':'')."&nbsp;&nbsp;<a href='Modules.php?modname=Messaging/Compose.php'><font color='red'>Remove Course</font></a><table width='100%'><tr><td align='left' width='50px'>To :</td><td align='left'><input type=text class=mail_input id=txtToUser name=txtToUser value='$title' readonly>&nbsp; <a href=# onclick=show_cc()>CC</a>&nbsp; <a href=# onclick=show_bcc()>BCC</a> </td></tr><tr><td colspan=2><div id=cc_bcc style=display:none><table width=100% cellpadding=0><tr><td align=left width=50px>CC : </td><td align=left style=padding-bottom:5px>".TextInput_mail($to_cc,'txtToCCUser','','class=mail_input')."</td></tr><tr><td align=left>BCC :</td><td align=left>".  TextInput_mail($to_bcc,'txtToBCCUser','','class=mail_input')."</td></tr></table></div></td></tr></table>\";window.close();</script>"; 
                }
                else
                {
                    echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title <BR>$period_title <br>Gender : ".($gender_res=='M'?'Male':'Female')." \";window.close();</script>";
                }
	}
}
  

 echo "</form>";?>
 
<?php PopTable('footer');
?>



