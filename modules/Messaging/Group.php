<?php
$curProfile= User('PROFILE');
$userName=  User('USERNAME');

if(!isset($_REQUEST['modfunc']))
{
echo "<FORM name=Group id=Group action=Modules.php?modname=$_REQUEST[modname]&modfunc=group method=POST >";
PopTable('header','Group');
echo "<table align='right'><tr><td><a href='#' onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=add_group\");'>".button('add')."</a></td><td>Add Group</td></tr></table>";
echo '<TABLE style="overflow:auto; width:820px;" >';
    $select="SELECT mail_group . * , members FROM mail_group LEFT OUTER JOIN (SELECT COUNT( id ) AS members, group_id AS group_id FROM mail_groupmembers GROUP BY group_id) AS members ON members.group_id = mail_group.group_id WHERE USER_NAME ='$userName'";
    $link['GROUP_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=groupmember";
    $link['GROUP_NAME']['variables'] = array('group_id'=>'GROUP_ID');       
    $columns=array('GROUP_NAME'=>'Group Name','DESCRIPTION'=>'Description','CREATION_DATE'=>'Create Date','MEMBERS'=>'Members','action'=>'Action');
    $list = DBGet(DBQuery($select));
    foreach($list as $id=>$value)
    {
        if($list[$id]['MEMBERS']=="")
        $list[$id]['MEMBERS']=0;
        if($list[$id]['action']=="")
        {
            $list[$id]['action']="<a href='Modules.php?modname=$_REQUEST[modname]&modfunc=groupmember&group_id=$value[GROUP_ID]'>".button('edit')."</a>&nbsp;&nbsp;<a href='Modules.php?modname=$_REQUEST[modname]&modfunc=delete&group_id=$value[GROUP_ID]'>".button('remove')."</a>";
        }
    }
    ListOutput( $list,$columns,'Group','Groups',$link,array(),array('search'=>false),'');
 PopTable('footer');
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='delete')
{
$group_id= $_REQUEST['group_id'];
$members=  DBGet (DBQuery ("select count(*) as countmember from mail_groupmembers where group_id=".$group_id.""));
$count_members=$members[1]['COUNTMEMBER'];
if($count_members>0)
{
    if(DeleteMail("group with ".$count_members." groupmembers",'delete',$_REQUEST['modname'],true))
    {
        $member_del="delete from mail_groupmembers where group_id=".$group_id."";
        $member_del_execute=  DBQuery($member_del);
        $mail_delete="delete from mail_group where group_id =".$group_id."";
        $mail_delete_ex=DBQuery($mail_delete);
        unset($_REQUEST['modfunc']);
        echo "<script>window.location='Modules.php?modname=Messaging/Group.php'</script>";
    }
}
 else 
{
    if(DeleteMail('group','delete',$_REQUEST['modname'],true))
    {       
        $mail_delete="delete from mail_group where group_id =".$group_id."";
        $mail_delete_ex=DBQuery($mail_delete);
        unset($_REQUEST['modfunc']);
        echo "<script>window.location='Modules.php?modname=Messaging/Group.php'</script>";
    }
}
    unset($_REQUEST['modfunc']);
}

 if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='groupmember')
{
 PopTable('header','Group Members');
 echo "<FORM name=sav id=sav action=Modules.php?modname=$_REQUEST[modname]&modfunc=members&groupid=$_REQUEST[group_id] method=POST>";

 echo '<div style="overflow:auto; width:820px;">';
 echo "<div id='members'>";
        
$member="select * from mail_groupmembers where GROUP_ID='".$_REQUEST['group_id']."'";
$member_list=DBGet(DBQuery($member));
foreach($member_list as $key=>$value)
{
    $member_list[$key]['PROFILE'];
    $select="SELECT * FROM user_profiles WHERE ID='".$member_list[$key]['PROFILE']."'";
    $profile=DBGet(DBQuery($select));
    $member_list[$key]['PROFILE']=$profile[1]['PROFILE'];
}
$columns=array('USER_NAME'=>'User Name','PROFILE'=>'Profile');
$extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
$extra['LO_group'] = array('GROUP_ID');
$extra['columns_before']= array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'group\');" checked><A>');
$extra['new'] = true;
    if(is_array($extra['columns_before']))
	{
		$LO_columns = $extra['columns_before'] + $columns;
		$columns = $LO_columns;
                
        }
foreach($member_list as $id=>$value)
{
    $extra['columns_before']['CHECKBOX'] = "<INPUT type=checkbox name=group[".$value['ID']."] value=Y CHECKED>";
    $member_list[$id]=$extra['columns_before']+$value;
}
$group="select GROUP_NAME,DESCRIPTION from mail_group where GROUP_ID=$_REQUEST[group_id]";
$groupDetails=DBGet(DBQuery($group));
$groupname=$groupDetails[1]['GROUP_NAME'];
$groupdesc=$groupDetails[1]['DESCRIPTION'];

echo '<table><tr><td>Group Name:'.'</td>';
//if(count($member_list)!=0)
//{
echo '<td><div id=group_name><div onclick=\'javascript:addHTML("<TABLE><TR><TD>'.str_replace('"','\"',TextInput_mail($groupname,'groupname','','maxlength=50 style="font-size:12px;"',false)).'</TD></TR></TABLE>","group_name",true);\'>'.$groupname.'</div></div></td>';
echo '<tr><td>Description:'.'</td>';
echo '<td><div id=group_desc><div onclick=\'javascript:addHTML("<TABLE><TR><TD>'.str_replace('"','\"',TextInput_mail($groupdesc,'groupdesc','','maxlength=50 style="font-size:12px;"',false)).'</TD></TR></TABLE>","group_desc",true);\'>'.$groupdesc.'</div></div></td>';
//}
//else 
//{
//    echo '<td><div>'.NoInput($groupname).'</div></td>';
//    echo '<tr><td>Description:'.'</td>';
//    echo '<td><div>'.NoInput($groupdesc).'</div></td>';
//}
//for($i=0;$i<strlen($groupname);$i++)
//{
//    if($groupname[$i]!=" " || $groupname[$i]!="'")
//        $grp=str_replace("","",$groupname);
//}
for($i=0;$i<strlen($groupname);$i++)
{
    if($groupname[$i]==" ")
    $groupname[$i]=str_replace(" ", "_",$groupname[$i]);
    else if($groupname[$i]=="'")
    $groupname[$i]=str_replace("'", "\\",$groupname[$i]);

}
$grp=$groupname;
//$groupdesc=strtolower($groupdesc);
if($groupdesc=='No Description')
    $groupdesc='No Description';
else
{
    for($i=0;$i<strlen($groupdesc);$i++)
    {
        if($groupdesc[$i]==" ")
        $groupdesc[$i]=str_replace(" ", "_",$groupdesc[$i]);
        else if($groupdesc[$i]=="'")
        $groupdesc[$i]=str_replace("'", "\\",$groupdesc[$i]);

    }
}
//echo $grp;
//echo addslashes(htmlspecialchars($groupname));
echo '<td align="right"></td>';
echo '</tr></table><table align="right"><tr><td><a href=Modules.php?modname='.$_REQUEST[modname].'&modfunc=exist_group&group_name='.$grp.'&desc='.$groupdesc.'>'.button('add').'</a></td><td>Add Member</td></tr></table>';
ListOutput( $member_list,$columns,'Member','Members','',array(),array('search'=>false,'save'=>false),'');
echo "</div>";
        echo "</div>";
//       if(count($member_list)!=0)
        {
            if(isset($userName))
                echo '<table align="center" width="94%"><tr><td align="center"><INPUT type=submit class=btn_medium value=Save></td></tr></table>';
           
        }
        echo '</FORM>';
         PopTable('footer');

}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='exist_group')
{
    PopTable('header','Group Members');
    //$grp_name=str_replace("'", "\\'",$_REQUEST['group_name']);
    $grp_name=$_REQUEST['group_name'];
//    for($i=0;$i<strlen($groupname);$i++)
//{
//    if($groupname[$i]==" ")
//    $grp_name=str_replace(" ", "_",$groupname);
//    else if($groupname[$i]=="'")
//    $grp_name=str_replace("'", "\\",$groupname);
//    else
//        $grp_name=$groupname;
//
//}
    
    //echo$grp_name=str_replace("\\", "'",$_REQUEST['group_name']);
 // echo "hello ".;
    echo "<FORM name=search action=Modules.php?modname=$_REQUEST[modname]&modfunc=add_group_member&search=true&group_id=$grp_name&desc=$_REQUEST[desc] method=POST>";
		echo '<TABLE>';
		echo '<TR><TD align=right>Last Name</TD><TD><INPUT type=text class=cell_floating name=last></TD></TR>';
		echo '<TR><TD align=right>First Name</TD><TD><INPUT type=text class=cell_floating name=first></TD></TR>';
		echo '<TR><TD align=right>Username</TD><TD><INPUT type=text class=cell_floating name=username></TD></TR>';
                if(User('PROFILE')=='teacher')
                {
                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles where id!=2'));
                }
                else if(User('PROFILE')=='parent' ||User('PROFILE')=='student')
                {
                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles where id not in(3,4)'));
                }              
                else
                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles'));
                $options[-1]='N/A';
                //$options['none']='No Access';
                
                foreach($profiles as $key=>$value)
                {
                    $options[$value['ID']]=$value['TITLE'];
                }
		echo '<TR><TD align=right>Profile</TD><TD><SELECT name=profile>';
		foreach($options as $key=>$val)
			echo '<OPTION value="'.$key.'">'.$val;
		echo '</SELECT></TD></TR>';
		if($extra['search'])
			echo $extra['search'];
		echo '<TR><TD colspan=2 align=center>';
		
		if(User('PROFILE')=='admin')
			echo '<INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'>Search All Schools<BR>';
			echo '<INPUT type=checkbox name=_dis_user value=Y>Include Disabled User<BR><br>';
		
		echo "<INPUT type=SUBMIT class=btn_medium value='Submit'>&nbsp<INPUT type=RESET class=btn_medium value='Reset'>";
		echo '</TD></TR>';
		echo '</TABLE>';
		/********************for Back to user***************************/
                    echo '<input type=hidden name=sql_save_session_staf value=true />';
                /************************************************/
                echo '</FORM>';
                PopTable('footer');
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='add_group_member')
{
    $groupname=$_REQUEST['group_id'];
    $desc=$_REQUEST['desc'];
    for($i=0;$i<strlen($groupname);$i++)
    {
    if($groupname[$i]=="_")
    $groupname[$i]=str_replace("_", " ",$groupname[$i]);
    else if($groupname[$i]=="\\")
    $groupname[$i]=str_replace("\\", "'",$groupname[$i]);
    }
    $_REQUEST['group_id']=$groupname;
    
    if($desc=='No')
        $desc="";
    else
    {
        for($i=0;$i<strlen($desc);$i++)
        {
        if($desc[$i]=="_")
            $desc[$i]=str_replace("_", " ",$desc[$i]);
        else if($desc[$i]=="\\")
            $desc[$i]=str_replace("\\", "'",$desc[$i]);
        }
    }
    /*if(strlen($_REQUEST['group_id']))
    {
        $arr=explode('_',$_REQUEST['group_id']);
        $grp=implode(' ',$arr);
        //echo$grp=str_replace("\\", "'",$_REQUEST['group_id']);
        $_REQUEST['group_id']=$grp;
    }*/
     
//    if($_REQUEST['desc']=='No')
//        $desc="";
//    else
//        $desc=$_REQUEST['desc'];
    echo "<FORM name=Group id=Compose action=Modules.php?modname=$_REQUEST[modname]&modfunc=member_insert method=POST >";
PopTable('header','Group');
echo '<table>
      <tr>
      <td>Group Name: </td>
      <td>'.TextInput_mail($_REQUEST['group_id'],'txtExistGrpName','','class=cell_medium readonly').'
      </td>
      </tr>
      <tr>
      <td>Description: </td>
      <td>'.TextInput_mail($desc,'txtExistGrpDesc','','class=cell_medium readonly').'
      </td>
      </tr>
      <tr>
      <td colspan=2>';
     echo  DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_wide VALUE='Add Members' onclick='return mail_group_chk();'/>")
       . '</td>
        </tr>
        </table>';
     $lastName=$_REQUEST['last'];
     $firstName=$_REQUEST['first'];
     $userName=$_REQUEST['username'];
     $profile=$_REQUEST['profile'];
     $disable=$_REQUEST['_dis_user'];
     $allschools=$_REQUEST['_search_all_schools'];
     //echo "<br> disable: ".$disable."<br>";
     echo '<input type=hidden value='.$profile.' name=profile>';
      if(isset($_REQUEST['group_id']))  
      {
          $select1="select * from mail_group where GROUP_NAME='".str_replace("'", "\\'",$_REQUEST['group_id'])."'";
          $groupselect=DBGet(DBQuery($select1));
             
          $member="select * from mail_groupmembers where GROUP_ID=".$groupselect[1]['GROUP_ID']."";
          $existuser=DBGet(DBQuery($member));
          $existuser=DBGet(DBQuery($member));
          foreach($existuser as $id=>$value)
          {
              $usernames[]=array('PROFILE_ID'=>$existuser[$id]['PROFILE'],'USERNAME'=>$existuser[$id]['USER_NAME']);                           
          }
         // print_r($usernames);
          foreach($usernames as $id=>$value)
          {
               if($value['PROFILE_ID']!=3 ||$value['PROFILE_ID']!=4)
               {
                    $staff="select * from login_authentication,staff where login_authentication.user_id=staff.staff_id and USERNAME='$value[USERNAME]' and login_authentication.profile_id not in(3)";
                    $stafflist=DBGet(DBQuery($staff));
                    $staff_id[]=$stafflist[1]['STAFF_ID'];
               }
               if($value['PROFILE_ID']==3)
               {                               
                    $stu="select * from login_authentication,students where login_authentication.user_id=students.student_id and profile_id=3 and USERNAME='$value[USERNAME]'";                              
                    $stulist=DBGet(DBQuery($stu));
                    $stu_id[]=$stulist[1]['STUDENT_ID'];    
               }
                            
          }  
          $staff_id=  array_filter($staff_id);
          $stu_id= array_filter($stu_id);
//        echo "<br> staff_id array ";print_r($staff_id);
//        echo "<br> student_id array ";print_r($stu_id);
          if($profile!=-1)//search by profile
          {
              if($profile==3)//students
              {
                if(User('PROFILE')=='teacher')
                   $user="SELECT * FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";
                else   
                   $user="select * from students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].")";                
              }
              if($profile==2)//teachers
              {
                  if(User('PROFILE')=='parent')
                  {
                       $parent_id=UserID();
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id NOT IN (Select distinct person_id from students_join_people where person_id<>".$parent_id.")";
//                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id IN(Select distinct student_id from students_join_users where staff_id=".$parent_id.")";
                  }
                  if(User('PROFILE')=='student')
                  {
                       $studentId=UserStudentID();
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".$studentId.")";
                  }
                  if(User('PROFILE')=='admin')
                  {
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID']." )";
                  }
              }
              if($profile==4)//parents
              {
                  if (User('PROFILE')=='teacher')
                  {
                    $teacher_id= UserID();
                    $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                  }
                  if(User('PROFILE')=='admin')
                  {
                      $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' ';        
                  }
              }
              if($profile==0 ||$profile==1 ||$profile==5)//all types of admin
              {
                  $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=$profile and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].")";
              }
              if($lastName!="")
              {
                  $user=$user." AND LAST_NAME LIKE '$lastName%' ";
              }
              if($firstName!="")
              {
                  $user=$user." AND FIRST_NAME LIKE '$firstName%' ";
              }
              if($userName!="")
              {
                  $user=$user." AND USERNAME LIKE '$userName%' ";
              }
              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
              {
                  $user=$user." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
              }
              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
              {
                  $user=$user." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
              }
              if($disable=='Y')//with disabled users
              {
                  $user=$user." ";
              }
          }
          
          else 
          {           
               if(User('PROFILE')=='admin')//all types of admin
               {
                    $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id not in(3,4)";
                    $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,students WHERE login_authentication.user_id=students.student_id AND login_authentication.profile_id=3 AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id=3";
                    $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id=4";
               }
               if(User('PROFILE')=='teacher')//teachers
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id in(0,1,5)";//all types of admin
                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";//scheduled students
                   $user3='SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.UserID().'\')))';//parents                  
               }
               if(User('PROFILE')=='parent')//parents
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id in(0,1,5)";//all types of admin
                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id NOT IN (Select distinct person_id from students_join_people where staff_id<>".UserID().")";//parents                
//                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id IN(Select distinct student_id from students_join_users where staff_id=".UserID().")";//parents                
               }
               if(User('PROFILE')=='student')//students
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id in(0,1,5)";//all types of admin
                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".UserStudentID().")";//teachers                 
               }
              if($lastName!="")
              {
                  $user1=$user1." AND LAST_NAME LIKE '$lastName%' ";
                  $user2=$user2." AND LAST_NAME LIKE '$lastName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND LAST_NAME LIKE '$lastName%' ";
              }
              if($firstName!="")
              {
                  $user1=$user1." AND FIRST_NAME LIKE '$firstName%' ";
                  $user2=$user2." AND FIRST_NAME LIKE '$firstName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND FIRST_NAME LIKE '$firstName%' ";
              }
              if($userName!="")
              {
                  $user1=$user1." AND USERNAME LIKE '$userName%' ";
                  $user2=$user2." AND USERNAME LIKE '$userName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND USERNAME LIKE '$userName%' ";
              }
//              if($disable=='')//only enabled users
//              {
//                  $user1=$user1." AND TRIM( IFNULL( is_disable, '' ) ) = ''   ";
//                  $user2=$user2." AND TRIM( IFNULL( is_disable, '' ) ) = ''   ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                    $user3=$user3." AND TRIM( IFNULL( is_disable, '' ) ) = ''   ";
//              }
//              if($disable=='Y')//with disabled users
//              {
//                  $user1=$user1." AND TRIM( IFNULL( is_disable, '' ) ) <> ''   ";
//                  $user2=$user2." AND TRIM( IFNULL( is_disable, '' ) ) <> ''   ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                    $user2=$user2." AND TRIM( IFNULL( is_disable, '' ) ) <> ''   ";
//              }
              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
              {
                  $user1=$user1." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
                  $user2=$user2." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user3=$user3." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
              }
              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
              {
                  $user1=$user1." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
                  $user2=$user2." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user3=$user3." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
              }
              if($disable=='Y')//with disabled users
              {
                  $user1=$user1." ";
                  $user2=$user2." ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user2=$user2." ";
              }
              if(User('PROFILE')=='admin'|| User('PROFILE')=='teacher')
                 $user=$user1." UNION ALL ".$user2." UNION ALL ".$user3;
             else 
                 $user=$user1." UNION ALL ".$user2;
          }
          
//          echo "<br>".$user."<br>";
              $userlist=DBGet(DBQueryMod($user)); 
//              print_r($userlist);
              
              foreach($userlist as $key=>$value)
              {
                    $select="SELECT * FROM user_profiles WHERE ID='".$userlist[$key]['PROFILE_ID']."'";
                    $profile=DBGet(DBQuery($select));
                    $userlist[$key]['FIRST_NAME']=$userlist[$key]['LAST_NAME'].' '.$userlist[$key]['FIRST_NAME'];
                    $userlist[$key]['PROFILE_ID']=$profile[1]['PROFILE'];
              }
              if($_REQUEST['_dis_user']=='Y')
              $columns=array('FIRST_NAME'=>'Member','USERNAME'=>'User Name','PROFILE_ID'=>'Profile','STATUS'=>'Status');
              else
              $columns=array('FIRST_NAME'=>'Member','USERNAME'=>'User Name','PROFILE_ID'=>'Profile');
              $extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
              $extra['LO_group'] = array('STAFF_ID');
              $extra['columns_before']= array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'groups\');"><A>');
              $extra['new'] = true;
              if(is_array($extra['columns_before']))
              {
                    $LO_columns = $extra['columns_before'] + $columns;
                    $columns = $LO_columns;
              }
              foreach($userlist as $id=>$value)
              {
                    $extra['columns_before']['CHECKBOX'] = "<INPUT type=checkbox name=groups[".$value['USER_ID'].",".$value['PROFILE_ID']."] value=Y>";
                    $userlist[$id]=$extra['columns_before']+$value;
              }
              if($_REQUEST['_dis_user']=='Y')
              {
                  foreach($userlist as $ui=>$ud)
                  {
                      if($ud['PROFILE_ID']=='student')                     
                      $chck_status=DBGet(DBQuery('SELECT COUNT(1) as DISABLED FROM students s,student_enrollment se WHERE se.STUDENT_ID=s.STUDENT_ID AND s.STUDENT_ID='.$ud['USER_ID'].' AND se.SYEAR='.UserSyear().' AND (s.IS_DISABLE=\'Y\' OR (se.END_DATE<\''.date('Y-m-d').'\'  AND se.END_DATE IS NOT NULL AND se.END_DATE<>\'0000-00-00\' ))'));
                      elseif($ud['PROFILE_ID']=='parent')
                      $chck_status=DBGet(DBQuery('SELECT COUNT(1) as DISABLED FROM people WHERE STAFF_ID='.$ud['USER_ID'].' AND IS_DISABLE=\'Y\' '));   
                      else
                      $chck_status=DBGet(DBQuery('SELECT COUNT(1) as DISABLED FROM staff s,staff_school_relationship se WHERE se.STAFF_ID=s.STAFF_ID AND s.STAFF_ID='.$ud['USER_ID'].' AND se.SYEAR='.UserSyear().' AND (s.IS_DISABLE=\'Y\' OR (se.END_DATE<\''.date('Y-m-d').'\'  AND se.END_DATE IS NOT NULL AND se.END_DATE<>\'0000-00-00\' ))'));
                      
                      
                        if($chck_status[1]['DISABLED']!=0)
                        $userlist[$ui]['STATUS']="<font style='color:red'>Inactive</font>";   
                        else
                        $userlist[$ui]['STATUS']="<font style='color:green'>Active</font>";
                  }
              }
              ListOutputExcel( $userlist,$columns,'Member','Members','',array(),array('search'=>false),'');                          
          
      }
      
     echo "</FORM>";
     PopTable('footer');
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='add_group')
{
    if(!isset($_REQUEST['search']))
    {
        echo "<FORM name=Group id=Compose action=Modules.php?modname=$_REQUEST[modname]&modfunc=group_insert method=POST >";
PopTable('header','Group');
echo '<table>
      <tr>
      <td>Group Name: </td>
      <td>'.TextInput_mail('','txtGrpName','','onkeyup=groups(this.value) class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td>Description: </td>
      <td>'.TextInput_mail('','txtGrpDesc','','onkeyup=desc(this.value) class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td colspan=2>';
     echo  DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Add Group' onclick='return mail_group_chk();'/>")
       . '</td>
        </tr>
        </table>';
     //$_SESSION['groupname']=$_REQUEST['txtGrpName'];
echo "</FORM>";

		if($_SESSION['staff_id'])
		{
			unset($_SESSION['staff_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		}

		echo '<BR>';
		//PopTable('header','Find a User');
               
//		echo "<FORM name=search action=Modules.php?modname=$_REQUEST[modname]&modfunc=add_group&search=true&grpname=$_REQUEST[txtGrpName] method=POST>";
//		echo '<TABLE>';
//		echo '<TR><TD align=right>Last Name</TD><TD><INPUT type=text class=cell_floating name=last></TD></TR>';
//		echo '<TR><TD align=right>First Name</TD><TD><INPUT type=text class=cell_floating name=first></TD></TR>';
//		echo '<TR><TD align=right>Username</TD><TD><INPUT type=text class=cell_floating name=username></TD></TR>';
////                $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles WHERE profile <> \''.'student'.'\''));
//                if(User('PROFILE')=='teacher')
//                {
//                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles where id!=2'));
//                }
//                else if(User('PROFILE')=='parent' || User('PROFILE')=='student')
//                {
//                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles where id not in(0,3)'));
//                }
//                else
//                $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles'));
//                $options[-1]='N/A';
//                //$options['none']='No Access';
//                
//                foreach($profiles as $key=>$value)
//                {
//                    $options[$value['ID']]=$value['TITLE'];
//                }
//                
//		echo '<TR><TD align=right>Profile</TD><TD><SELECT name=profile>';
//		foreach($options as $key=>$val)
//			echo '<OPTION value="'.$key.'">'.$val;
//		echo '</SELECT></TD></TR>';
//		if($extra['search'])
//			echo $extra['search'];
//		echo '<TR><TD colspan=2 align=center>';
//		
//		if(User('PROFILE')=='admin')
//			echo '<INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'>Search All Schools<BR>';
//			echo '<INPUT type=checkbox name=_dis_user value=Y>Include Disabled User<BR><br>';
//		//echo Buttons('Submit','Reset');
//                        echo "<input type=hidden id=groupname name=groupname>";
//                        echo "<input type=hidden id=groupdescription name=groupdescription>";
//		echo "<INPUT type=SUBMIT class=btn_medium value='Submit' >&nbsp<INPUT type=RESET class=btn_medium value='Reset'>";
//		echo '</TD></TR>';
//		echo '</TABLE>';
//		/********************for Back to user***************************/
//                    echo '<input type=hidden name=sql_save_session_staf value=true />';
//                /************************************************/
//                echo '</FORM>';
    }
    if(isset($_REQUEST['search']) && $_REQUEST['search']=='true' && $_REQUEST['modfunc']=='add_group')
    {        
        
    echo "<FORM name=Group id=Compose action=Modules.php?modname=$_REQUEST[modname]&modfunc=group_insert method=POST >";
    PopTable('header','Group');
   // echo "hello ".$_REQUEST['groupname'];
    echo '<table>
      <tr>
      <td>Group Name: </td>
      <td>'.TextInput_mail($_REQUEST['groupname'],'txtGrpName','','class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td>Description: </td>
      <td>'.TextInput_mail($_REQUEST['groupdescription'],'txtGrpDesc','','class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td colspan=2>';
     echo  DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Add Group' onclick='return mail_group_chk();'/>")
       . '</td>
        </tr>
        </table>';
//echo "</FORM>";
     $lastName=$_REQUEST['last'];
     $firstName=$_REQUEST['first'];
     $userName=$_REQUEST['username'];
     $profile=$_REQUEST['profile'];
     $disable=$_REQUEST['_dis_user'];
     $allschools=$_REQUEST['_search_all_schools'];
          if($profile!=-1)//search by profile
          {
              if($profile==3)//students
              {
                if(User('PROFILE')=='teacher')
                   $user="SELECT * FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";
                else   
                   $user="select * from students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' ";                
              }
              if($profile==2)//teachers
              {
                  if(User('PROFILE')=='parent')
                  {
                       $parent_id=UserID();
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND staff_id IN(Select distinct person_id from students_join_people where person_id<>".$parent_id.")";
//                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND staff_id IN(Select distinct student_id from students_join_users where staff_id=".$parent_id.")";
                  }
                  if(User('PROFILE')=='student')
                  {
                       $studentId=UserStudentID();
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".$studentId.")";
                  }
                  if(User('PROFILE')=='admin')
                  {
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''";
                  }
              }
              if($profile==4)//parents
              {
                  if (User('PROFILE')=='teacher')
                  {
                    $teacher_id= UserID();
                    $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                  }
                  if(User('PROFILE')=='admin')
                  {
                      $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' ';        
                  }
              }
              if($profile==0 ||$profile==1 ||$profile==5)//all types of admin
              {
                  $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=$profile and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' ";
              }
              if($lastName!="")
              {
                  $user=$user." AND LAST_NAME LIKE '$lastName%' ";
              }
              if($firstName!="")
              {
                  $user=$user." AND FIRST_NAME LIKE '$firstName%' ";
              }
              if($userName!="")
              {
                  $user=$user." AND USERNAME LIKE '$userName%' ";
              }
//              if($disable=='')//only enabled users
//              {
//                  $user=$user." AND TRIM( IFNULL( is_disable, '' ) ) = ''   ";
//              }
//              if($disable=='Y')//with disabled users
//              {
//                  $user=$user." AND TRIM( IFNULL( is_disable, '' ) ) <> ''   ";
//              }
              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
              {
                  $user=$user." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
              }
              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
              {
                  $user=$user." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
              }
              if($disable=='Y')//with disabled users
              {
                  $user=$user." ";
              }
          }
         else 
          {           
               if(User('PROFILE')=='admin')//all types of admin
               {
                    $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id not in(3,4)";
                    $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,students WHERE login_authentication.user_id=students.student_id AND login_authentication.profile_id=3 AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id=3";
                    $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND login_authentication.profile_id=4";
               }
               if(User('PROFILE')=='teacher')//teachers
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id in(0,1,5)";//all types of admin
                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";//scheduled students
                   $user3='SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 AND TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.UserID().'\')))';//parents                  
               }
               if(User('PROFILE')=='parent')//parents
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id in(0,1,5)";//all types of admin
                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND staff_id NOT IN (Select distinct person_id from students_join_people where person_id<>".UserID().")";//parents                
//                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND staff_id IN(Select distinct student_id from students_join_users where staff_id=".UserID().")";//parents                
               }
               if(User('PROFILE')=='student')//students
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id in(0,1,5)";//all types of admin
                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".UserStudentID().")";//teachers                 
               }
              if($lastName!="")
              {
                  $user1=$user1." AND LAST_NAME LIKE '$lastName%' ";
                  $user2=$user2." AND LAST_NAME LIKE '$lastName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND LAST_NAME LIKE '$lastName%' ";
              }
              if($firstName!="")
              {
                  $user1=$user1." AND FIRST_NAME LIKE '$firstName%' ";
                  $user2=$user2." AND FIRST_NAME LIKE '$firstName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND LAST_NAME LIKE '$firstName%' ";
              }
              if($userName!="")
              {
                  $user1=$user1." AND USERNAME LIKE '$userName%' ";
                  $user2=$user2." AND USERNAME LIKE '$userName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND LAST_NAME LIKE '$firstName%' ";
              }
              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
              {
                  $user1=$user1." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
                  $user2=$user2." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user3=$user3." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
              }
              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
              {
                  $user1=$user1." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
                  $user2=$user2." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user3=$user3." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
              }
              if($disable=='Y')//with disabled users
              {
                  $user1=$user1." ";
                  $user2=$user2." ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user2=$user2." ";
              }
              if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                 $user=$user1." UNION ALL ".$user2." UNION ALL ".$user3;
             else 
                 $user=$user1." UNION ALL ".$user2;
          }
              $userlist=DBGet(DBQueryMd($user)); 
              foreach($userlist as $key=>$value)
              {
                    $select="SELECT * FROM user_profiles WHERE ID='".$userlist[$key]['PROFILE_ID']."'";
                    $profile=DBGet(DBQuery($select));
                    $userlist[$key]['FIRST_NAME']=$userlist[$key]['LAST_NAME'].' '.$userlist[$key]['FIRST_NAME'];
                    $userlist[$key]['PROFILE_ID']=$profile[1]['PROFILE'];
              }
              $columns=array('FIRST_NAME'=>'Member','USERNAME'=>'User Name','PROFILE_ID'=>'Profile');
              $extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
              $extra['LO_group'] = array('STAFF_ID');
              $extra['columns_before']= array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'groups\');"><A>');
              $extra['new'] = true;
              if(is_array($extra['columns_before']))
              {
                    $LO_columns = $extra['columns_before'] + $columns;
                    $columns = $LO_columns;
              }
              foreach($userlist as $id=>$value)
              {
                  $extra['columns_before']['CHECKBOX'] = "<INPUT type=checkbox name=groups[".$value['USER_ID'].",".$value['PROFILE_ID']."] value=Y>";
                  $userlist[$id]=$extra['columns_before']+$value;
              }
              ListOutput( $userlist,$columns,'Member','Members','',array(),array('search'=>false),'');                          
          
               echo '</FORM>';  
                }
          
PopTable('footer');
   
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='member_insert')
 {
       if($_REQUEST['groups'])
     {
        $grp=array_keys($_REQUEST['groups']);
        $select="select * from mail_group where group_name='".str_replace("'","\'",$_REQUEST['txtExistGrpName'])."'";
        $grp_select=DBGet(DBQuery($select));
        $grp_select[1]['GROUP_ID'];
        $grp_select['group_name']=$grp_select[1]['GROUP_NAME'];
        $grp_select['description']=$grp_select[1]['DESCRIPTION'];
        foreach($grp as $i=>$j)
        {
            $idProfile=  explode(",", $j);
            $member_select=  DBGet(DBQuery("Select * from login_authentication,user_profiles where login_authentication.profile_id=user_profiles.id and user_profiles.profile='".$idProfile[1]."' and login_authentication.user_id='$idProfile[0]'  "));
            $grp_members='INSERT INTO mail_groupmembers(GROUP_ID,USER_NAME,profile) VALUES(\''.$grp_select[1]['GROUP_ID'].'\',\''.$member_select[1]['USERNAME'].'\',\''.$member_select[1]['PROFILE_ID'].'\')';
            $members=DBGet(DBQuery($grp_members));
        }
        
    } 
    else
    {
        PopTable('header','Alert Message');
        echo "<CENTER><h4>Please select atleast one member to add</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=Messaging/Group.php\"'></FORM></CENTER>";
        PopTable('footer');
	return false;
    }
        
    unset($_REQUEST['modfunc']);
    echo "<script>window.location='Modules.php?modname=Messaging/Group.php'</script>";
 }
 
if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='group_insert')
 {
     $exist_group=DBGet(DBQuery("SELECT * FROM mail_group WHERE USER_NAME='$userName'"));
    foreach($exist_group as $id=>$value)
    {
        if($exist_group[$id]['GROUP_NAME']==$_REQUEST['txtGrpName'])
        {
            PopTable('header','Alert Message');
            echo "<CENTER><h4>groupname already exist for $userName</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=Messaging/Group.php\"'></FORM></CENTER>";
            PopTable('footer');
            exit;
        }
    }
    $description=$_REQUEST['txtGrpDesc'];
    if($description=="")
        $description='No Description';
        if($_REQUEST['txtGrpName'])
        {
            $group='INSERT INTO mail_group(GROUP_NAME,DESCRIPTION,USER_NAME,CREATION_DATE) VALUES(\''. str_replace("'", "\\'",$_REQUEST['txtGrpName']).'\',\''.str_replace("'", "\\'",$description).'\',\''.$userName.'\',now())';  
            $group_info=DBQuery($group);

            if($_REQUEST['groups'])
            {            
                $grp=array_keys($_REQUEST['groups']);
               // print_r($grp);
                $select="select group_id from mail_group where group_name='".str_replace("'","\'",$_REQUEST['txtGrpName'])."'";
                $grp_select=DBGet(DBQuery($select));
                $grp_select[1]['GROUP_ID'];
                foreach($grp as $i=>$j)
                {
                     $idProfile=  explode(",", $j);
        //            echo "<br>";
        //            echo $idProfile[1];
                    $member_select=  DBGet(DBQuery("Select * from login_authentication,user_profiles where login_authentication.profile_id=user_profiles.id and user_profiles.profile='".$idProfile[1]."' and login_authentication.user_id='$idProfile[0]'  "));
                   // print_r($member_select);
                    $grp_members='INSERT INTO mail_groupmembers(GROUP_ID,USER_NAME,profile) VALUES(\''.$grp_select[1]['GROUP_ID'].'\',\''.$member_select[1]['USERNAME'].'\',\''.$member_select[1]['PROFILE_ID'].'\')';
                    $members=DBGet(DBQuery($grp_members));
                }
            } 

            unset($_REQUEST['modfunc']);
            echo "<script>window.location='Modules.php?modname=Messaging/Group.php'</script>";
        }
        else 
        {
            echo "<script>window.location='Modules.php?modname=Messaging/Group.php&modfunc=add_group'</script>";
        }
    }
    
if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='members' && $_REQUEST['groupid'])
{ 
    if(isset($_REQUEST['groupname']))
    {
    $exist_group=DBGet(DBQuery("SELECT * FROM mail_group WHERE USER_NAME='$userName'"));
    foreach($exist_group as $id=>$value)
    {
        if($exist_group[$id]['GROUP_NAME']==$_REQUEST[groupname])
        {
            PopTable('header','Alert Message');
            echo "<CENTER><h4>groupname already exist for $userName</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=Messaging/Group.php\"'></FORM></CENTER>";
            PopTable('footer');
            exit;
        }
    }
        
       $update="UPDATE mail_group SET GROUP_NAME='".str_replace("'", "\\'",$_REQUEST[groupname])."' WHERE GROUP_ID=$_REQUEST[groupid]";
       
       $update_group=DBGet(DBQuery($update));
    }
     if(isset($_REQUEST['groupdesc']))
        {
         if(trim($_REQUEST['groupdesc'])!="")
            $update="UPDATE mail_group SET DESCRIPTION='".str_replace("'", "\\'",$_REQUEST[groupdesc])."' WHERE GROUP_ID=$_REQUEST[groupid]";
         else
             $update="UPDATE mail_group SET DESCRIPTION='No Description' WHERE GROUP_ID=$_REQUEST[groupid]";
            $update_group=DBGet(DBQuery($update));
        }
        if(isset($_REQUEST['group']))
        {
   if(implode(',',$_REQUEST['group'])=='')
   {
       $select="select * from mail_groupmembers where group_id=$_REQUEST[groupid]";
        $list=DBGet(DBQuery($select));
         foreach($list as $m=>$n)
        {
             if($list[$m]['ID'])
                $del_id[]=$list[$m]['ID'];
        }
   
       $id=implode(',',$del_id);
       $select="DELETE FROM mail_groupmembers WHERE GROUP_ID=$_REQUEST[groupid] AND ID IN($id)";
       $not_in_group=DBGet(DBQuery($select));
        //unset($_REQUEST['modfunc']);
         //unset($_REQUEST['modfunc']);
       echo "<script>window.location='Modules.php?modname=$_REQUEST[modname]'</script>";
   }     
   else
   {
       $not_select="select * from mail_groupmembers where GROUP_ID=$_REQUEST[groupid]";
        $list1=DBGet(DBQuery($not_select));
        foreach($list1 as $i=>$j)
        {
            $id_list[]=$j['ID'];
        }
        $id3=implode(',',$id_list);
       $id1=array_keys($_REQUEST['group']);
       $id2= implode(',',$id1);
       if($id2==$id3)
           echo "<script>window.location='Modules.php?modname=$_REQUEST[modname]'</script>";
        else   
        {
       $select="SELECT * FROM mail_groupmembers WHERE GROUP_ID=$_REQUEST[groupid] AND ID NOT IN($id2)";
            $list=DBGet(DBQuery($select));
       foreach($list as $i=>$j)
       {
           $del_id1[]=$list[$i]['ID'];
       } 
        $id=implode(',',$del_id1);
        $select="DELETE FROM mail_groupmembers WHERE GROUP_ID=$_REQUEST[groupid] AND ID IN($id)";
        $not_in_group=DBGet(DBQuery($select));
        echo "<script>window.location='Modules.php?modname=$_REQUEST[modname]'</script>";
        
        }
   }
 
        }
         echo "<script>window.location='Modules.php?modname=$_REQUEST[modname]'</script>";
}
?>