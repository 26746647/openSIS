<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if($_REQUEST['teacher_view']!='y')
{
 $sql_school_admin='SELECT ssr.SCHOOL_ID FROM schools s,staff st INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE s.id=ssr.school_id AND ssr.syear='.UserSyear().' AND st.staff_id='.User('STAFF_ID');
$school_admin=DBGet(DBQuery($sql_school_admin)); 
foreach($school_admin as $index=>$school)
{
   if($_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']])
    {
        
        $start_date=$_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]."-".$_REQUEST['month_values']['START_DATE'][$school['SCHOOL_ID']]."-".$_REQUEST['year_values']['START_DATE'][$school['SCHOOL_ID']];
    }
    else
    {
        $start_date='';
    }
    if($_REQUEST['day_values']['END_DATE'][$school['SCHOOL_ID']])
    {
        $end_month=array("01"=>"JAN","02"=>"FEB","03"=>"MAR","04"=>"APR","05"=>"MAY","06"=>"JUN","07"=>"JUL","08"=>"AUG","09"=>"SEP","10"=>"OCT","11"=>"NOV","12"=>"DEC");
        foreach($end_month as $ei=>$ed)
        {
            if($ed==$_REQUEST['month_values']['END_DATE'][$school['SCHOOL_ID']])
            $_REQUEST['month_values']['END_DATE'][$school['SCHOOL_ID']]=$ei;
        }
        //$end_date=$_REQUEST['year_values']['END_DATE'][$school['SCHOOL_ID']]."-".$_REQUEST['month_values']['END_DATE'][$school['SCHOOL_ID']]."-".$_REQUEST['day_values']['END_DATE'][$school['SCHOOL_ID']];
        $end_date=$_REQUEST['day_values']['END_DATE'][$school['SCHOOL_ID']]."-".$_REQUEST['month_values']['END_DATE'][$school['SCHOOL_ID']]."-".$_REQUEST['year_values']['END_DATE'][$school['SCHOOL_ID']];
    }
    else
    {
        $end_date='';
    } 
    if(($start_date!='' && VerifyDate($start_date)) || ($end_date!='' && VerifyDate($end_date)) || ($start_date=='' && $end_date==''))
    {
    if(in_array($school['SCHOOL_ID'], $cur_school))
   {
       $schools_each_staff= DBGet(DBQuery('SELECT SCHOOL_ID,START_DATE,END_DATE FROM staff_school_relationship WHERE staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID='.$school['SCHOOL_ID']));
       $schools_each_staff[1]['START_DATE']=date('d-m-Y',strtotime($schools_each_staff[1]['START_DATE']));
       if($schools_each_staff[1]['START_DATE']>$end_date && $end_date!='')
       {
           $error='end_date';
       }
//                               if($start_date=='')
//                               {
////                                  
//                                   $error='start_date';
//                               }
       
       if(!empty($schools_each_staff))
       {
           $update='false';
            unset($sql_up);
           foreach($_REQUEST['values']['SCHOOLS'] as $index=>$value)
            {
               if($index==$school['SCHOOL_ID'] && $value=='Y')
                {
                   $update='go';
                }
               
            }
            if($update=='go')
            {
                if($start_date!='' && $end_date!='')
                {
                   if(strtotime($start_date)<=strtotime($end_date)) 
                       
                    $sql_up='UPDATE staff_school_relationship SET START_DATE=\''.date('Y-m-d',  strtotime($start_date)).'\', END_DATE=\''.date('Y-m-d',  strtotime($end_date)).'\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';
                   else
                      $error='end_date'; 
                }
                elseif($start_date=='' && $end_date!='')
                {
                    if(isset($_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]) && $_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]=='')
                    {
                        $error1='start_date';
                    }
                    else
                    {
                        if(strtotime($schools_each_staff[1]['START_DATE'])<=strtotime($end_date))
                            $sql_up='UPDATE staff_school_relationship SET END_DATE=\''.date('Y-m-d',  strtotime($end_date)).'\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';
                        else
                          $error='end_date';
                    }
                }
                elseif($start_date!='' && $end_date=='')
                {
                    if(strtotime($schools_each_staff[1]['END_DATE'])>=strtotime($start_date) || $schools_each_staff[1]['END_DATE']=='0000-00-00')
                        $sql_up='UPDATE staff_school_relationship SET START_DATE=\''.date('Y-m-d',  strtotime($start_date)).'\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';
                    else
                      $error='end_date';
                }
                elseif (isset($_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]) && isset($_REQUEST['day_values']['END_DATE'][$school['SCHOOL_ID']]) && $_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]=='' && $_REQUEST['day_values']['END_DATE'][$school['SCHOOL_ID']]=='') 
                {
                 
                    $sql_up='UPDATE staff_school_relationship SET START_DATE=\'0000-00-00\', END_DATE=\'0000-00-00\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';
                   
                }
//                elseif (isset($_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]) && $_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]=='') 
//                {
//                    $sql_up='UPDATE staff_school_relationship SET START_DATE=\'0000-00-00\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';     
//                }
                
                elseif (isset($_REQUEST['day_values']['END_DATE'][$school['SCHOOL_ID']]) && $_REQUEST['day_values']['END_DATE'][$school['SCHOOL_ID']]=='') 
                {
                    $sql_up='UPDATE staff_school_relationship SET END_DATE=\'0000-00-00\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';   
                }
                if(!$error  && !$error1 && $sql_up!='')
                {     
                    DBQuery($sql_up);
                }
            }
            
         
//           if($_REQUEST['values']['SCHOOL']['OPENSIS_PROFILE']=='5')
//            {
//            //echo "insert need";
//           $sql_up='INSERT INTO staff_school_relationship(staff_id,syear,school_id';
//                      $sql_up.=')VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$school['SCHOOL_ID'].'\'';
//           
//           if($start_date!='')
//           {
//               $sql_up.=',start_date';
//           }
//           if($end_date!='')
//           {
//               $sql_up.=',end_date';
//           }
//           if($start_date!='')
//           {
//               $sql_up.=',\''.date('Y-m-d',strtotime($start_date)).'\'';
//           }
//           if($end_date!='')
//           {
//               $sql_up.=',\''.date('Y-m-d',strtotime($end_date)).'\'';
//           }
//           $sql_up.=')';
////                                   if($start_date=='')
////                               {
////                                       
////                                   $error='start_date';
////                               }
////                               else
////                               {
//           DBQuery($sql_up);
////                               }
//       # DBQuery('INSERT INTO staff_school_relationship(staff_id,syear,school_id,start_date)VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$school['SCHOOL_ID'].'\',\''.date('Y-m-d').'\')');
//       }
        
        
       }
       else
       {
           
           $sql_up='INSERT INTO staff_school_relationship(staff_id,syear,school_id';
                      $sql_up_data='VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$school['SCHOOL_ID'].'\'';
           
           if($start_date!='')
           {
               $sql_up.=',start_date';
           }
           if($end_date!='')
           {
               if($_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]!='')
               {
                    
               $sql_up.=',end_date';
               }
           }
           if($start_date!='')
           {
               $sql_up_data.=',\''.date('Y-m-d',strtotime($start_date)).'\'';
           }
           if($end_date!='')
           {
               if($_REQUEST['day_values']['START_DATE'][$school['SCHOOL_ID']]!='')
                    $sql_up_data.=',\''.date('Y-m-d',strtotime($end_date)).'\'';
           }
           $sql_up.=')'.$sql_up_data.')';
           
           if($start_date!='' && $end_date!='')
           {
               if(strtotime($start_date)>strtotime($end_date))
                   $error='end_date';
           }
           
//                                   if($start_date=='')
//                               {
//                                       
//                                   $error='start_date';
//                               }
//                               else
//                               {
           if(!$error)
           DBQuery($sql_up);
//                               }
       # DBQuery('INSERT INTO staff_school_relationship(staff_id,syear,school_id,start_date)VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$school['SCHOOL_ID'].'\',\''.date('Y-m-d').'\')');
       }
   }
   else
   {
       $user_profile=DBGet(DBQuery("SELECT PROFILE_ID FROM staff WHERE STAFF_ID='".$_REQUEST['staff_id']."'"));
       if ($user_profile[1]['PROFILE_ID']!='')
        {   $school_selected=implode(',',array_unique(array_keys($_REQUEST['values']['SCHOOLS'])));
           $del_qry="DELETE FROM staff_school_relationship WHERE STAFF_ID='".$_REQUEST['staff_id']."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID NOT IN (".$school_selected.")";
           DBQuery($del_qry);
//            unset($schools_selected);
//           $schools_selected=array_keys($_REQUEST['values']['SCHOOLS']);
//           $schools=DBGet(DBQuery("SELECT DISTINCT SCHOOL_ID FROM staff_school_relationship WHERE STAFF_ID='".$_REQUEST['staff_id']."' AND SYEAR='".UserSyear()."'"));
//           foreach($schools as $index=>$val)
//           {
//              $all_school[]=$val['SCHOOL_ID'];
//              foreach($schools_selected as $value)
//              {
//                  if($val['SCHOOL_ID']!=$value)
//                  {
//                      $final[$val['SCHOOL_ID']]=$val['SCHOOL_ID'];
//                  }
//                  else
//                   {
//                      unset($final[$val['SCHOOL_ID']]);
//                   }
//              }
//           }
//          
          
         
        }
//      $schools_each_staff= DBGet(DBQuery('SELECT SCHOOL_ID,START_DATE,END_DATE FROM staff_school_relationship WHERE staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID='.$school['SCHOOL_ID']));

//       if(!empty($schools_each_staff))
//       {
//           if($schools_each_staff[1]['START_DATE']>$end_date && $end_date!='')
//       {
//           $error='end_date';
//       }
//       
////                               if($start_date=='')
////                               {
////                                     
////                                   $error='start_date';
////                               }
//
////           if($end_date!='')
////            {
////                $sql_up='UPDATE staff_school_relationship SET END_DATE=\''.date('Y-m-d',  strtotime($end_date)).'\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';
////            }
////            elseif($start_date=='' && $end_date!='')
////            {
////                $sql_up='UPDATE staff_school_relationship SET END_DATE=\''.date('Y-m-d',  strtotime($end_date)).'\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\'';
////            }
////            elseif($end_date=='')
////            {
////                if($schools_each_staff[1]['END_DATE']=='0000-00-00')
////                {
////                    $sql_up='UPDATE staff_school_relationship SET END_DATE=\''.date('Y-m-d').'\' where staff_id=\''.$_REQUEST[staff_id].'\' AND syear=\''.UserSyear().'\' AND SCHOOL_ID=\''.$school['SCHOOL_ID'].'\''; 
////                }
////            }
////            if(!$error && $sql_up!='')
////            {
////                DBQuery($sql_up);
////            }
//       } 
   }


}
else
{
    $err= "<center><font color=red><b>The invalid date could not be saved.</b><font></center>";
}


}
if($error=='end_date')
{
    echo '<script type=text/javascript>document.getElementById(\'sh_err\').innerHTML=\'<b><font color=red>Start date can not be greater than end date</font></b>\';</script>';
//    echo "<font color=red><b>Start date can not be greater than End date</b></font><br/>";
    unset($error);
//    if($_REQUEST['staff_id']=='new')
//    {
//        header("location:modules/Users/Staff.php&staff_id=new");
//    }
}
    if($error1=='start_date')
    {
        echo '<script type=text/javascript>document.getElementById(\'sh_err\').innerHTML=\'<font color=red><b>Start date can not be blank</b></font>\';</script>';
        unset($error1);

    }
}

if($_REQUEST['month_values']['JOINING_DATE'] && $_REQUEST['day_values']['JOINING_DATE'] && $_REQUEST['year_values']['JOINING_DATE']){
$_REQUEST['values']['SCHOOL']['JOINING_DATE']=$_REQUEST['year_values']['JOINING_DATE'].'-'.$_REQUEST['month_values']['JOINING_DATE'].'-'.$_REQUEST['day_values']['JOINING_DATE'];
$_REQUEST['values']['SCHOOL']['JOINING_DATE']=date("Y-m-d",strtotime($_REQUEST['values']['SCHOOL']['JOINING_DATE']));
                           }
elseif(isset($_REQUEST['month_values']['JOINING_DATE']) && isset($_REQUEST['day_values']['JOINING_DATE']) && isset($_REQUEST['year_values']['JOINING_DATE']))
			$_REQUEST['values']['SCHOOL']['JOINING_DATE'] = '';


if($_REQUEST['month_values']['ENDING_DATE'] && $_REQUEST['day_values']['ENDING_DATE'] && $_REQUEST['year_values']['ENDING_DATE']){
$_REQUEST['values']['SCHOOL']['ENDING_DATE']=$_REQUEST['year_values']['ENDING_DATE'].'-'.$_REQUEST['month_values']['ENDING_DATE'].'-'.$_REQUEST['day_values']['ENDING_DATE'];
$_REQUEST['values']['SCHOOL']['ENDING_DATE']=date("Y-m-d",strtotime($_REQUEST['values']['SCHOOL']['ENDING_DATE']));
                          }
elseif(isset($_REQUEST['month_values']['ENDING_DATE']) && isset($_REQUEST['day_values']['ENDING_DATE']) && isset($_REQUEST['year_values']['ENDING_DATE']))
			$_REQUEST['values']['SCHOOL']['ENDING_DATE'] = '';

$end_date=$_REQUEST['values']['SCHOOL']['ENDING_DATE'];
unset($_REQUEST['values']['SCHOOL']['ENDING_DATE']);
$_REQUEST['values']['SCHOOL']['END_DATE']=$end_date;

if($_REQUEST['values']['SCHOOL_IDS'])
        {
                   $_REQUEST['values']['SCHOOL']['SCHOOL_ACCESS']=',';
                    foreach($_REQUEST['values']['SCHOOL_IDS'] as $key=>$val)
                        {
                                   $_REQUEST['values']['SCHOOL']['SCHOOL_ACCESS'].=$key.",";
                        }
       }

$select_RET=DBGet(DBQuery("SELECT STAFF_ID FROM staff_school_info where STAFF_ID='".UserStaffID()."'"));
$select=$select_RET[1]['STAFF_ID'];
//print_r($_REQUEST['values']['SCHOOLS']);
//if(count($_REQUEST['values']['SCHOOLS'])>0)
//{
//foreach($_REQUEST['values']['SCHOOLS'] as $index=>$values)
//{
//    
//}
//}

$password=md5($_REQUEST['staff_school']['PASSWORD']);
$sql=DBQuery('SELECT PASSWORD FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
$number=mysql_num_rows($sql);
if($number!=0)
{
    echo '<font color = red><b>Invalid password</b></font>';
}

if($_REQUEST['values']['SCHOOL']['OPENSIS_PROFILE']=='11')
{
    $district1=DBGet(DBQuery("SELECT DISTRICT FROM schools WHERE id='".UserSchool()."'"));
    $school_id1=DBGet(DBQuery("SELECT ID FROM schools WHERE DISTRICT='".$district1[1]['DISTRICT']."'"));
    
    foreach($school_id1 as $index=>$val)
    {
        $schools[]=$val['ID'];
    }
    
    $schools=implode(",",$schools);
    $_REQUEST['values']['SCHOOL']['SCHOOL_ACCESS']=",".$schools.",";
}

elseif($_REQUEST['values']['SCHOOL']['OPENSIS_PROFILE']=='1')
{
    $school_id1=DBGet(DBQuery("SELECT ID FROM schools"));
    
    foreach($school_id1 as $index=>$val)
    {
        $schools[]=$val['ID'];
    }
    
    $schools=implode(",",$schools);
    $_REQUEST['values']['SCHOOL']['SCHOOL_ACCESS']=",".$schools.",";
}

//elseif($_REQUEST['values']['SCHOOL']['OPENSIS_PROFILE']=='24')
//{
////foreach($_REQUEST['values']['SCHOOLS'] as $school=>$val)
////{
////    if($val=='Y')
////    {
////     $schools[]=$school;   
////    }
////}
//$schools=implode(",",$schools);
//$_REQUEST['values']['SCHOOL']['SCHOOL_ACCESS']=",".$schools.",";
//}
else
{
foreach($_REQUEST['values']['SCHOOLS'] as $school=>$val)
{
    if($val=='Y')
    {
     $schools[]=$school;   
    }
}
$schools=implode(",",$schools);
$_REQUEST['values']['SCHOOL']['SCHOOL_ACCESS']=",".$schools.",";    
}

if($select == '')
{
    if($_REQUEST['values']['SCHOOL']['OPENSIS_ACCESS']=='Y')
   {
             $sql = "INSERT INTO staff_school_info ";
			$fields = 'STAFF_ID,';
			$values = "'".UserStaffID()."',";
           foreach($_REQUEST['values']['SCHOOL'] as $column=>$value)
			{
//                        echo $column.'--->'.$value;echo '<br>';
                             if($column=='SCHOOL_ACCESS' && $value==',,')
                                 $value=','.UserSchool().',';
				if($value)
				{

                                     $fields .= $column.',';
                                      if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                                 $values .= "'".str_replace("'","\'",$value)."',";
                                        }else
                                               $values .= "'".str_replace("'","''",$value)."',";

                                       }
                             if($column=='OPENSIS_PROFILE' && $value==0)
                             {
                                 $fields .= $column.',';
                                      if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                                 $values .= "'".str_replace("'","\'",$value)."',";
                                        }else
                                               $values .= "'".str_replace("'","''",$value)."',";
                             }
                                 
                        }
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
                        
                            DBQuery($sql);
                            $update_staff_RET=DBGet(DBQuery("SELECT  * FROM staff_school_info where STAFF_ID='".UserStaffID()."'"));
                           $update_staff=$update_staff_RET[1];
                           $profile_name_RET=DBGet(DBQuery("SELECT PROFILE from user_profiles WHERE id=".$update_staff['OPENSIS_PROFILE']));
                           $profile=$profile_name_RET[1]['PROFILE'];
                           $staff_CHECK=DBGet(DBQuery("SELECT  s.*,la.*  FROM staff s,login_authentication la where s.STAFF_ID='".UserStaffID()."' AND la.PROFILE_ID NOT IN (3,4) AND la.USER_ID=s.STAFF_ID"));
                           $staff=$staff_CHECK[1];
                           $sql_staff="UPDATE staff SET ";
//                           $sql_staff.="SCHOOLS='".$update_staff['SCHOOL_ACCESS']."',
//                                            PROFILE_ID='".$update_staff['OPENSIS_PROFILE']."',
//                                            PROFILE='".$profile."',";
                           if($_REQUEST['staff_school']['CURRENT_SCHOOL_ID'])
                            $sql_staff.="PROFILE_ID='".$update_staff['OPENSIS_PROFILE']."',PROFILE='".$profile."',CURRENT_SCHOOL_ID='".$_REQUEST['staff_school']['CURRENT_SCHOOL_ID']."',";
                           else
                              $sql_staff.="PROFILE_ID='".$update_staff['OPENSIS_PROFILE']."',PROFILE='".$profile."',"; 
                           foreach($_REQUEST['staff_school'] as $field=>$value)
                           {
                                         if($field=='IS_DISABLE')
                                           {
                                                      if($value)
                                                       {
                                                          $sql_staff .= $field."='".str_replace("\'","''",$value)."',";
                                                       }
//                                                       else
//                                                       {
//                                                           $sql_staff .= $field."=NULL,";
//                                                       }
                                            }
                                            elseif($field=='PASSWORD')
                                            {
                                                $password=md5($value);
                                                $sql=DBQuery('SELECT PASSWORD FROM login_authentication  WHERE PASSWORD=\''.$password.'\'');
                                                $number=mysql_num_rows($sql);
                                                if($number==0)
                                                {
                                                     if((!$staff['USERNAME']) && (!$staff['PASSWORD']))
                                                    {
                                                    $sql_staff_pwd= $field."=NULL";
                                                    }
                                                    else
                                                    {
                                                    $sql_staff_pwd= $field."='".str_replace("\'","''",md5($value))."'";
                                                    }
                                                
                                                }
                                            }
                           }
                        $sql_staff = substr($sql_staff,0,-1) . " WHERE STAFF_ID='".UserStaffID()."'";
                        if($sql_staff_pwd!='')
                        {
                        $sql_staff_pwd='Update login_authentication SET '.$sql_staff_pwd.' WHERE USER_ID='.UserStaffID();
                        if(SelectedUserProfile('PROFILE_ID')!='')
                        $sql_staff_pwd.=' AND PROFILE_ID='.SelectedUserProfile('PROFILE_ID');
                        }
                        
                        if($update_staff['OPENSIS_PROFILE']!='')
                        {
                        $check_rec=DBGet(DBQuery('SELECT COUNT(1) AS REC_EXISTS FROM login_authentication WHERE USER_ID='.UserStaffID().' AND PROFILE_ID NOT IN (3,4) '));
                        if($check_rec[1]['REC_EXISTS']==0)
                        $sql_staff_prf='INSERT INTO login_authentication (PROFILE_ID,USER_ID) VALUES (\''.$update_staff['OPENSIS_PROFILE'].'\',\''.  UserStaffID().'\') ';
                        else
                        $sql_staff_prf='Update login_authentication SET  PROFILE_ID=\''.$update_staff['OPENSIS_PROFILE'].'\' WHERE PROFILE_ID NOT IN (3,4) AND USER_ID='.UserStaffID();
                        }
                        
                        DBQuery($sql_staff);
                        if($sql_staff_pwd!='')
                        DBQuery($sql_staff_pwd);
                        if($update_staff['OPENSIS_PROFILE']!='')
                        DBQuery($sql_staff_prf);      
                          if((!$staff['USERNAME']) && (!$staff['PASSWORD']))
                          {
                              $check_bday=DBGet(DBQuery('SELECT BIRTHDATE FROM staff WHERE STAFF_ID='.UserStaffID()));
                              if($check_bday[1]['BIRHTDATE']!='')
                              {   
                               $sql_staff_algo="UPDATE login_authentication l,staff s, staff_school_info ssi SET
                                l.username = CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(YEAR(s.birthdate),2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'),
                               l.password = md5(CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(YEAR(s.birthdate),2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'))
                                WHERE s.staff_id = ssi.staff_id AND l.user_id=s.staff_id AND l.profile_id NOT IN (3,4) AND s.staff_id = ".UserStaffID();
                              }
                              else
                              {
                               $random_num=rand(999,99999);   
                               $sql_staff_algo="UPDATE login_authentication l,staff s, staff_school_info ssi SET
                                l.username = CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(".$random_num.",2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'),
                               l.password = md5(CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(".$random_num.",2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'))
                                WHERE s.staff_id = ssi.staff_id AND l.user_id=s.staff_id AND l.profile_id NOT IN (3,4) AND s.staff_id = ".UserStaffID();
                              }
                            DBQuery($sql_staff_algo);
                          }
             if($update_staff['OPENSIS_PROFILE']=='1')
           {
             
            $school_id3=DBGet(DBQuery("SELECT ID FROM schools WHERE ID NOT IN (SELECT school_id FROM staff_school_relationship WHERE
                                      STAFF_ID='".$_REQUEST['staff_id']."' AND SYEAR='".UserSyear()."')"));
            foreach($school_id3 as $index=>$val)
            {
                
                $sql_up='INSERT INTO staff_school_relationship(staff_id,syear,school_id';
                       $sql_up.=')VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$val['ID'].'\'';

//                if($start_date!='')
//                {
//                $sql_up.=',start_date';
//                }
//                if($end_date!='')
//                {
//                $sql_up.=',end_date';
//                }
//                if($start_date!='')
//                {
//                $sql_up.=',\''.date('Y-m-d',strtotime($start_date)).'\'';
//                }
//                if($end_date!='')
//                {
//                $sql_up.=',\''.date('Y-m-d',strtotime($end_date)).'\'';
//                }
               $sql_up.=')';
                DBQuery($sql_up);
            
            }
           }
           
//           if($update_staff['OPENSIS_PROFILE']=='11')
//           {
//            $district2=DBGet(DBQuery("SELECT DISTRICT FROM schools WHERE id='".UserSchool()."'"));
//            $school_id2=DBGet(DBQuery("SELECT ID FROM schools WHERE DISTRICT='".$district2[1]['DISTRICT']."' AND ID NOT IN (SELECT school_id FROM staff_school_relationship WHERE
//                                      STAFF_ID='".$_REQUEST['staff_id']."' AND SYEAR='".UserSyear()."')"));

//            foreach($school_id2 as $index=>$val)
//            {
//            $sql_up='INSERT INTO staff_school_relationship(staff_id,syear,school_id';
//                   $sql_up.=')VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$val['ID'].'\'';
//
////            if($start_date!='')
////            {
////            $sql_up.=',start_date';
////            }
////            if($end_date!='')
////            {
////            $sql_up.=',end_date';
////            }
////            if($start_date!='')
////            {
////            $sql_up.=',\''.date('Y-m-d',strtotime($start_date)).'\'';
////            }
////            if($end_date!='')
////            {
////            $sql_up.=',\''.date('Y-m-d',strtotime($end_date)).'\'';
////            }
//            $sql_up.=')';
//            DBQuery($sql_up);
//            } 
//           }
                          
   }
   elseif($_REQUEST['values']['SCHOOL']['OPENSIS_ACCESS']=='N')
   {
              $sql = "INSERT INTO staff_school_info ";
			$fields = 'STAFF_ID,';
			$values = "'".UserStaffID()."',";
            foreach($_REQUEST['values']['SCHOOL'] as $column=>$value)
			{
                            
                            if($column=='OPENSIS_PROFILE')
                            {
                                 $fields .= $column.',';
                                 $values .= "NULL,";
                            }

			else{
                            if($value)
				{
                                    $fields .= $column.',';
                                    if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                                      {
                                        $values .= "'".str_replace("'","\'",$value)."',";
                                    }
                                    else
                                    $values .= "'".str_replace("'","''",$value)."',";
			         }
			}
                        }
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

			DBQuery($sql);
                         $update_staff_RET=DBGet(DBQuery("SELECT  * FROM staff_school_info where STAFF_ID='".UserStaffID()."'"));
                           $update_staff=$update_staff_RET[1];
//                           $profile_name_RET=DBGet(DBQuery("SELECT PROFILE from USER_PROFILES WHERE id=".$update_staff['OPENSIS_PROFILE']));
//                           $profile=$profile_name_RET[1]['PROFILE'];
                           $staff_CHECK=DBGet(DBQuery("SELECT  *  FROM staff where STAFF_ID='".UserStaffID()."'"));
                           $staff=$staff_CHECK[1];

                           $sql_staff="UPDATE staff SET ";
                           $sql_staff.="PROFILE_ID='".$update_staff['OPENSIS_PROFILE']."',";
                                           
                             $sql_staff = substr($sql_staff,0,-1) . " WHERE STAFF_ID='".UserStaffID()."'";
                             DBQuery($sql_staff);
   }
}
else
{
   if($_REQUEST['values']['SCHOOL']['OPENSIS_ACCESS']=='Y')
   {
                               $sql = "UPDATE staff_school_info  SET ";

                                        foreach($_REQUEST['values']['SCHOOL'] as $column=>$value)
                                        {
//                                                 if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
//                                                        $sql .= "$column='".str_replace("'","\'",str_replace("`","''",$value))."',";
//                                                        }
                                            if(strtoupper($column)=='OPENSIS_PROFILE' || strtoupper($column)=='CATEGORY')
                                            {
                                                $check_prof=  DBGet(DBQuery('SELECT * FROM staff_school_info WHERE STAFF_ID='.UserStaffID()));
                                                if(strtoupper($column)=='OPENSIS_PROFILE' && $value!=$check_prof[1]['OPENSIS_PROFILE'])
                                                {
                                                    if($value!='')
                                                    {
                                                        $check_staff_cp=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_ASSIGNED FROM course_periods WHERE TEACHER_ID='.UserStaffID().' OR SECONDARY_TEACHER_ID='.UserStaffID().''));
                                                    }
                                                    if($check_staff_cp[1]['TOTAL_ASSIGNED']==0 && $value!='')
                                                    {
//                                                        $go=true;
//                                                        $profile_TYPE=DBGet(DBQuery('SELECT PROFILE FROM user_profiles WHERE ID=\''.$value.'\''));
//                                                        $p_ID=$value;
//                                                        $value=$profile_TYPE[1]['PROFILE'];

                                                        $sql .= $column.'=\''.str_replace("\'","''",str_replace("`","''",trim($value))).'\',';
    //                                                    $sql .= 'PROFILE_ID=\''.str_replace("\'","''",str_replace("`","''",$p_ID)).'\',';
                                                    }
                                                    if($check_staff_cp[1]['TOTAL_ASSIGNED']>0 && $value!='')
                                                    {
                                                        if(strtoupper($column)=='OPENSIS_PROFILE')
                                                            echo '<script type=text/javascript>document.getElementById(\'prof_err\').innerHTML=\'<font color=red><b>Cannot change the profile as this staff has one or more course periods.</b></font>\';</script>';
//                                                        if(strtoupper($column)=='CATEGORY')
//                                                            echo '<script type=text/javascript>document.getElementById(\'cat_err\').innerHTML=\'<font color=red><b>Cannot change the category as this staff has one or more course periods.</b></font>\';</script>';
    //                                                     echo '<font color = red><b> Cannot change the profile as this staff has one or more course periods. </b></font><br/>';
                                                    }
                                                }
                                                if(strtoupper($column)=='CATEGORY' && $value!=$check_prof[1]['CATEGORY'])
                                                {
                                                    if($value!='')
                                                    {
                                                        $check_staff_cp=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_ASSIGNED FROM course_periods WHERE TEACHER_ID='.UserStaffID().' OR SECONDARY_TEACHER_ID='.UserStaffID().''));
                                                    }
                                                    if($check_staff_cp[1]['TOTAL_ASSIGNED']==0 && $value!='')
                                                    {
                                                        $go=true;
//                                                        $profile_TYPE=DBGet(DBQuery('SELECT TITLE AS PROFILE FROM user_profiles WHERE ID=\''.$value.'\''));
//                                                        $p_ID=$value;
//                                                        $value=$profile_TYPE[1]['PROFILE'];

                                                        $sql .= $column.'=\''.str_replace("\'","''",str_replace("`","''",trim($value))).'\',';
    //                                                    $sql .= 'PROFILE_ID=\''.str_replace("\'","''",str_replace("`","''",$p_ID)).'\',';
                                                    }
                                                    if($check_staff_cp[1]['TOTAL_ASSIGNED']>0 && $value!='')
                                                    {
//                                                        if(strtoupper($column)=='OPENSIS_PROFILE')
//                                                            echo '<script type=text/javascript>document.getElementById(\'prof_err\').innerHTML=\'<font color=red><b>Cannot change the profile as this staff has one or more course periods.</b></font>\';</script>';
                                                        if(strtoupper($column)=='CATEGORY')
                                                            echo '<script type=text/javascript>document.getElementById(\'cat_err\').innerHTML=\'<font color=red><b>Cannot change the category as this staff has one or more course periods.</b></font>\';</script>';
    //                                                     echo '<font color = red><b> Cannot change the profile as this staff has one or more course periods. </b></font><br/>';
                                                    }
                                                }
                                            }
                                               else
                                               $sql .= "$column='".str_replace("'","''",str_replace("'`","''",$value))."',";


                                        }
                          $sql = substr($sql,0,-1) . " WHERE STAFF_ID='".UserStaffID()."'";
                          DBQuery($sql);
                           $update_staff_RET=DBGet(DBQuery("SELECT  * FROM staff_school_info where STAFF_ID='".UserStaffID()."'"));
                           $update_staff=$update_staff_RET[1];
                           $profile_name_RET=DBGet(DBQuery("SELECT PROFILE from user_profiles WHERE id=".$update_staff['OPENSIS_PROFILE']));
                           $profile=$profile_name_RET[1]['PROFILE'];
                           $staff_CHECK=DBGet(DBQuery("SELECT  s.*,l.*  FROM staff s,login_authentication l where s.STAFF_ID='".UserStaffID()."' AND l.USER_ID=s.STAFF_ID AND l.PROFILE_ID NOT IN (3,4) "));
                           $staff=$staff_CHECK[1];
                          
                           $sql_staff="UPDATE staff SET ";
//                           $sql_staff.="SCHOOLS='".$update_staff['SCHOOL_ACCESS']."',
//                                            PROFILE_ID='".$update_staff['OPENSIS_PROFILE']."',
//                                       PROFILE='".$profile."',";
                           
                           $sql_staff.=" PROFILE_ID='".$update_staff['OPENSIS_PROFILE']."',
                                       PROFILE='".$profile."',CURRENT_SCHOOL_ID='".$_REQUEST['staff_school']['CURRENT_SCHOOL_ID']."',";
                           
                           foreach($_REQUEST['staff_school'] as $field=>$value)
                           {
                                              if($field=='IS_DISABLE')
                                           {
                                                      if($value)
                                                       {
                                                          $sql_staff .= $field."='".str_replace("\'","''",$value)."',";
                                                       }
//                                                       else
//                                                       {
//                                                           $sql_staff .= $field."='Y',";
//                                                       }
                                            }
                                            elseif($field=='PASSWORD')
                                            {   
                                                $password=md5($value);
                                                $sql=DBQuery('SELECT PASSWORD FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
                                                $number=mysql_num_rows($sql);
                                                if($number==0)
                                                {
                                                    if((!$staff['USERNAME']) && (!$staff['PASSWORD']))
                                                    {
                                                    $sql_staff_pwd= $field."=NULL";
                                                    }
                                                    else
                                                    {
                                                    $sql_staff_pwd= $field."='".str_replace("\'","''",md5($value))."'";
                                                    }
                                                    
                                                }
                                            }
                           }
                        $sql_staff = substr($sql_staff,0,-1) . " WHERE STAFF_ID='".UserStaffID()."'";
                        if($sql_staff_pwd!='')
                        $sql_staff_pwd='Update login_authentication SET '.$sql_staff_pwd.' WHERE USER_ID='.UserStaffID().' AND PROFILE_ID='.SelectedUserProfile('PROFILE_ID');
                       
                        if($update_staff['OPENSIS_PROFILE']!='')
                        {
                        $check_rec=DBGet(DBQuery('SELECT COUNT(1) AS REC_EXISTS FROM login_authentication WHERE USER_ID='.UserStaffID().' AND PROFILE_ID NOT IN (3,4) '));
                        if($check_rec[1]['REC_EXISTS']==0)
                        $sql_staff_prf='INSERT INTO login_authentication (PROFILE_ID,USER_ID) VALUES (\''.$update_staff['OPENSIS_PROFILE'].'\',\''.  UserStaffID().'\') ';
                        else
                        $sql_staff_prf='Update login_authentication SET  PROFILE_ID=\''.$update_staff['OPENSIS_PROFILE'].'\' WHERE PROFILE_ID NOT IN (3,4) AND USER_ID='.UserStaffID();
                        }
                       
                        DBQuery($sql_staff);
                        if($sql_staff_pwd!='')
                        DBQuery($sql_staff_pwd);
                        
                        if($update_staff['OPENSIS_PROFILE']!='')
                        DBQuery($sql_staff_prf);
                        
                          if((!$staff['USERNAME']) && (!$staff['PASSWORD']))
                          {
                          $check_bday=DBGet(DBQuery('SELECT BIRTHDATE FROM staff WHERE STAFF_ID='.UserStaffID()));
                            if($check_bday[1]['BIRHTDATE']!='')
                            {   
                              $sql_staff_algo="UPDATE login_authentication l,staff s, staff_school_info ssi SET
                                l.username = CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(YEAR(s.birthdate),2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'),
                               l.password = md5(CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(YEAR(s.birthdate),2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'))
                                WHERE s.staff_id = ssi.staff_id AND s.staff_id=l.user_id AND l.PROFILE_ID NOT IN (3,4) AND s.staff_id = ".UserStaffID();
                            }
                            else
                            {
                            $random_num=rand(999,99999);   
                            $sql_staff_algo="UPDATE login_authentication l,staff s, staff_school_info ssi SET
                            l.username = CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(".$random_num.",2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'),
                            l.password = md5(CONCAT(UPPER(LEFT(s.last_name,3)),UPPER(LEFT(s.first_name,1)),RIGHT(".$random_num.",2),RIGHT(YEAR(ssi.joining_date),2),'".str_pad(UserStaffID(),5,"0", STR_PAD_LEFT)."'))
                            WHERE s.staff_id = ssi.staff_id AND s.staff_id=l.user_id AND l.PROFILE_ID NOT IN (3,4) AND s.staff_id = ".UserStaffID();
                            }    
                              
                            DBQuery($sql_staff_algo);
                          }
            if($update_staff['OPENSIS_PROFILE']=='1')
           {
             
            $school_id3=DBGet(DBQuery("SELECT ID FROM schools WHERE ID NOT IN (SELECT school_id FROM staff_school_relationship WHERE
                                      STAFF_ID='".$_REQUEST['staff_id']."' AND SYEAR='".UserSyear()."')"));
            foreach($school_id3 as $index=>$val)
            {
                
                $sql_up='INSERT INTO staff_school_relationship(staff_id,syear,school_id';
                       $sql_up.=')VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$val['ID'].'\'';

//                if($start_date!='')
//                {
//                $sql_up.=',start_date';
//                }
//                if($end_date!='')
//                {
//                $sql_up.=',end_date';
//                }
//                if($start_date!='')
//                {
//                $sql_up.=',\''.date('Y-m-d',strtotime($start_date)).'\'';
//                }
//                if($end_date!='')
//                {
//                $sql_up.=',\''.date('Y-m-d',strtotime($end_date)).'\'';
//                }
               $sql_up.=')';
                DBQuery($sql_up);
            
            }
           }
           
//           if($update_staff['OPENSIS_PROFILE']=='11')
//           {
//            $district2=DBGet(DBQuery("SELECT DISTRICT FROM schools WHERE id='".UserSchool()."'"));
//            $school_id2=DBGet(DBQuery("SELECT ID FROM schools WHERE DISTRICT='".$district2[1]['DISTRICT']."' AND ID NOT IN (SELECT school_id FROM staff_school_relationship WHERE
//                                      STAFF_ID='".$_REQUEST['staff_id']."' AND SYEAR='".UserSyear()."')"));
//
//            foreach($school_id2 as $index=>$val)
//            {
//            $sql_up='INSERT INTO staff_school_relationship(staff_id,syear,school_id';
//                   $sql_up.=')VALUES(\''.$_REQUEST[staff_id].'\',\''.UserSyear().'\',\''.$val['ID'].'\'';
//
////            if($start_date!='')
////            {
////            $sql_up.=',start_date';
////            }
////            if($end_date!='')
////            {
////            $sql_up.=',end_date';
////            }
////            if($start_date!='')
////            {
////            $sql_up.=',\''.date('Y-m-d',strtotime($start_date)).'\'';
////            }
////            if($end_date!='')
////            {
////            $sql_up.=',\''.date('Y-m-d',strtotime($end_date)).'\'';
////            }
//            $sql_up.=')';
//            DBQuery($sql_up);
//            } 
//           }

   }
   elseif($_REQUEST['values']['SCHOOL']['OPENSIS_ACCESS']=='N')
   {
       
       unset($_REQUEST['values']['SCHOOL']['SCHOOL_ACCESS']);
       unset($_REQUEST['values']['SCHOOL']['OPENSIS_PROFILE']);
       
                      $sql = "UPDATE staff_school_info  SET ";

                                        foreach($_REQUEST['values']['SCHOOL'] as $column=>$value)
                                        {
                                                 if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                                        $sql .= "$column='".str_replace("'","\'",str_replace("`","''",$value))."',";
                                                        }else
                                               $sql .= "$column='".str_replace("'","''",str_replace("'`","''",$value))."',";


                                        }
                           $sql = substr($sql,0,-1) . " WHERE STAFF_ID='".UserStaffID()."'";
                           DBQuery($sql);
//                           $update_staff_RET=DBGet(DBQuery("SELECT  * FROM staff_school_info where STAFF_ID='".UserStaffID()."'"));
//                           $update_staff=$update_staff_RET[1];
//                           $profile_name_RET=DBGet(DBQuery("SELECT PROFILE from user_profiles WHERE id=".$update_staff['OPENSIS_PROFILE']));
//                           $profile=$profile_name_RET[1]['PROFILE'];
//                           $staff_CHECK=DBGet(DBQuery("SELECT  *  FROM staff where STAFF_ID='".UserStaffID()."'"));
//                           $staff=$staff_CHECK[1];
//                           $sql_staff="UPDATE staff SET ";
//                           $sql_staff.="
//                                            PROFILE_ID='".$update_staff['OPENSIS_PROFILE']."',
//                                       PROFILE='".$profile."',
//                                           USERNAME=NULL,
//                                           PASSWORD=NULL,
//                                           IS_DISABLE='N',";
//                          $sql_staff = substr($sql_staff,0,-1) . " WHERE STAFF_ID='".UserStaffID()."'";
////                         DBQuery($sql_staff);
                        
                        
   }
}

if(!$_REQUEST['modfunc'])
{
	$this_school_RET = DBGet(DBQuery("SELECT * FROM staff_school_info   WHERE   STAFF_ID=".UserStaffID()));
        $this_school=$this_school_RET[1];
        
        $this_school_RET_mod = DBGet(DBQuery("SELECT s.*,l.* FROM staff s,login_authentication l  WHERE l.USER_ID=s.STAFF_ID AND l.PROFILE_ID NOT IN (3,4) AND s.STAFF_ID=".UserStaffID()));

        $this_school_mod=$this_school_RET_mod[1];

        
        if(User('PROFILE_ID')==1)
        $profiles_options =DBGet(DBQuery("SELECT PROFILE ,TITLE, ID FROM user_profiles WHERE ID <> 3 AND PROFILE <> 'parent' AND ID<>0 ORDER BY ID"));
        
        $prof_check=DBGet(DBQuery('SELECT PROFILE_ID FROM staff WHERE STAFF_ID='.UserStaffID()));
        if(User('PROFILE_ID')==0 && $prof_check[1]['PROFILE_ID']==0)
        $profiles_options =DBGet(DBQuery("SELECT PROFILE ,TITLE, ID FROM user_profiles WHERE ID <> 3  AND PROFILE <> 'parent' ORDER BY ID"));
        if(User('PROFILE_ID')==0 && $prof_check[1]['PROFILE_ID']!=0)
        $profiles_options =DBGet(DBQuery("SELECT PROFILE ,TITLE, ID FROM user_profiles WHERE ID <> 0  AND PROFILE <> 'parent' AND ID<>'4' ORDER BY ID"));
//        if(User('PROFILE_ID')==24)
//        $profiles_options =DBGet(DBQuery("SELECT PROFILE ,TITLE, ID FROM user_profiles WHERE ID <> 0 AND ID<>1 AND ID<>11 AND PROFILE <> 'parent' AND ID<>4 ORDER BY ID"));
//        
        if(User('PROFILE_ID')==2)
            $profiles_options =DBGet(DBQuery("SELECT PROFILE ,TITLE, ID FROM user_profiles WHERE  PROFILE ='teacher' ORDER BY ID"));
		$i = 1;
		foreach($profiles_options as $options)
		{

			$option[$options['ID']] = $options['TITLE'];
			$i++;
		}

                $_REQUEST['category_id'] = 3;
                $_REQUEST['custom']='staff';
                include('modules/Users/includes/Other_Info.inc.php');
	echo '<TABLE border=0><TR><TD valign=top>';

	echo '<TABLE border=0><TR><TD valign=top>';

	echo '<TABLE border=0 cellpadding=0 cellspacing=0>';

       $style = '';



	echo '</TABLE>';
	echo '</TD>';
	echo '<TD class=vbreak>&nbsp;</TD><TD valign=top>';

	if(isset($_REQUEST['school_info_id']))
	{
		echo "<INPUT type=hidden name=school_info_id value=$_REQUEST[school_info_id]>";

		if($_REQUEST['school_info_id']!='0' && $_REQUEST['school_info_id']!=='old')
		{

			echo '<TABLE width=100%><TR><TD>'; // open 3a
			echo '<FIELDSET><LEGEND><FONT color=gray>Official Information</FONT></LEGEND><TABLE width=100%>';
                        if(User('PROFILE_ID')==0 && $prof_check[1]['PROFILE_ID']==0 && User('STAFF_ID')==UserStaffID())
                        echo '<TR><td><span class=red>*</span>Category</td><td>:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'.SelectInput($this_school['CATEGORY'],'values[SCHOOL][CATEGORY]','',array('Super Administrator'=>'Super Administrator','Administrator'=>'Administrator','Teacher'=>'Teacher','Non Teaching Staff'=>'Non Teaching Staff','Custodian'=>'Custodian','Principal'=>'Principal','Clerk'=>'Clerk'),false).'</td><td>';
                        else
                        echo '<TR><td><span class=red>*</span>Category</td><td>:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'.SelectInput($this_school['CATEGORY'],'values[SCHOOL][CATEGORY]','',array('Administrator'=>'Administrator','Teacher'=>'Teacher','Non Teaching Staff'=>'Non Teaching Staff','Custodian'=>'Custodian','Principal'=>'Principal','Clerk'=>'Clerk'),false).'</td><td>';
			echo '</td></tr></table></TD></tr>';
			echo '<TR><td>Job Title</td><td>:</td><TD>'.TextInput($this_school['JOB_TITLE'],'values[SCHOOL][JOB_TITLE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>Joining Date</td><td>:</td><TD>'.DateInput($this_school['JOINING_DATE'],'values[JOINING_DATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>End Date</td><td>:</td><TD>'.DateInput($this_school['END_DATE'],'values[ENDING_DATE]','','class=cell_medium').'</TD></tr>';
                       # echo '<TR><td>Termination Reason</td><td>:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'._makETerminationInput($this_school['TERMINATION_REASON'],'','values[SCHOOL][TERMINATION_REASON]').'</td><td>';
                      #  echo '</td></tr></table></TD></tr>';
                        echo "<INPUT type=hidden name=values[SCHOOL][HOME_SCHOOL] value=".UserSchool().">";
			echo '</TABLE></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>';
			echo'</TD></TR>';
			echo '</TABLE>';
                        echo '<TABLE border=0 width=100%><TR><TD>';
			echo '<FIELDSET><LEGEND><FONT color=gray>OpenSIS Access Information</FONT></LEGEND>';
                         if($this_school_mod['USERNAME'] &&  (!$this_school['OPENSIS_ACCESS']=='Y'))
                             {
                                          echo '<table><TR><TD><input type="radio" id="noaccs" name="values[SCHOOL][OPENSIS_ACCESS]" value="N" onClick="hidediv();">&nbsp;No Access &nbsp;&nbsp; <input type="radio" id="r4" name="values[SCHOOL][OPENSIS_ACCESS]" value="Y" onClick="showdiv();" checked>&nbsp;Access</TD></TR></TABLE>';
                                           echo '<div id="hideShow">';
                         }
                       elseif($this_school_mod['USERNAME'] &&  $this_school_mod['PASSWORD'] && $this_school['OPENSIS_ACCESS'])
                         {
                                        if($this_school['OPENSIS_ACCESS']=='N')
                                         echo '<table><TR><TD><input type="radio" id="noaccs" name="values[SCHOOL][OPENSIS_ACCESS]" value="N" checked>&nbsp;No Access &nbsp;&nbsp; <input type="radio" id="r4" name="values[SCHOOL][OPENSIS_ACCESS]" value="Y" >&nbsp;Access</TD></TR></TABLE>';
                                         # echo '<div id="hideShow" style="display:none">';
                                        elseif($this_school['OPENSIS_ACCESS']=='Y')
                                            echo '<table><TR><TD><input type="radio" id="noaccs" name="values[SCHOOL][OPENSIS_ACCESS]" value="N">&nbsp;No Access &nbsp;&nbsp; <input type="radio" id="r4" name="values[SCHOOL][OPENSIS_ACCESS]" value="Y"  checked>&nbsp;Access</TD></TR></TABLE>';
                                          echo '<div id="hideShow">';
                        }
                         elseif(!$this_school_mod['USERNAME'] || $this_school['OPENSIS_ACCESS']=='N' )
                         {
                                         echo '<table><TR><TD><input type="radio" id="noaccs" name="values[SCHOOL][OPENSIS_ACCESS]" value="N" onClick="hidediv();" checked>&nbsp;No Access &nbsp;&nbsp; <input type="radio" id="r4" name="values[SCHOOL][OPENSIS_ACCESS]" value="Y" onClick="showdiv();">&nbsp;Access</TD></TR></TABLE>';
                                          echo '<div id="hideShow" style="display:none">';
                        }
                        
//                        elseif(!$this_school_mod['USERNAME'])
//                         {
//                                        if($this_school['OPENSIS_ACCESS']=='N')
//                                        {
//                                         echo '<table><TR><TD><input type="radio" id="noaccs" name="values[SCHOOL][OPENSIS_ACCESS]" value="N" onClick="hidediv();" checked>&nbsp;No Access &nbsp;&nbsp; <input type="radio" id="r4" name="values[SCHOOL][OPENSIS_ACCESS]" value="Y" onClick="showdiv();">&nbsp;Access</TD></TR></TABLE>';
//                                         echo '<div id="hideShow" style="display:none">';  
//                                         
//                                        }
//                                        else
//                                        {
//                                         echo '<table><TR><TD><input type="radio" id="noaccs" name="values[SCHOOL][OPENSIS_ACCESS]" value="N" onClick="hidediv();" >&nbsp;No Access &nbsp;&nbsp; <input type="radio" id="r4" name="values[SCHOOL][OPENSIS_ACCESS]" value="Y" onClick="showdiv();" checked>&nbsp;Access</TD></TR></TABLE>';
//                                          echo '<div id="hideShow">';  
//                                        }
//                        }
                        
                         # else
                       	echo '<TABLE>';
                        $staff_profile=DBGet(DBQuery("SELECT PROFILE_ID FROM staff WHERE STAFF_ID='".UserStaffID()."'"));
//                        if($staff_profile[1]['PROFILE_ID']!='')
//                        echo '<TR><td>Profile</td><td>:</td><TD> '.SelectInput($this_school['OPENSIS_PROFILE'],'values[SCHOOL][OPENSIS_PROFILE]','',$option,false,'id=values[SCHOOL][OPENSIS_PROFILE] disabled=disabled').'</TD><tr>';
//                        else
                        echo '<TR><td>Profile</td><td>:</td><TD>'.SelectInput($this_school['OPENSIS_PROFILE'],'values[SCHOOL][OPENSIS_PROFILE]','',$option,false,'id=values[SCHOOL][OPENSIS_PROFILE]').'</TD><tr>';
		        echo '<TR><td>Username</td><td>:</td><TD>';
                        if(!$this_school_mod['USERNAME'])
                                {
                                echo NoInput('Will automatically be assigned','');
                                        echo '<span id="ajax_output_stid"></span>';
                                }
                                else
                                {
                                echo NoInput($this_school_mod['USERNAME'],'','','class=cell_medium onkeyup="usercheck_init(this)"').'<div id="ajax_output"></div>'.'</TD></tr>';
                                }
			echo '<TR><td>Password</td><td>:</td><TD>';
                        if(!$this_school_mod['PASSWORD'])
                                {
                                echo NoInput('Will automatically be assigned','');
                                        echo '<span id="ajax_output_stid"></span>';
                                }
                                else
                                {
                            #   echo NoInput($this_school_mod['PASSWORD'],'','','class=cell_medium').'</TD></tr></table>';
                                echo TextInput(array($this_school_mod['PASSWORD'],str_repeat('*',strlen($this_school_mod['PASSWORD']))),'staff_school[PASSWORD]','','size=20 maxlength=100 class=cell_floating AUTOCOMPLETE = off onkeyup=passwordStrength(this.value);validate_password(this.value);');
                                }
                                echo "<td><span id='passwordStrength'></span></td>";
                                echo '<TR>';
                            echo '<TD>Disable User</TD><TD>:</TD><TD>';
                            if($this_school_mod['IS_DISABLE']=='Y')
                                $dis_val='Y';
                            else
                                $dis_val='N';
                            echo CheckboxInput_No($dis_val,'staff_school[IS_DISABLE]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>');
                            echo '</TD>';
                            echo '</TR>';

                        if($this_school['SCHOOL_ACCESS']){

                                $pieces = explode(",", $this_school['SCHOOL_ACCESS']);

                            }

//                        echo '<TABLE><TR><td><span class=red>*</span>Schools Access :</td><TD>';
//	$sql = "SELECT ID,TITLE FROM schools";
//	$QI = DBQuery($sql);
//	$schools_RET = DBGet($QI);
//	unset($options);
//	if(count($schools_RET) && User('PROFILE')=='admin')
//	{
//		$i = 0;
//		$staff_school_chkbox_id=0;
//		echo '<TABLE><TR>';
//
//		foreach($schools_RET as $value)
//		{
//			$staff_school_chkbox_id++;
//			if($i%3==0)
//				echo '</TR><TR>';
//
//
//                        if(in_array($value['ID'], $pieces)){
//                        echo '<TD>'."<input type='checkbox' name='values[SCHOOL_IDS][".$value['ID']."]' id=value_SCHOOLS".$staff_school_chkbox_id." value='".$value['ID']."' checked />".$value['TITLE'].'</TD>';
//                        }else{
//                            echo '<TD>'."<input type='checkbox' name='values[SCHOOL_IDS][".$value['ID']."]' id=value_SCHOOLS".$staff_school_chkbox_id." value='".$value['ID']."'  />".$value['TITLE'].'</TD>';
//                        }
//			$i++;
//
//		}
//
//		echo '</TR><TABLE></TABLE>';
//		echo '<FONT color='.Preferences('TITLES').'></FONT>';
//	}
//	elseif(User('PROFILE')!='admin')
//	{
//		$i = 0;
//		echo '<TABLE><TR><TD>Schools : </TD>';
//		foreach($schools_RET as $value)
//		{
//			if($i%3==0)
//				echo '</TR><TR>';
//			if(strpos($this_school['SCHOOL_ACCESS'],','.$value['ID'].',')!==false)
//			echo '<TD align = center>'.$value['TITLE'].'</TD><TD>&nbsp;</TD>';
//			$i++;
//		}
//		echo '</TR></TABLE>';
//	}

//	echo '</TD></TR>';
//			echo '<TABLE>';
			echo '</TABLE>';
                       
			echo '</div>';
                        echo '</div>';
                        echo'</TD></TR>';
			echo '</TABLE>';
//                         echo '</div>';
//			echo '</div>';
//                        $_REQUEST['category_id'] = 3;
//                        $_REQUEST['custom']='staff';
//                        include('modules/Users/includes/Other_Info.inc.php');
			echo '</FIELDSET>';
                        echo '<br/>';
$profile_return=DBGet(DBQuery("SELECT PROFILE_ID FROM staff WHERE STAFF_ID='".UserStaffID()."'"));
if($profile_return[1]['PROFILE_ID']!='')
{
echo '<table><tr><td><FIELDSET><LEGEND><FONT color=gray>School Information</FONT></LEGEND>';
$functions = array('START_DATE'=>'_makeStartInputDate','PROFILE'=>'_makeUserProfile','END_DATE'=>'_makeEndInputDate','SCHOOL_ID'=>'_makeCheckBoxInput_gen','ID'=>'_makeStatus');
#$functions2=array('SCHOOL_ID'=>'_makeCheckBoxInput_gen');

$sql='SELECT s.ID,ssr.SCHOOL_ID,s.TITLE,ssr.START_DATE,ssr.END_DATE,st.PROFILE FROM schools s,staff st INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE s.id=ssr.school_id  AND st.staff_id='.User('STAFF_ID').' GROUP BY ssr.SCHOOL_ID';
$school_admin=DBGet(DBQuery($sql),$functions);
//$columns = array('SCHOOL_ID'=>'','START_DATE'=>'Start Date','END_DATE'=>'Drop Date','TITLE'=>'School');
$columns = array('SCHOOL_ID'=>'<a><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'values[SCHOOLS]\');" /></a>','TITLE'=>'School','PROFILE'=>'Profile','START_DATE'=>'Start Date','END_DATE'=>'Drop Date','ID'=>'Status');
//$columns = array('SCHOOL_ID'=>'','TITLE'=>'School','PROFILE'=>'Profile','START_DATE'=>'Start Date','END_DATE'=>'Drop Date','ID'=>'Status');
ListOutputStaffPrint($school_admin,$columns,'School Record','School Records',array(),array(),array('search'=>false));
echo '</FIELDSET>';
echo "</td></tr></table>";
}

		}

	}
	else
		echo '';
	$separator = '<HR>';
}

        echo '</TD></TR>';
	echo '</TABLE>'; // end of table 1

function CheckboxInput_No($value,$name,$title='',$checked='',$new=false,$yes='Yes',$no='No',$div=true,$extra='')
{
	// $checked has been deprecated -- it remains only as a placeholder
	if(Preferences('HIDDEN')!='Y')
		$div = false;

	if($div==false || $new==true)
	{
		if($value && $value!='N')
			$checked = 'CHECKED';
		else
			$checked = '';
	}

	if(AllowEdit() && !$_REQUEST['_openSIS_PDF'])
	{
		if($new || $div==false){
			return "<INPUT type=checkbox name=$name value=Y  $extra>".($title!=''?'<BR><small>'.(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'').'</small>':'');
                }
		else{
			return "<DIV id='div$name'><div onclick='javascript:addHTML(\"<INPUT type=hidden name=$name value=\\\"N\\\"><INPUT type=checkbox name=$name ".(($value=='Y')?'checked':'')." value=Y ".str_replace('"','\"',$extra).">".($title!=''?'<BR><small>'.str_replace("'",'&#39;',(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'')).'</small>':'')."\",\"div$name\",true)'>".(($value!='N')?$yes:$no).($title!=''?"<BR><small>".str_replace("'",'&#39;',(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':''))."</small>":'')."</div></DIV>";
                }}
	else
		return (($value!='N')?$yes:$no).($title!=''?'<BR><small>'.(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'').'</small>':'');
}
function _makeStartInputDate($value,$column)
{
    global $THIS_RET;
    #print_r($THIS_RET);
    if($_REQUEST['staff_id']=='new')
    {
        $date_value='';
    }
    else
    {
    $sql='SELECT ssr.START_DATE FROM staff s,staff_school_relationship ssr  WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR='.UserSyear();
    $user_exist_school=DBGet(DBQuery($sql));
    if($user_exist_school[1]['START_DATE']=='0000-00-00' || $user_exist_school[1]['START_DATE']=='')
        $date_value='';
    else
       $date_value=$user_exist_school[1]['START_DATE']; 
    }
        return '<TABLE class=LO_field><TR>'.'<TD>'.DateInput2($date_value,'values[START_DATE]['.$THIS_RET['ID'].']','1'.$THIS_RET['ID'],'').'</TD></TR></TABLE>';
}

function _makeUserProfile($value,$column)
{
   global $THIS_RET;
    if($_REQUEST['staff_id']=='new')
    {
        $profile_value='';
    }
    else
    {
    $sql='SELECT up.TITLE FROM staff s,staff_school_relationship ssr,user_profiles up  WHERE ssr.STAFF_ID=s.STAFF_ID AND up.ID=s.PROFILE_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=   (SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].')';
    $user_profile=DBGet(DBQuery($sql));
    $profile_value=  $user_profile[1]['TITLE'];  
    }
        return '<TABLE class=LO_field><TR>'.'<TD>'.$profile_value.'</TD></TR></TABLE>'; 
}

function _makeEndInputDate($value,$column)
{
    global $THIS_RET;
    if($_REQUEST['staff_id']=='new')
    {
        $date_value='';
    }
    else
    {
//    $sql='SELECT ssr.END_DATE FROM staff s,staff_school_relationship ssr  WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR='.UserSyear();
    $sql='SELECT ssr.END_DATE FROM staff s,staff_school_relationship ssr  WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=   (SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].')';
    $user_exist_school=DBGet(DBQuery($sql));
    if($user_exist_school[1]['END_DATE']=='0000-00-00' || $user_exist_school[1]['END_DATE']=='')
        $date_value='';
    else
       $date_value=$user_exist_school[1]['END_DATE'];  
    }
        return '<TABLE class=LO_field><TR>'.'<TD>'.DateInput2($date_value,'values[END_DATE]['.$THIS_RET['ID'].']','2'.$THIS_RET['ID'].'','').'</TD></TR></TABLE>';
}
function _makeCheckBoxInput_gen($value,$column) 
{	
    global $THIS_RET;
    #print_r($THIS_RET);
    $_SESSION[staff_school_chkbox_id]++;
    $staff_school_chkbox_id=$_SESSION[staff_school_chkbox_id];
    if($_REQUEST['staff_id']=='new')
    {
      return '<TABLE class=LO_field><TR>'.'<TD>'.CheckboxInput('','values[SCHOOLS]['.$THIS_RET['ID'].']','','',true,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>',true,'id=staff_SCHOOLS'.$staff_school_chkbox_id).'</TD></TR></TABLE>';        
    }
    else
    {
//    $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR='.UserSyear().' AND (ssr.END_DATE>=CURDATE() OR ssr.END_DATE=\'0000-00-00\')';
      $dates=DBGet(DBQuery("SELECT ssr.START_DATE,ssr.END_DATE FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='".$THIS_RET['SCHOOL_ID']."' AND ssr.STAFF_ID='".$_SESSION['staff_selected']."' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='".$THIS_RET['SCHOOL_ID']."' AND STAFF_ID='".$_SESSION['staff_selected']."')"));
      if($dates[1]['START_DATE']=='0000-00-00' && $dates[1]['END_DATE']=='0000-00-00')
      {
       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].')';   
      }
      if($dates[1]['START_DATE']=='0000-00-00' && $dates[1]['END_DATE']!='0000-00-00')
      {
       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].') AND (ssr.END_DATE>=CURDATE() OR ssr.END_DATE=\'0000-00-00\')';   
      }
      if($dates[1]['START_DATE']!='0000-00-00')
      {
       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].')  AND (ssr.START_DATE>=ssr.END_DATE OR ssr.START_DATE=\'0000-00-00\' OR ssr.END_DATE>=CURDATE())';   
      }
//       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].') AND (ssr.END_DATE>=CURDATE() OR ssr.END_DATE=\'0000-00-00\')  AND (ssr.START_DATE>=ssr.END_DATE OR ssr.START_DATE=\'0000-00-00\')';
//     $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].') AND  (ssr.START_DATE>=ssr.END_DATE OR ssr.START_DATE=\'0000-00-00\')';
    $user_exist_school=DBGet(DBQuery($sql));
    if(!empty($user_exist_school))
      return '<TABLE class=LO_field><TR>'.'<TD>'.CheckboxInput('Y','values[SCHOOLS]['.$THIS_RET['ID'].']','','',true,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>',true,'id=staff_SCHOOLS'.$staff_school_chkbox_id).'</TD></TR></TABLE>';
    else
      return '<TABLE class=LO_field><TR>'.'<TD>'.CheckboxInput('','values[SCHOOLS]['.$THIS_RET['ID'].']','','',true,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>',true,'id=staff_SCHOOLS'.$staff_school_chkbox_id).'</TD></TR></TABLE>';
    }
}

function _makeStatus($value,$column)
{
    global $THIS_RET;
    if($_REQUEST['staff_id']=='new')
        $status_value='';
    else
    {
//      $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR='.UserSyear().' AND (ssr.END_DATE>=CURDATE() OR ssr.END_DATE=\'0000-00-00\')';  
      $dates=DBGet(DBQuery("SELECT ssr.START_DATE,ssr.END_DATE FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='".$THIS_RET['SCHOOL_ID']."' AND ssr.STAFF_ID='".$_SESSION['staff_selected']."' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='".$THIS_RET['SCHOOL_ID']."' AND STAFF_ID='".$_SESSION['staff_selected']."')"));
      if($dates[1]['START_DATE']=='0000-00-00' && $dates[1]['END_DATE']=='0000-00-00')
      {
       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].')';   
      }
   
      if($dates[1]['START_DATE']=='0000-00-00' && $dates[1]['END_DATE']!='0000-00-00')
      {
       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].') AND (ssr.END_DATE>=CURDATE() OR ssr.END_DATE=\'0000-00-00\')';   
      }
      if($dates[1]['START_DATE']!='0000-00-00')
      {
       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].')  AND (ssr.START_DATE>=ssr.END_DATE OR ssr.START_DATE=\'0000-00-00\')';   
      }
//        $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].') AND (ssr.END_DATE>=CURDATE() OR ssr.END_DATE=\'0000-00-00\') AND (ssr.START_DATE>=ssr.END_DATE OR ssr.START_DATE=\'0000-00-00\')';
//       $sql='SELECT SCHOOL_ID FROM staff s,staff_school_relationship ssr WHERE ssr.STAFF_ID=s.STAFF_ID AND ssr.SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND ssr.STAFF_ID='.$_SESSION['staff_selected'].' AND ssr.SYEAR=(SELECT MAX(SYEAR) FROM  staff_school_relationship WHERE SCHOOL_ID='.$THIS_RET['SCHOOL_ID'].' AND STAFF_ID='.$_SESSION['staff_selected'].') AND (ssr.START_DATE>=ssr.END_DATE OR ssr.START_DATE=\'0000-00-00\')';
       $user_exist_school=DBGet(DBQuery($sql));
       if(!empty($user_exist_school))
         $status_value='Active';  
        else
         $status_value='';
//         $status_value='Resigned';    
    }    
     return '<TABLE class=LO_field><TR>'.'<TD>'.$status_value.'</TD></TR></TABLE>'; 
}

?>
