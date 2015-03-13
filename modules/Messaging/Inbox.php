<?php
include('../../Redirect_modules.php');
//print_r($_REQUEST);
////exit;
//echo '<br><br>';
$userName=  User('USERNAME');
$toProfile='';
$toArray=array();
$toArray=  explode(',',$_REQUEST["txtToUser"]);
if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='trash' )
{ 
    if(count($_REQUEST['mail'])!=0)
    {
        $count=count($_REQUEST['mail']);
if($count!=1)
     $row="messages";
else 
    $row="message";
    if(DeleteMail($count.' '.$row,'delete',$_REQUEST['modname']))
    {
         $id=array_keys($_REQUEST['mail']);
        $mail_id=implode(',',$id);

//         echo "<script>window.location.href='Modules.php?modname=Messaging/Inbox.php&modfunc=trash'</script>";
        unset($_REQUEST['modfunc']);
    
 
    
    $to_arr=array();
    //$mail_id=$_REQUEST['mail_id'];
//    $id=array();
$arr=array();
    $qr="select to_user,istrash,to_cc,to_bcc from msg_inbox where mail_id IN($mail_id)";
$fetch=DBGet(DBQuery($qr));
//print_r($fetch);
foreach($fetch as $key =>$value)
{
     $s=$value['TO_USER'];"<br>";
    $to_cc=$value['TO_CC'];
    $to_cc_arr=explode(',',$to_cc);
    $arr=explode(',',$s);
    $to_bcc=$value['TO_BCC'];
    $to_bcc_arr=explode(',',$to_bcc);


 if(($key = array_search($userName,$arr)) !== false) {
    unset($arr[$key]);
    $update_to_user=implode(',',$arr);
    if($value['ISTRASH']!='')
    {
        $to_arr=explode(',',$value['ISTRASH']);

            array_push($to_arr,$userName);

            $trash_user=implode(',',$to_arr);
            
    }
     else
    {
       $trash_user=$userName;
    }
    
//       $trash_user=$userName;
       $query="update msg_inbox set to_user='$update_to_user',istrash='$trash_user' where mail_id IN ($mail_id)";

    $fetch_ex=DBGet(DBQuery($query));
 }
 if(($key = array_search($userName, $to_cc_arr)) !== false) {
    unset( $to_cc_arr[$key]);
   echo $update_to_user=implode(',', $to_cc_arr);
    if($value['ISTRASH']!='')
    {
        $to_arr=explode(',',$value['ISTRASH']);

            array_push($to_arr,$userName);

            $trash_user=implode(',',$to_arr);
    }
    else
    {
       $trash_user=$userName;
    }

     
       $query="update msg_inbox set to_cc='$update_to_user',istrash='$trash_user' where mail_id IN ($mail_id)";

    $fetch_ex=DBGet(DBQuery($query));
    
 }
    if(($key = array_search($userName,$to_bcc_arr)) !== false) {
    unset( $to_bcc_arr[$key]);
    $update_to_user=implode(',',$to_bcc_arr);
    if($value['ISTRASH']!='')
    {
        $to_arr=explode(',',$value['ISTRASH']);

            array_push($to_arr,$userName);

            $trash_user=implode(',',$to_arr);
    }
     else
    {
       $trash_user=$userName;
    }
//       $trash_user=$userName;
       $query="update msg_inbox set to_bcc='$update_to_user',istrash='$trash_user' where mail_id IN ($mail_id)";

    $fetch_ex=DBGet(DBQuery($query));
 }

 

}

//    $mail_trash="update msg_inbox set istrash=1 where mail_id='$mail_id'";
//    $mail_trash_ex=DBQuery($mail_trash);
//    unset($_REQUEST['modfunc']);
    }
    }
    else
    {
        echo '<BR>';
		PopTable('header','Alert Message');
		echo "<CENTER><h4>Please select atleast one message to delete</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=Messaging/Inbox.php\"'></FORM></CENTER>";
		PopTable('footer');
		return false;
    }
}

if(count($toArray)>1)
    CheckAuthenticMail($userName,$_REQUEST["txtToUser"],$_REQUEST["txtToCCUser"],$_REQUEST["txtToBCCUser"]);
else 
{
  if(count($toArray)==1)
  {
   if($_SESSION['course_period_id']!='')
   { 
    if(User('PROFILE')=='teacher')
     {
$chkParent=$_POST['list_gpa_parent'];
$chkStudent=$_POST['list_gpa_student'];
$course_period_id=$_SESSION['course_period_id'];
if($chkStudent=='Y')
    $stuList_forCourseArr=  DBGet(DBQuery("SELECT la.username,student_id from students s ,login_authentication la where student_id in(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.course_period_id=".$course_period_id.") AND la.USER_ID=s.STUDENT_ID AND la.PROFILE_ID=3 AND username IS NOT NULL"));
//if($chkTeacher=='Y' )
//    $teacherList_forCourse=DBGet(DBQuery("Select distinct teacher_id,secondary_teacher_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.course_period_id=".$course_period_id));
if($chkParent=='Y')
{
    $parentList_forCourseArr=DBGet(DBQuery("SELECT username FROM login_authentication WHERE username IS NOT NULL AND PROFILE_ID=4 AND USER_ID IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (Select student_id from students where student_id in(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.course_period_id=".$course_period_id.")))"));   
}
//echo "<br><br>studentlist:<br>";
//print_r($stuList_forCourseArr);
//echo "<br><br>parentlist:<br>";
//print_r($parentList_forCourseArr);exit;
$stuList_forCourse='';
 foreach ($stuList_forCourseArr as $stu) {
     $stuList_forCourse .= $stu["USERNAME"] . ",";
 }
 $parentList_forCourse='';
 foreach ($parentList_forCourseArr as $parent) {
     $parentList_forCourse .= $parent["USERNAME"] . ",";
 }
 if($chkStudent=='Y' && $chkParent=='Y')
 {
 $finalList=$stuList_forCourse.",".$parentList_forCourse;
 }
 if($chkStudent=='Y' && $chkParent!='Y')
 {
 $finalList=$stuList_forCourse;

 }
  if($chkStudent!='Y' && $chkParent=='Y')
 {
 $finalList=$parentList_forCourse;

 }
 $finalList=rtrim($finalList, ",");
 if($finalList!="")
CheckAuthenticMail($userName,$finalList,$_REQUEST["txtToCCUser"],$_REQUEST["txtToBCCUser"]);
}
   }
   else 
   {
       $to=str_replace("'","\'",trim($_REQUEST["txtToUser"]));
       $q="SELECT mail_group.*, GROUP_CONCAT(gm.user_name) AS members FROM mail_group INNER JOIN mail_groupmembers gm ON(mail_group.group_id = gm.group_id) where mail_group.user_name='$userName' AND group_name ='$to' GROUP BY gm.group_id";
       $group_list=  DBGet(DBQuery($q));
       if(count($group_list)!=0)
       {
       foreach ($group_list as $groupId=>$groupmembers)
       {
          $groupName=$group_list[$groupId]['GROUP_NAME'];
          if($groupName==$_REQUEST["txtToUser"])
          {
          $members=$group_list[$groupId]['MEMBERS'];
          CheckAuthenticMail($userName,$members,$_REQUEST["txtToCCUser"],$_REQUEST["txtToBCCUser"],$groupName);
          }
       }
       }
       else 
       {
           if(trim($_REQUEST["txtToUser"])!="")
           {
            CheckAuthenticMail($userName,$_REQUEST["txtToUser"],$_REQUEST["txtToCCUser"],$_REQUEST["txtToBCCUser"]);
            
           }
       }
   }
  }
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='body' )
{
    PopTable('header','Message Details');
    $mail_id=$_REQUEST['mail_id'];
    $mail_body="select mail_body,mail_attachment,mail_Subject,from_user,mail_datetime,to_cc_multiple,to_multiple_users,to_bcc_multiple,mail_read_unread from msg_inbox where mail_id='$mail_id'";

    $mail_body_info=DBGet(DBQuery($mail_body));
    $sub=$mail_body_info[1]['MAIL_SUBJECT'];
    if($mail_body_info[1]['MAIL_READ_UNREAD']=="")
        $user_name=$userName;
    else 
    {
        $read_unread_Arr=  explode(",", $mail_body_info[1]['MAIL_READ_UNREAD']);
        if(in_array($userName, $read_unread_Arr))
        {
            $user_name=$mail_body_info[1]['MAIL_READ_UNREAD'];
        }
        else
        {
            $mail_body_info[1]['MAIL_READ_UNREAD'].=','.$userName;
            $user_name=$mail_body_info[1]['MAIL_READ_UNREAD'];
        }
    }
    $mail_read_unread="update msg_inbox set mail_read_unread='$user_name' where mail_id='$mail_id'";
    $mail_read_unread_ex=DBQuery($mail_read_unread);
    
    foreach($mail_body_info as $k => $v)
    {
         $fromUser=$v['FROM_USER'];
         echo "<table width='100%' style='width:650px'>
               <tr>
               <td align='left'><b>From:</b> ". GetNameFromUserName($v['FROM_USER'])."</td>
               <td align='right'><b>Date/Time:</b> ".$v['MAIL_DATETIME'].
               "</tr>";
         if($v['TO_CC_MULTIPLE']!='')
         {
             echo "<tr>
                   <td align='left'>
                   <b>CC:</b> ".$v['TO_CC_MULTIPLE'] ."</td><td></td>
                   </tr>";    
         }
           if($v['MAIL_ATTACHMENT']!='')
         {
               echo "<tr>
                 <td align='left'>
                  Attachment: ";
          $attach=explode(',',$v['MAIL_ATTACHMENT']);
          foreach($attach as $user=>$img)
          {
              $img_pos=strrpos($img,'/');
              $img_name[]=substr($img,$img_pos+1,strlen($img));
              //$name=explode('_',$img);
              $pos=strpos($img,'_');
              
              $img_src[]=substr($img,$pos+1,strlen($img));
              for($i=0;$i<(count($img_src));$i++)
              {
              $img1=$img_src[$i];
              $m=array_keys(str_word_count($img1, 2));
              $a=$m[0];
              $img3[$i]=substr($img1,$a,strlen($img1));
              }
              
          }
         for($i=0;$i<(count($attach));$i++)
         {
             
                    $img_name[$i]=str_replace(" ", "\\",$img_name[$i]);
                    $img4[$i]=str_replace(" ", "\\",$img3[$i]);
                //    else if($groupname[$i]!=" " || $groupname[$i]!="'")
                //        $grp=str_replace("","",$groupname);

               
//                             echo "<a href='for_export.php?modname=Messaging/Inbox.php&search_modfunc=list&next_modname=Messaging/Inbox.php&sql_save_session=true&page=&LO_sort=&LO_direction=&LO_search=&LO_save=1&_openSIS_PDF=true&filename=$img_name[$i]&name=$img4[$i]&modfunc=save'>".$img3[$i]."</a>";
                             echo "<a href='download_window.php?filename=$img_name[$i]&name=$img4[$i]' target='new' >".$img3[$i]."</a>";
             
              echo '<br>&nbsp;&nbsp;&nbsp;<br>';
             
         }
         echo "</td></tr>";
         }
         
         if($v['TO_BCC_MULTIPLE']!='')
         {
              $to_bcc_arr=explode(',',$v['TO_BCC_MULTIPLE']);
              if(in_array($userName,$to_bcc_arr))
              {
                  echo "<tr>
                        <td align='left'><b>BCC:</b> ".$userName."</td><td></td></tr>"; 
                  
              }
         }
         
         echo "<tr><td align='left' colspan='2'><br /><div class='mail_body'>".htmlspecialchars_decode(wordwrap($v['MAIL_BODY'], 100, "<br />", true))."<br /></div></td></tr></table>";

    }
   echo "<table align='center'><tr><td><a class='btn_medium' href='Modules.php?modname=Messaging/Compose.php&modto=$fromUser&m=reply&sub=".base64_encode($sub)."'>"
                     . "Reply</a></td><td>";
    echo "<a class='btn_medium' href='Modules.php?modname=Messaging/Inbox.php'>Back</a></td></tr></table>";
    PopTable('footer');
}

 if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='save')
 { 
     $mod_file=$_REQUEST['name'];
     $_REQUEST['filename']=str_replace("\\", " ",$_REQUEST['filename']);
     $mod_file=str_replace("\\", " ",$mod_file);

     if(isset($_REQUEST['filename']))
     {
        set_time_limit(0);
        $file_path='./assets/'.$_REQUEST['filename'];
        output_file($file_path, ''.$_REQUEST['filename'].'', 'text/plain',$mod_file);
     }
 }

               
if(!isset($_REQUEST['modfunc']))
{
    PopTable('header','Inbox');
    $link=array();
    $id=array();
    $arr=array();
    $qr="select to_user,mail_id,to_cc,to_bcc from msg_inbox where isdraft=0";
    $fetch=DBGet(DBQuery($qr));
    //print_r($fetch);
    foreach($fetch as $key =>$value)
    {
         $s=$value['TO_USER'];"<br>";
         $cc=$value['TO_CC'];
         $bcc=$value['TO_BCC'];

        $arr=explode(',',$s);
         $arr_cc=explode(',',$cc);
         $arr_bcc=explode(',',$bcc);

        if(in_array($userName,$arr) || in_array($userName,$arr_cc) || in_array($userName,$arr_bcc))
        {
            array_push($id,$value['MAIL_ID']);
    //            print_r($id);
        }
        else
        {

        }
    }
     $count=count($id);
    if($count>0)
     $to_user_id=implode(',',$id);
    else
        $to_user_id='null';
    
    echo "<FORM name=sav id=sav action=Modules.php?modname=$_REQUEST[modname]&modfunc=trash method=POST>";
    $inbox="select * from msg_inbox where mail_id in($to_user_id) order by(mail_id)desc";
    $inbox_info=DBGet(DBQuery($inbox));
    
   foreach($inbox_info as $key=>$value)
   {
       if($value['MAIL_READ_UNREAD']=='')
       {
	    $inbox_info[$key]['MAIL_SUBJECT'] = '<div style="color:red;"><b>'.$inbox_info[$key]['MAIL_SUBJECT'].'</b></div>';
       }
       if($value['MAIL_READ_UNREAD']!='')
       {
           $read_user=explode(',',$value['MAIL_READ_UNREAD']);
           if(!in_array($userName,$read_user))
            {
               array_push($key,$value['MAIL_ID']);
               $inbox_info[$key]['MAIL_SUBJECT'] = '<div style="color:red;"><b>'.$inbox_info[$key]['MAIL_SUBJECT'].'</b></div>';
            }
        }
       if($value['MAIL_ATTACHMENT']!='')
       {
           $inbox_info[$key]['MAIL_SUBJECT']=$inbox_info[$key]['MAIL_SUBJECT']."<img align='right' src='./assets/attachment.png'>";
       }
//        $from_User=$value['FROM_USER'];
//       $fromProfile=  DBGet(DBQuery("Select * from login_authentication where username='$from_User'"));
//       $fromProfileId=$fromProfile[1]['PROFILE_ID'];
//       $fromUserId=$fromProfile[1]['USER_ID'];
//       if($fromProfileId!=3 ||$fromProfileId!=4)
//       {
//           $nameQuery="Select CONCAT(first_name,' ', last_name) name from staff where profile_id=$fromProfileId and staff_id=$fromUserId  ";
//       }
//       if($fromProfileId==3)
//       {
//           $nameQuery="Select CONCAT(first_name,' ', last_name) name from students where profile_id=$fromProfileId and staff_id=$fromUserId  ";
//       }
//       if($fromProfileId==4)
//       {
//           $nameQuery="Select CONCAT(first_name,' ', last_name) name from people where profile_id=$fromProfileId and staff_id=$fromUserId  ";
//       }
//       $name=  DBGet(DBQuery($nameQuery));
//       $name=$name[1]['NAME'];
////       echo "<br> ".$name;
        $inbox_info[$key]['FROM_USER']=GetNameFromUserName($value['FROM_USER']);
       //print_r($fromProfile);
   }
        echo '<div style="overflow:auto; width:820px;">';
        echo '<div id="students" >';
        $columns = array('FROM_USER'=>'FROM','MAIL_SUBJECT'=>'SUBJECT','MAIL_DATETIME'=>'DATE/TIME');
        $extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
        $extra['LO_group'] = array('MAIL_ID');
        $extra['columns_before']= array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'mail\');"><A>');
	$extra['new'] = true;
         if(is_array($extra['columns_before']))
	{
		$LO_columns = $extra['columns_before'] + $columns;
		$columns = $LO_columns;
                
        }
        $link['MAIL_SUBJECT']['link'] = "Modules.php?modname=Messaging/Inbox.php&modfunc=body";
	$link['MAIL_SUBJECT']['variables'] = array('mail_id'=>'MAIL_ID');
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=trash";
        //$link['remove']['variables'] = array('mail_id'=>'MAIL_ID');
        foreach($inbox_info as $id=>$value)
         {
         $extra['columns_before']['CHECKBOX'] = "<INPUT type=checkbox name=mail[".$value['MAIL_ID']."] value=Y>";
          $inbox_info[$id]=$extra['columns_before']+$value;
         }
         if(count($inbox_info)!=0)
        {
            echo '<table align="center" width="94%"><tr><td align="right"><INPUT type=submit class=delete_mail value=Delete onclick=\'formload_ajax("sav");\' ></td></tr></table>';
        }
        echo "";

        ListOutput($inbox_info,$columns,'','',$link,array(),array('search'=>false),'',TRUE);
        //echo '</TD></TR></TABLE>';
        echo "</div>";
        echo "</div>";
        echo '</FORM>';
         PopTable('footer');    
 }

function SendMail($to,$userName,$subject,$mailBody,$attachment,$toCC,$toBCCs,$grpName)
 {
    $grpName=  str_replace("'", "\'", $grpName);
     $inbox_query=mysql_query('INSERT INTO msg_inbox(to_user,from_user,mail_Subject,mail_body,isdraft,mail_attachment,to_multiple_users,to_cc_multiple,to_cc,to_bcc,to_bcc_multiple,mail_datetime) VALUES(\''.$to.'\',\''.$userName.'\',\''.$subject.'\',\''.$mailBody.'\',\''.$isdraft.'\',\''.$attachment.'\',\''.$to.'\',\''.$toCC.'\',\''.$toCC.'\',\''.$toBCCs.'\',\''.$toBCCs.'\',now())');  
     if($grpName=='false')
       $outbox_query=mysql_query('INSERT INTO msg_outbox(to_user,from_user,mail_Subject,mail_body,mail_attachment,to_cc,to_bcc,mail_datetime) VALUES(\''.$to.'\',\''.$userName.'\',\''.$subject.'\',\''.$mailBody.'\',\''.$attachment.'\',\''.$toCC.'\',\''.$toBCCs.'\',NOW())'); 
     else
     {
         $q='INSERT INTO msg_outbox(to_user,from_user,mail_Subject,mail_body,mail_attachment,to_cc,to_bcc,mail_datetime,to_grpName) VALUES(\''.$to.'\',\''.$userName.'\',\''.$subject.'\',\''.$mailBody.'\',\''.$attachment.'\',\''.$toCC.'\',\''.$toBCCs.'\',NOW(),\''.$grpName.'\')';
        // echo "<br> ".$q;
         $outbox_query=mysql_query($q) ; 
     }
     echo 'Your message has been sent';  
 }
 
function array_push_assoc($array, $key, $value){
$array[$key] = $value;
return $array;
}

function CheckAuthenticMail($userName,$toUsers,$toCCUsers,$toBCCUsers,$grpName='false')
 {
     $toAssArray=array();
     $toCCAssArray=array();
     $toBCCAssArray=array();
     $notUserArray=array();
     $toUserArray=explode(",", $toUsers);
     foreach ($toUserArray as $toUser)
     {
        $to=trim($toUser);
        
//        $sub = DBQuery("SELECT up.profile FROM staff as s,user_profiles as up WHERE s.profile_id=up.id AND s.username='".$to."'");
        $sub = DBQuery("SELECT * FROM login_authentication,user_profiles WHERE login_authentication.profile_id=user_profiles.id AND username='".$to."'");
        $RET = DBGet($sub);
        if(Count($RET)==0)
        {
//            $student=DBQuery("SELECT * FROM students where username='".$to."'");
//            $st=DBGet($student);
//            if(Count($st)!=0)
//            {
//                $toProfile="student";
//                $toAssArray=array_push_assoc($toAssArray,$toUser,$toProfile);
//            }
//            else 
//            {
                array_push($notUserArray, $to);
//            }
        }
        else
        {
            $toProfile=$RET[1]['PROFILE'];
             $toAssArray=array_push_assoc($toAssArray,$toUser,$toProfile);
        }
     }
   //  print_r($toAssArray);
     $toUserArray=array_diff($toUserArray,$notUserArray);
     
     $toCCUserArray=explode(",", $toCCUsers);
     foreach ($toCCUserArray as $toCCUser)
     {
        $toCC=trim($toCCUser);
//        $sub = DBQuery("SELECT up.profile FROM staff as s,user_profiles as up WHERE s.profile_id=up.id AND s.username='".$toCC."'");
        $sub = DBQuery("SELECT * FROM login_authentication,user_profiles WHERE login_authentication.profile_id=user_profiles.id AND username='".$toCC."'");
        $RET = DBGet($sub);
        if(Count($RET)==0)
        {
//            $student=DBQuery("SELECT * FROM students where username='".$toCC."'");
//            $st=DBGet($student);
//            if(Count($st)!=0)
//            {
//                $toCCProfile="student";
//                $toCCAssArray=array_push_assoc($toCCAssArray,$toCCUser,$toCCProfile);
//            }
//            else 
//            {
                array_push($notUserArray, $toCC);
//            }
        }
        else
        {
            $toCCProfile=$RET[1]['PROFILE'];
            $toCCAssArray=array_push_assoc($toCCAssArray,$toCCUser,$toCCProfile);
        }
     }
     //print_r($toCCAssArray);
      $toCCUserArray=array_diff($toCCUserArray,$notUserArray);

     $toBCCUserArray=explode(",", $toBCCUsers);
     foreach ($toBCCUserArray as $toBCCUser)
     {
        $toBCC=trim($toBCCUser);
        //$sub = DBQuery("SELECT up.profile FROM staff as s,user_profiles as up WHERE s.profile_id=up.id AND s.username='".$toBCC."'");
        $sub = DBQuery("SELECT * FROM login_authentication,user_profiles WHERE login_authentication.profile_id=user_profiles.id AND username='".$toBCC."'");
        $RET = DBGet($sub);
        if(Count($RET)==0)
        {
//            $student=DBQuery("SELECT * FROM students where username='".$toBCC."'");
//            $st=DBGet($student);
//            if(Count($st)!=0)
//            {
//                $toBCCProfile="student";
//                $toBCCAssArray=array_push_assoc($toBCCAssArray,$toBCCUser,$toBCCProfile);
//            }
//            else
//            {
                array_push($notUserArray, $toBCC);
//            }
        }
        else
        {
            $toBCCProfile=$RET[1]['PROFILE'];
            $toBCCAssArray=array_push_assoc($toBCCAssArray,$toBCCUser,$toBCCProfile);
        }
     }
     //print_r($toBCCAssArray);
     $toBCCUserArray=array_diff($toBCCUserArray,$notUserArray);

    $subject=$_REQUEST['txtSubj'];
    // echo $date=date("d/m/y  H:i:s", time());
    //$date=date("y/m/d  H:i:s", time());

    if($subject=='')
        $subject='No Subject';
 
    $mailBody=$_POST['txtBody'];
   
    $uploaded_file_count=count($_FILES['f']['name']);
    //$images=implode(",",$_FILES['f']['name']);
    for($i=0;$i<$uploaded_file_count;$i++)
    {
        $name=$_FILES['f']['name'][$i];
        if($name)
        {
        $path=$userName.'_'.time().rand(00,99).$name;
        $folder="./assets/".$path;
        $temp=$_FILES['f']['tmp_name'][$i];
        move_uploaded_file($temp,$folder);
        $arr[$i]=$folder;
        }
        else
            $attachment="";
    }
    //$attachment=implode(',../../assets/',$arr);
    //$attachment='../../assets/'.$attachment;
   
      $attachment=implode(',',$arr);
    
    $multipleUser='';
    $toAllowArr=array();
    foreach ($toAssArray as $userTo=>$profileTo)
    {
//            echo "<br/>";echo "<br/>";
//            echo "<br/>toProfile= ".$profileTo;
//            echo "<br/>current Profile= ".User('PROFILE');
//            echo "<br/>";echo "<br/>";
//            echo "to user ".$userTo;          
            if($profileTo=='admin')
            {
                array_push($toAllowArr, "yes");
                $toAssArray[$userTo]=$profileTo.",yes";
                if($multipleUser=="")
                    $multipleUser=trim($userTo);
                else 
                    $multipleUser=$multipleUser.",".trim($userTo);
            }
            else
            {
                if(User('PROFILE')=='admin')
                {
                    if($profileTo!='')
                    {
                        array_push($toAllowArr, "yes");
                        $toAssArray[$userTo]=$profileTo.",yes";
                        if($multipleUser=="")
                            $multipleUser=trim($userTo);
                        else 
                            $multipleUser=$multipleUser.",".trim($userTo);
                    }

                }
                if (User('PROFILE')=='teacher')
                {                        
                    $teacher_id= UserID();
//                    echo $teacher_id;
                    $studentNameArray=array();
                   // $sql='Select username from students where username is not null and student_id IN(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.teacher_id=\''.$teacher_id.'\')';
                    $sql='Select username from login_authentication INNER JOIN students on user_id=student_id where  profile_id=3 and username IS NOT NULL and student_id IN(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.teacher_id=\''.$teacher_id.'\')';
                    $studentNameArray=  DBGet(DBQuery($sql));
//                  echo "<br> scheduled student";
//                  print_r($studentNameArray);
                    //$sql1='SELECT username FROM staff WHERE username IS NOT NULL AND staff_id IN (SELECT DISTINCT staff_id FROM students_join_users WHERE student_id IN (SELECT student_id FROM students WHERE username IS NOT NULL AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                    //$sql1='SELECT username FROM staff WHERE username IS NOT NULL AND staff_id IN (SELECT DISTINCT staff_id FROM students_join_users WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                  $sql1='SELECT username FROM login_authentication WHERE profile_id=4 and username IS NOT NULL AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                    $parentNameArray=DBGet(DBQuery($sql1));
//                  echo "<br> scheduled student s parent";
//                  print_r($parentNameArray);
                  
                        $toUser=trim($userTo);
                        $flag=0;
                        if($profileTo=='student')
                        {
                        foreach($studentNameArray as $studentNameArr)
                        {
                            foreach($studentNameArr as $studentName)
                            {
                                if($toUser==$studentName)
                                {
                                    $flag=1;
                                if($multipleUser=="")
                                    $multipleUser=trim($toUser);
                                else 
                                    $multipleUser=$multipleUser.",".trim($toUser);
                                }
                            }
                        }
                        }
                        if($profileTo=='parent')
                        {
                        foreach($parentNameArray as $parentNameArr)
                        {
                            foreach($parentNameArr as $parentName)
                            {
                                if($toUser==$parentName)
                                {
                                    $flag=1;
                                if($multipleUser=="")
                                    $multipleUser=trim($toUser);
                                else 
                                    $multipleUser=$multipleUser.",".trim($toUser);
                                }
                            }
                        }
                        }
                        if($flag==1)
                        {
                            $toAssArray[$userTo]=$profileTo.",yes";
                        }
                        else 
                        {
                            $toAssArray[$userTo]=$profileTo.",no";
                        }
                    
                }
               
                if (User('PROFILE')=='parent')//to teacher only
                {
                    $parent_id=UserID();
                   // echo $parent_id;
                    $teacherNameArray=array();
                    //$sql='Select username from staff where staff_id IN(Select distinct student_id from students_join_users where staff_id=\''.$parent_id.'\'))';
                    $sql='Select username from login_authentication where username is not null and profile_id=2 and user_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where student_id in(Select student_id from students where student_id IN(select student_id from students_join_people where person_id=\''.$parent_id.'\')))';
                    $teacherNameArray=DBGet(DBQuery($sql));
//                      
                    $toUser=trim($userTo);
                    $flag=0;
                    if($profileTo=='teacher')
                    {
                        foreach($teacherNameArray as $teacherNameArr)
                        {
                            foreach($teacherNameArr as $teacherName)
                            {
                                if($toUser==$teacherName)
                                {
                                    $flag=1;
                                if($multipleUser=="")
                                    $multipleUser=trim($toUser);
                                else 
                                    $multipleUser=$multipleUser.",".trim($toUser);
                                }
                            }
                        }
                   
                   }
                    if($flag==1)
                    {
                        $toAssArray[$userTo]=$profileTo.",yes";
                    }
                    else 
                    {
                        $toAssArray[$userTo]=$profileTo.",no";
                    }
                    

                }
                if (User('PROFILE')=='student')
                {
                    $studentId=UserStudentID();
                    $teacherNameArray=array();
                    //$sql='Select username from staff where staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=\''.$studentId.'\')';
                    $sql='Select username from login_authentication where username is not null and profile_id=2 and user_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=\''.$studentId.'\')';
                    //echo $sql;
                    $teacherNameArray=  DBGet(DBQuery($sql));
                   // print_r($teacherNameArray);
                   $toUser=trim($userTo);
                   $flag=0;
                   if($profileTo=='teacher')
                   { 
                        foreach($teacherNameArray as $teacherNameArr)
                        {
                            foreach($teacherNameArr as $teacherName)
                            {
                                if($toUser==$teacherName)
                                {
                                $flag=1;
                                if($multipleUser=="")
                                    $multipleUser=trim($toUser);
                                else 
                                    $multipleUser=$multipleUser.",".trim($toUser);
                                }
                            }
                        }
                       
                   }
                    if($flag==1)
                    {
                            $toAssArray[$userTo]=$profileTo.",yes";
                    }
                    else 
                    {
                            $toAssArray[$userTo]=$profileTo.",no";
                    }
                    
                }
        }
    }
    //echo "hello ".$multipleUser;
    
    $multipleCCUser='';
    
     foreach ($toCCAssArray as $userCCTo=>$profileCCTo)
    {
//            echo "<br/>";echo "<br/>";
//            echo "<br/>toCCProfile= ".$profileCCTo;
//            echo "<br/>current Profile= ".User('PROFILE');
//            echo "<br/>";echo "<br/>";
//            echo "to user ".$userCCTo;          
            if($profileCCTo=='admin')
            {
                array_push($toAllowArr, "yes");
                 $toCCAssArray[$userCCTo]=$profileCCTo.",yes";
                if($multipleCCUser=="")
                    $multipleCCUser=trim($userCCTo);
                else 
                    $multipleCCUser=$multipleCCUser.",".trim($userCCTo);
            }
            else
            {
                if(User('PROFILE')=='admin')
                {
                    if($profileCCTo!='')
                    {
                        array_push($toAllowArr, "yes");
                         $toCCAssArray[$userCCTo]=$profileCCTo.",yes";
                        if($multipleCCUser=="")
                            $multipleCCUser=trim($userCCTo);
                        else 
                            $multipleCCUser=$multipleCCUser.",".trim($userCCTo);
                    }
                }
                if (User('PROFILE')=='teacher')
                {                        
                    $teacher_id= UserID();
                    //echo "<br/><br/> testing".$teacher_id;
                    $studentNameArray=array();
//                    $sql='Select username from students where username is not null and student_id IN(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.teacher_id=\''.$teacher_id.'\')';
                    $sql='Select username from login_authentication INNER JOIN students on user_id=student_id where  profile_id=3 and username IS NOT NULL and student_id IN(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.teacher_id=\''.$teacher_id.'\')';
                    $studentNameArray=  DBGet(DBQuery($sql));
//                  echo "<br> scheduled student";
//                  print_r($studentNameArray);                   
                   // $sql1='Select username from staff where username is not null and staff_id IN(Select distinct student_id from students_join_users where staff_id=\''.$teacher_id.'\')';
                     //$sql1='SELECT username FROM staff WHERE username IS NOT NULL AND staff_id IN (SELECT DISTINCT staff_id FROM students_join_users WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                    $sql1='SELECT username FROM login_authentication WHERE profile_id=4 and username IS NOT NULL AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                    $parentNameArray=DBGet(DBQuery($sql1));
//                  echo "<br> scheduled student s parent";
//                  print_r($parentNameArray);
//                    
                        $toCCUser=trim($userCCTo);
                        $flag=0;
                        if($profileCCTo=='student')
                        {
                        foreach($studentNameArray as $studentNameArr)
                        {
                            foreach($studentNameArr as $studentName)
                            {
                                if($toCCUser==$studentName)
                                {
                                    $flag=1;
                                if($multipleCCUser=="")
                                    $multipleCCUser=trim($toCCUser);
                                else 
                                    $multipleCCUser=$multipleCCUser.",".trim($toCCUser);
                                }
                            }
                        }
                        }
                        if($profileTo=='parent')
                        {
                           // $flag=0;
                        foreach($parentNameArray as $parentNameArr)
                        {
                            foreach($parentNameArr as $parentName)
                            {
                                if($toCCUser==$parentName)
                                {
                                    $flag=1;
                                if($multipleCCUser=="")
                                    $multipleCCUser=trim($toCCUser);
                                else 
                                    $multipleCCUser=$multipleCCUser.",".trim($toCCUser);
                                }
                            }
                        }
                        }
                        if($flag==1)
                             $toCCAssArray[$userCCTo]=$profileCCTo.",yes";
                        else
                             $toCCAssArray[$userCCTo]=$profileCCTo.",no";
                    
                }
               
                if (User('PROFILE')=='parent')//to teacher only
                {
                    $parent_id=UserID();
                   // echo "<br/><br/> testing".$parent_id;
                    $teacherNameArray=array();
                    $sql='Select username from staff where staff_id IN(Select distinct student_id from students_join_users where staff_id=\''.$parent_id.'\'))';
                    $teacherNameArray=DBGet(DBQuery($sql));
//                      
                    $toCCUser=trim($userCCTo);
                    $flag=0;
                    if($profileCCTo=='teacher')
                    {
//                      $flag=0;
                        foreach($teacherNameArray as $teacherNameArr)
                        {
                            foreach($teacherNameArr as $teacherName)
                            {
                                if($toCCUser==$teacherName)
                                {
                                $flag=1;
                                if($multipleCCUser=="")
                                    $multipleCCUser=trim($toCCUser);
                                else 
                                    $multipleCCUser=$multipleCCUser.",".trim($toCCUser);
                                }
                            }
                        }

                   }
                   if($flag==1)
                             $toCCAssArray[$userCCTo]=$profileCCTo.",yes";
                   else
                             $toCCAssArray[$userCCTo]=$profileCCTo.",no";

                }
                if (User('PROFILE')=='student')
                {
                    $studentId=UserStudentID();
                    $teacherNameArray=array();
                    //$sql='Select username from staff where staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=\''.$studentId.'\')';
                    $sql='Select username from login_authentication where username is not null and profile_id=2 and user_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=\''.$studentId.'\')';
                    //echo $sql;
                    $teacherNameArray=  DBGet(DBQuery($sql));
                   // print_r($teacherNameArray);
                   $toCCUser=trim($userCCTo);
                   $flag=0;
                   if($profileCCTo=='teacher')
                   {
                        foreach($teacherNameArray as $teacherNameArr)
                        {
                            foreach($teacherNameArr as $teacherName)
                            {
                                if($toCCUser==$teacherName)
                                {
                                $flag=1;
                                if($multipleCCUser=="")
                                    $multipleCCUser=trim($toCCUser);
                                else 
                                    $multipleCCUser=$multipleCCUser.",".trim($toCCUser);
                                }
                            }
                        }
                   }  
                   if($flag==1)
                             $toCCAssArray[$userCCTo]=$profileCCTo.",yes";
                   else
                             $toCCAssArray[$userCCTo]=$profileCCTo.",no";
                }
        }
    }
    
    //echo "<br/><br/>hello ".$multipleCCUser;
    
    $multipleBCCUser='';
   // print_r($BCCprofileArr);
    foreach ($toBCCAssArray as $userBCCTo=>$profileBCCTo)
    {
//            echo "<br/>";echo "<br/>";
//            echo "<br/>toCCProfile= ".$profileCCTo;
//            echo "<br/>current Profile= ".User('PROFILE');
//            echo "<br/>";echo "<br/>";
//            echo "to user ".$userCCTo;          
            if($profileBCCTo=='admin')
            {
                array_push($toAllowArr, "yes");
                 $toBCCAssArray[$userBCCTo]=$profileBCCTo.",yes";
                if($multipleBCCUser=="")
                    $multipleBCCUser=trim($userBCCTo);
                else 
                    $multipleBCCUser=$multipleBCCUser.",".trim($userBCCTo);
            }
            else
            {
                if(User('PROFILE')=='admin')
                {
                    if($profileBCCTo!='')
                    {
                        array_push($toAllowArr, "yes");
                         $toBCCAssArray[$userBCCTo]=$profileBCCTo.",yes";
                        if($multipleBCCUser=="")
                            $multipleBCCUser=$userBCCTo;
                        else 
                            $multipleBCCUser=$multipleBCCUser.",".trim($userBCCTo);
                    }

                }
                if (User('PROFILE')=='teacher')
                {                        
                    $teacher_id= UserID();
                    //echo "<br/><br/> testing".$teacher_id;
                    $studentNameArray=array();
                    //$sql='Select username from students where username is not null and student_id IN(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.teacher_id=\''.$teacher_id.'\')';
                    $sql='Select username from login_authentication INNER JOIN students on user_id=student_id where  profile_id=3 and username IS NOT NULL and student_id IN(Select distinct student_id from course_periods INNER JOIN schedule using(course_period_id) where course_periods.teacher_id=\''.$teacher_id.'\')';
                    $studentNameArray=  DBGet(DBQuery($sql));
//                    echo "<br> scheduled student";
//                    print_r($studentNameArray);                   
                    //$sql1='Select username from staff where username is not null and staff_id IN(Select distinct student_id from students_join_users where staff_id=\''.$teacher_id.'\')';
                     //$sql1='SELECT username FROM staff WHERE username IS NOT NULL AND staff_id IN (SELECT DISTINCT staff_id FROM students_join_users WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                    $sql1='SELECT username FROM login_authentication WHERE profile_id=4 and username IS NOT NULL AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                    $parentNameArray=DBGet(DBQuery($sql1));
//                    echo "<br> scheduled student s parent";
//                    print_r($parentNameArray);
//                    
                        $toBCCUser=trim($userBCCTo);
                        $flag=0;
                        if($profileBCCTo=='student')
                        {
                        foreach($studentNameArray as $studentNameArr)
                        {
                            foreach($studentNameArr as $studentName)
                            {
                                if($toBCCUser==$studentName)
                                {
                                $flag=1;
                                if($multipleBCCUser=="")
                                    $multipleBCCUser=$toBCCUser;
                                else 
                                    $multipleBCCUser=$multipleBCCUser.",".$toBCCUser;
                                }
                            }
                        }                       
                        }
                        if($profileTo=='parent')
                        {
                        foreach($parentNameArray as $parentNameArr)
                        {
                            foreach($parentNameArr as $parentName)
                            {
                                if($toBCCUser==$parentName)
                                {
                                    $flag=1;
                                if($multipleBCCUser=="")
                                    $multipleBCCUser=$toBCCUser;
                                else 
                                    $multipleBCCUser=$multipleBCCUser.",".$toBCCUser;
                                }
                            }
                        }                      
                        }
                     if($flag==1)
                             $toBCCAssArray[$userBCCTo]=$profileBCCTo.",yes";
                     else
                             $toBCCAssArray[$userBCCTo]=$profileBCCTo.",no";
                }
               
                if (User('PROFILE')=='parent')//to teacher only
                {
                    $parent_id=UserID();
                   // echo "<br/><br/> testing".$parent_id;
                    $teacherNameArray=array();
                    //$sql='Select username from staff where staff_id IN(Select distinct student_id from students_join_users where staff_id=\''.$parent_id.'\'))';
                    $sql='Select username from login_authentication where username is not null and profile_id=2 and user_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where student_id in(Select student_id from students where student_id IN(select student_id from students_join_people where person_id=\''.$parent_id.'\')))';
                    $teacherNameArray=DBGet(DBQuery($sql));
//                      
                    $toBCCUser=trim($userBCCTo);
                    $flag=0;
                    if($profileCCTo=='teacher')
                    {
                        foreach($teacherNameArray as $teacherNameArr)
                        {
                            foreach($teacherNameArr as $teacherName)
                            {
                                if($toBCCUser==$teacherName)
                                {
                                    $flag=1;
                                if($multipleBCCUser=="")
                                    $multipleBCCUser=$toBCCUser;
                                else 
                                    $multipleBCCUser=$multipleBCCUser.",".$toBCCUser;
                                }
                            }
                        }                       
                   }
                   if($flag==1)
                             $toBCCAssArray[$userBCCTo]=$profileBCCTo.",yes";
                   else
                             $toBCCAssArray[$userBCCTo]=$profileBCCTo.",no";

                }
                if (User('PROFILE')=='student')
                {
                    $studentId=UserStudentID();
                    $teacherNameArray=array();
                    //$sql='Select username from staff where staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=\''.$studentId.'\')';
                    $sql='Select username from login_authentication where username is not null and profile_id=2 and user_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=\''.$studentId.'\')';
                    $teacherNameArray=  DBGet(DBQuery($sql));
                   // print_r($teacherNameArray);
                   $toBCCUser=trim($userBCCTo);
                   $flag=0;
                   if($profileBCCTo=='teacher')
                   {                       
                        foreach($teacherNameArray as $teacherNameArr)
                        {
                            foreach($teacherNameArr as $teacherName)
                            {
                                if($toBCCUser==$teacherName)
                                {
                                    $flag=1;
                                if($multipleBCCUser=="")
                                    $multipleBCCUser=$toBCCUser;
                                else 
                                    $multipleBCCUser=$multipleBCCUser.",".$toBCCUser;
                                }
                            }
                        }                        
                   }  
                   if($flag==1)
                             $toBCCAssArray[$userBCCTo]=$profileBCCTo.",yes";
                   else
                             $toBCCAssArray[$userBCCTo]=$profileBCCTo.",no";
                }
        }
    }
    
    //echo "<br/>hello ".$multipleBCCUser;
    $notUserArray=  array_filter($notUserArray);
    $multipleUserArr=  explode(",", $multipleUser);
    $multipleUserArr=  array_unique($multipleUserArr);
    $multipleUser=  implode(",", $multipleUserArr);
    
    $multipleCCUserArr=  explode(",", $multipleCCUser);
    $multipleCCUserArr=  array_unique($multipleCCUserArr);
    $multipleCCUser=  implode(",", $multipleCCUserArr);
    
    $multipleBCCUserArr=  explode(",", $multipleBCCUser);
    $multipleBCCUserArr=  array_unique($multipleBCCUserArr);
    $multipleBCCUser=  implode(",", $multipleBCCUserArr);
    if($multipleUser!="")
    {
        $toArr=  explode(",", $multipleUser);
        $toCCArr=  explode(",", $multipleCCUser);
        $toBCCArr=  explode(",", $multipleBCCUser);

       foreach ($notUserArray as $notUser)
       {
           if(($key = array_search($notUser, $toArr)) !== false) 
           {
                unset($toArr[$key]);
           }
       }

       foreach ($notUserArray as $notUser)
       {
           if(($key = array_search($notUser, $toCCArr)) !== false) 
           {
                unset($toCCArr[$key]);
           }
       }
       foreach ($notUserArray as $notUser)
       {
           if(($key = array_search($notUser, $toBCCArr)) !== false) 
           {
                unset($toBCCArr[$key]);
           }
       }
       $multipleUser=  implode(",", $toArr);
       $multipleCCUser=  implode(",", $toCCArr);
       $multipleBCCUser=  implode(",", $toBCCArr);
       $mailBody = htmlspecialchars($mailBody) ;
       SendMail($multipleUser, $userName, $subject, $mailBody, $attachment,$multipleCCUser,$multipleBCCUser,$grpName);

        $notAllowArr=array();
        foreach ($toAssArray as $userTo=>$profileTo)
        {
            $chkallowUserArr=explode(",",$profileTo);
//            echo "<br/>";
//            print_r($chkallowUserArr);
            foreach($chkallowUserArr as $chk)
            {
                  if($chk=='no')
                    array_push($notAllowArr,$userTo);
            }
        }
        foreach ($toCCAssArray as $userCCTo=>$profileCCTo)
        {
            $chkallowUserArr=explode(",",$profileCCTo);
//            echo "<br/>";
//            print_r($chkallowUserArr);
            foreach($chkallowUserArr as $chk)
            {
                  if($chk=='no')
                    array_push($notAllowArr,$userCCTo);
            }
        }
        foreach ($toBCCAssArray as $userBCCTo=>$profileBCCTo)
        {
            $chkallowUserArr=explode(",",$profileBCCTo);
//            echo "<br/>";
//            print_r($chkallowUserArr);
            foreach($chkallowUserArr as $chk)
            {
                  if($chk=='no')
                    array_push($notAllowArr,$userBCCTo);
            }
        }
       // print_r($notAllowArr);
        $notAllowArr=  array_filter($notAllowArr);
        $notAllowArr=  array_unique($notAllowArr);
        if(count($notAllowArr)>0)
             echo "<br/><br/>Message was not sent to ".implode(",",$notAllowArr);
        
        
        $notUserArray=array_filter($notUserArray);
       
        if(count($notUserArray)!=0)
        {
        $notUser=  implode(",", $notUserArray);
        if($notUser!="")
        {
            echo "<br/><br/>Message was not sent to ".$notUser." as they not exist";
        }
        }
    }
    else 
    {
        $notUserArray=  array_filter($notUserArray);
        $noUser=  implode(",", $notUserArray);
        echo "Message was not sent to ".$noUser." as they not exist";  
        // echo '<div style=text-align:centre><table cellpadding=5 cellspacing=5 class=alert_box ><tr><td class=alert></td><td class=alert_msg ><b>message not sent</b></td></tr><tr><td colspan=2 class=clear></td></tr></table></div>';
    }
    
 }
function output_file($file, $name, $mime_type='',$mod_file)
{
if(!is_readable($file)) die('File not found or inaccessible!');

$size = filesize($file);
$name = rawurldecode($name);
$known_mime_types=array(
"pdf" => "application/pdf",
"txt" => "text/plain",
"html" => "text/html",
"htm" => "text/html",
"exe" => "application/octet-stream",
"zip" => "application/zip",
"doc" => "application/msword",
"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
"xls" => "application/vnd.ms-excel",
"ppt" => "application/vnd.ms-powerpoint",//application/vnd.ms-powerpoint",
"pptx" =>"application/vnd.openxmlformats-officedocument.presentationml.presentation",//application/vnd.ms-powerpoint",
"gif" => "image/gif",
"png" => "image/png",
"jpeg"=> "image/jpeg",
"jpg" => "image/jpg",
"php" => "text/plain"
);
if($mime_type==''){
$file_extension = strtolower(substr(strrchr($file,"."),1));
if(array_key_exists($file_extension, $known_mime_types)){
$mime_type=$known_mime_types[$file_extension];
} else {
$mime_type="application/force-download";
};
};

@ob_end_clean();


if(ini_get('zlib.output_compression'))
ini_set('zlib.output_compression', 'Off');
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="'.$mod_file.'"');
header("Content-Transfer-Encoding: binary");
header('Accept-Ranges: bytes');
header("Cache-control: private");
header('Pragma: private');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
if(isset($_SERVER['HTTP_RANGE']))
{
list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
list($range) = explode(",",$range,2);
list($range, $range_end) = explode("-", $range);
$range=intval($range);
if(!$range_end) {
$range_end=$size-1;
} else {
$range_end=intval($range_end);
}
$new_length = $range_end-$range+1;
header("HTTP/1.1 206 Partial Content");
header("Content-Length: $new_length");
header("Content-Range: bytes $range-$range_end/$size");
} else {
$new_length=$size;
header("Content-Length: ".$size);
}
$chunksize = 1*(1024*1024);
$bytes_send = 0;
if ($file = fopen($file, 'r'))
{
if(isset($_SERVER['HTTP_RANGE']))
fseek($file, $range);

while(!feof($file) &&
(!connection_aborted()) &&
($bytes_send<$new_length)
)
{
$buffer = fread($file, $chunksize);
print($buffer);
flush();
$bytes_send += strlen($buffer);
}
fclose($file);
} else

die('Error - can not open file.');
die();
}
?>