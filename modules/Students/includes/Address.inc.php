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

include('../../../Redirect_includes.php');
include 'modules/Students/config.inc.php';

if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
{
//    print_r($_REQUEST);
    if($_REQUEST['r7']=='Y')
    {    
        $get_home_add=DBGet(DBQuery('SELECT address,street,city,state,zipcode,bus_pickup,bus_dropoff,bus_no FROM student_address WHERE STUDENT_ID=\''.UserStudentID().'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID= \''.  UserSchool().'\' AND TYPE=\'Home Address\' '));
        if(count($get_home_add)>0)
        {
           foreach($get_home_add[1] as $gh_i=>$gh_d)
           {
               if($gh_d!='')
               $_REQUEST['values']['student_address']['OTHER'][$gh_i]=$gh_d;
           }
        }
        else
        {
            echo "<script>show_home_error();</script>";
            unset($_REQUEST['values']);
        }
    }
    
    if($_REQUEST['r4']=='Y')
    {    
        $_REQUEST['values']['student_address']['MAIL']['ADDRESS']=$_REQUEST['values']['student_address']['HOME']['ADDRESS'];
        $_REQUEST['values']['student_address']['MAIL']['STREET']=$_REQUEST['values']['student_address']['HOME']['STREET'];
        $_REQUEST['values']['student_address']['MAIL']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        $_REQUEST['values']['student_address']['MAIL']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
        $_REQUEST['values']['student_address']['MAIL']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
    }
    if($_REQUEST['same_addr']=='Y')
    {    

        $address_details=DBGEt(DBQuery('SELECT ADDRESS,STREET,CITY,STATE,ZIPCODE FROM  student_address WHERE STUDENT_ID='.$_REQUEST['student_id'].' AND type=\'Home Address\' '));
        if(isset($_REQUEST['values']['student_address']['HOME']['ADDRESS']))
        $_REQUEST['values']['student_address']['MAIL']['ADDRESS']=$_REQUEST['values']['student_address']['HOME']['ADDRESS'];
        else
        $_REQUEST['values']['student_address']['MAIL']['ADDRESS']=$address_details[1]['ADDRESS'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STREET']))
        $_REQUEST['values']['student_address']['MAIL']['STREET']=$_REQUEST['values']['student_address']['HOME']['STREET'];
        else
        $_REQUEST['values']['student_address']['MAIL']['STREET']=$address_details[1]['STREET'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['CITY']))
        $_REQUEST['values']['student_address']['MAIL']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        else
        $_REQUEST['values']['student_address']['MAIL']['CITY']=$address_details[1]['CITY'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['ZIPCODE']))
        $_REQUEST['values']['student_address']['MAIL']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
        else
        $_REQUEST['values']['student_address']['MAIL']['ZIPCODE']=$address_details[1]['ZIPCODE'];
        
        if(isset($_REQUEST['values']['student_address']['HOME']['STATE']))
        $_REQUEST['values']['student_address']['MAIL']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
        else
        $_REQUEST['values']['student_address']['MAIL']['STATE']=$address_details[1]['STATE'];
    }
    
    if($_REQUEST['r6']=='Y')
    {    
        $_REQUEST['values']['student_address']['SECONDARY']['ADDRESS']=$_REQUEST['values']['student_address']['HOME']['ADDRESS'];
        $_REQUEST['values']['student_address']['SECONDARY']['STREET']=$_REQUEST['values']['student_address']['HOME']['STREET'];
        $_REQUEST['values']['student_address']['SECONDARY']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        $_REQUEST['values']['student_address']['SECONDARY']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
        $_REQUEST['values']['student_address']['SECONDARY']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
    }
    
    if($_REQUEST['r5']=='Y')
    {    
        $_REQUEST['values']['student_address']['PRIMARY']['ADDRESS']=$_REQUEST['values']['student_address']['HOME']['ADDRESS'];
        $_REQUEST['values']['student_address']['PRIMARY']['STREET']=$_REQUEST['values']['student_address']['HOME']['STREET'];
        $_REQUEST['values']['student_address']['PRIMARY']['CITY']=$_REQUEST['values']['student_address']['HOME']['CITY'];
        $_REQUEST['values']['student_address']['PRIMARY']['ZIPCODE']=$_REQUEST['values']['student_address']['HOME']['ZIPCODE'];
        $_REQUEST['values']['student_address']['PRIMARY']['STATE']=$_REQUEST['values']['student_address']['HOME']['STATE'];
    }
    
    
//    print_r($_REQUEST['values']);
//    exit;
    
    foreach($_REQUEST['values'] as $table=>$type)
    {
        foreach($type as $ind=>$val)
        {
           if($val['ID']!='new')
           {
               $go='false';
               $cond_go='false';
               foreach($val as $col=>$col_v)
               {   

                        if($col!='ID')
                        {
                            if($col=='PASSWORD' && $col_v!='')
                            {
//                                $set_arr[]=$col."='".md5(str_replace("'","\'",$col_v))."'";   
                                $password=md5(str_replace("'","''",$col_v));
                            }
                            elseif ($col=='USER_NAME' && $col_v!='') 
                            {
                                $user_name_val=str_replace("'","''",$col_v);
                            }
                            elseif($col=='RELATIONSHIP' && $col_v!='')
                                $rel_stu[]=$col.'=\''.str_replace("'","''",str_replace("'","\'",$col_v)).'\'';
                            elseif($col=='IS_EMERGENCY' && $col_v!='')
                                $rel_stu[]=$col.'=\''.str_replace("'","''",str_replace("'","\'",$col_v)).'\'';
                            else 
                            {
                                if($col!='USER_NAME' && $col!='RELATIONSHIP' && $col!='PASSWORD' && $col!='IS_EMERGENCY')
                                 $set_arr[]=$col."='".str_replace("'","''",$col_v)."'";                            
                            }  
                            $go='true';
                        }

                   if($col=='ID' && $col_v!='')
                   {
                       if($table=='people')
                       {
                            $where='STAFF_ID='.$col_v;
                            if($ind=='PRIMARY')
                                $pri_up_pl_id=$col_v;
                            if($ind=='SECONDARY')
                                $sec_up_pl_id=$col_v;
                            if($ind=='OTHER')
                                $oth_up_pl_id=$col_v;
                       }
                       else
                            $where=' ID='.$col_v;
                       $cond_go='true';
                   }
               }
               $set_arr=implode(',',$set_arr);
               $rel_stu=implode(',',$rel_stu);
               if($set_arr!='')
               $qry='UPDATE '.$table.' SET '.$set_arr.' WHERE '.$where;
               
               //codes to be inserted
               
               if($go=='true' && $cond_go=='true')
                    DBQuery($qry);
               if($ind=='PRIMARY' && $rel_stu!='')
               {
                   DBQuery('UPDATE students_join_people SET '.$rel_stu.' WHERE EMERGENCY_TYPE=\'Primary\' AND PERSON_ID='.$pri_up_pl_id.'');
               }
               if($ind=='SECONDARY' && $rel_stu!='')
               {
                   DBQuery('UPDATE students_join_people SET '.$rel_stu.' WHERE EMERGENCY_TYPE=\'Secondary\' AND PERSON_ID='.$sec_up_pl_id.'');
               }
               if($ind=='OTHER' && $rel_stu!='')
               {
                   DBQuery('UPDATE students_join_people SET '.$rel_stu.' WHERE EMERGENCY_TYPE=\'Other\' AND PERSON_ID='.$oth_up_pl_id.'');
               }
               if($table=='people' && $ind=='PRIMARY')
                { 
                    if(clean_param($_REQUEST['primary_portal'],PARAM_ALPHAMOD)=='Y' && $password!='' )
                    {
//                        $people_exists=DBQuery('SELECT * FROM login_authentication WHERE USER_ID='.$pri_up_pl_id.' AND PROFILE_ID=3');
//                        if(count($people_exists)>0)
//                        {
                            $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
                            $num_pass = DBGet($res_pass_chk);
                            if(count($num_pass)==0)
                            {
                                
//                                DBQuery('UPDATE login_authentication SET PASSWORD=\''.$password.'\' WHERE USER_ID='.$pri_up_pl_id.' AND PROFILE_ID=3');
                                DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$pri_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',4)');
                            }
                            else
                            { 
                                echo '<font color = red><b>Password already exists.</b></font>';
                            }
//                        }
//                        else
//                        {
//                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$pri_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',3)');
//                        }
                    }
                }
               if($table=='people' && $ind=='SECONDARY')
               { 
                   if(clean_param($_REQUEST['secondary_portal'],PARAM_ALPHAMOD)=='Y' && $password!='')
                   {
//		        $ins_profile=3;
//                        $people_exists=DBQuery('SELECT * FROM login_authentication WHERE USER_ID='.$sec_up_pl_id.' AND PROFILE_ID=3');
//                        if(count($people_exists)>0)
//                        {
                            $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
                            $num_pass = DBGet($res_pass_chk);
                            if(count($num_pass)==0)
                            {
//                                DBQuery('UPDATE login_authentication SET PASSWORD=\''.$password.'\' WHERE USER_ID='.$sec_up_pl_id.' AND PROFILE_ID=3');
                                DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$sec_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',4)');
                            }
                            else
                            {
    //                            $staff_id=  DBGet(DBQuery('SELECT STAFF_ID FROM staff WHERE USERNAME=\''.$type['PRIMARY']['USER_NAME'].'\''));
    //                            $staff_id=$staff_id[1]['STAFF_ID'];
    //                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
                                echo '<font color = red><b>Password already exists.</b></font>';
                            }
//                        }
//                        else
//                        {
                            
//                        }
                    }
               }
               if($table=='people' && $ind=='OTHER')
               { 
                   if(clean_param($_REQUEST['other_portal'],PARAM_ALPHAMOD)=='Y' && $password!='')
                   {
//		        $ins_profile=3;
//                        $people_exists=DBQuery('SELECT * FROM login_authentication WHERE USER_ID='.$oth_up_pl_id.' AND PROFILE_ID=3');
//                        if(count($people_exists)>0)
//                        {
                            $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD=\''.$password.'\'');
                            $num_pass = DBGet($res_pass_chk);
                            if(count($num_pass)==0)
                            {
    //                            DBQuery('INSERT INTO staff (CURRENT_SCHOOL_ID,FIRST_NAME,LAST_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,PROFILE_ID) VALUES ('.UserSchool().',\''.$type['PRIMARY']['FIRST_NAME'].'\',\''.$type['PRIMARY']['LAST_NAME'].'\',\''.$type['PRIMARY']['USER_NAME'].'\',\''.$password.'\',\''.$type['PRIMARY']['HOME_PHONE'].'\',\''.$type['PRIMARY']['EMAIL'].'\',\'parent\','.$ins_profile.')');
    //                            $staff_id=  DBGet(DBQuery('SELECT max(STAFF_ID) AS STAFF_ID FROM staff'));
    //                            $staff_id=$staff_id[1]['STAFF_ID'];
    //                            DBQuery('INSERT INTO staff_school_relationship VALUES ('.$staff_id.','.UserSchool().','.UserSyear().',\'0000-00-00\',\'0000-00-00\')');
    //                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
//                                DBQuery('UPDATE login_authentication SET PASSWORD=\''.$password.'\' WHERE USER_ID='.$oth_up_pl_id.' AND PROFILE_ID=3');
                                DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$oth_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',4)');

                            }
                            else
                            {
    //                            $staff_id=  DBGet(DBQuery('SELECT STAFF_ID FROM staff WHERE USERNAME=\''.$type['PRIMARY']['USER_NAME'].'\''));
    //                            $staff_id=$staff_id[1]['STAFF_ID'];
    //                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
                                echo '<font color = red><b>Password already exists.</b></font>';
                            }
//                        }
//                        else
//                        {
//                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$oth_up_pl_id.',\''.$user_name_val.'\',\''.$password.'\',3)');
//                        }
                    }
               }
               unset($set_arr);
               unset($where);
               unset($col);
               unset($col_v);
               unset($go);
               unset($cond_go);
               unset($password);
               unset($user_name_val);
               unset($rel_stu);
               
//               echo $qry.'<br><br>';
           }
           else
           {    
               $pri_pep_exists='N';
               $sec_pep_exists='N';
               $oth_pep_exists='N';
               if($ind=='PRIMARY' || $ind=='SECONDARY')
               {
                    $pri_people_exists=  DBGet (DBQuery ('SELECT * FROM people WHERE FIRST_NAME=\''.$_REQUEST['values']['people']['PRIMARY']['FIRST_NAME'].'\' AND LAST_NAME=\''.$_REQUEST['values']['people']['PRIMARY']['LAST_NAME'].'\' AND EMAIL=\''.$_REQUEST['values']['people']['PRIMARY']['EMAIL'].'\''));
                    if(count($pri_people_exists)>0)
                    {
                        $pri_person_id=$pri_people_exists[1]['STAFF_ID'];
                        $pri_pep_exists='Y';
                    }
                    else
                    {
                         $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'people'"));
                         $pri_person_id= $id[1]['AUTO_INCREMENT'];
                    }
                    $sec_people_exists=  DBGet (DBQuery ('SELECT * FROM people WHERE FIRST_NAME=\''.$_REQUEST['values']['people']['SECONDARY']['FIRST_NAME'].'\' AND LAST_NAME=\''.$_REQUEST['values']['people']['SECONDARY']['LAST_NAME'].'\' AND EMAIL=\''.$_REQUEST['values']['people']['SECONDARY']['EMAIL'].'\''));
                    if(count($sec_people_exists)>0)
                    {
                        $sec_person_id=$sec_people_exists[1]['STAFF_ID'];
                        $sec_pep_exists='Y';
                    }
                    else
                    {
                        if($pri_pep_exists=='Y')
                        {
                             $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'people'"));
                             $sec_person_id= $id[1]['AUTO_INCREMENT'];
                        }
                        else
                         $sec_person_id = $pri_person_id+1;
                    }
               }
               if($ind=='OTHER' && $table=='people')
               {
                   $oth_people_exists=  DBGet (DBQuery ('SELECT * FROM people WHERE FIRST_NAME=\''.$_REQUEST['values']['people']['OTHER']['FIRST_NAME'].'\' AND LAST_NAME=\''.$_REQUEST['values']['people']['OTHER']['LAST_NAME'].'\' AND EMAIL=\''.$_REQUEST['values']['people']['OTHER']['EMAIL'].'\''));
                    if(count($oth_people_exists)>0)
                    {
                        $oth_person_id=$oth_people_exists[1]['STAFF_ID'];
                        $oth_pep_exists='Y';
                    }
                    else
                    {
                         $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'people'"));
                         $oth_person_id= $id[1]['AUTO_INCREMENT'];
                    }
               }
               $go='false';
               $log_go=false;
               foreach($val as $col=>$col_v)
               {
                   if($table=='student_address')
                   {
                        if($col!='ID' && $col_v!='')
                        {
                            $fields[]=$col;
     //                       if($col=='PASSWORD' && $col_v!='')
     //                       {
     //                           $field_vals[]="'".md5(str_replace("'","\'",$col_v))."'"; 
     //                           $password=md5(str_replace("'","\'",$col_v));
     //                       }
     //                       else 
     //                       {
                                $field_vals[]="'".str_replace("'","\'",$col_v)."'";
     //                       }                      
                            $go='true';
                        }
                   }
                   if($table=='people')
                   {
                       if($col!='ID' && $col_v!='')
                        {
                           if($col=='RELATIONSHIP' || $col=='IS_EMERGENCY')
                           {
                               $sjp_field=$col.',';
                               $sjp_value="'".$col_v."',";
                           }
                           else
                           {
                               if($col!='PASSWORD' && $col!='USER_NAME')
                               {
                                    $peo_fields[]=$col;
                                    $peo_field_vals[]="'".str_replace("'","\'",$col_v)."'";
                                    $log_go=true;
                               }
                           }
                        }
                   }
               }
               $fields=implode(',',$fields);
               $field_vals=implode(',',$field_vals);
               $peo_fields=implode(',',$peo_fields);
               $peo_field_vals=implode(',',$peo_field_vals);
               if($table=='student_address')
               {
                   if($ind=='PRIMARY' || $ind=='SECONDARY' || $ind=='OTHER')
                    $type_n='type,people_id';
                   else
                       $type_n='type';
               }
               if($ind=='HOME')
               $ind_n="'Home Address'";
               if($ind=='PRIMARY')
               $ind_n="'Primary',".$pri_person_id."";
               if($ind=='SECONDARY')
               $ind_n="'Secondary',".$sec_person_id."";
               if($ind=='OTHER')
               $ind_n="'Other',".$oth_person_id."";
               if($ind=='MAIL')
               $ind_n="'Mail'";
               
//               if($table=='student_address' && $ind=='OTHER')
//                {
//                    $g_id=DBGet(DBQuery('SELECT ID FROM student_contacts WHERE STUDENT_ID=\''.UserStudentID().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY ID DESC LIMIT 0,1'));
//                    $g_id=$g_id[1]['ID'];
//                    $type_n.=',other_id';
//                    $ind_n.=','.$g_id;
//                    $go='true';
//                }
               if($table=='student_address')
               {
                   if($ind=='HOME' || $ind=='MAIL')
                       $qry='INSERT INTO '.$table.' (student_id,syear,school_id,'.$fields.','.$type_n.') VALUES ('.UserStudentID().','.UserSyear().','.UserSchool().','.$field_vals.','.$ind_n.') ';
                   if(($ind=='PRIMARY' && $pri_pep_exists=='N') || ($ind=='SECONDARY' && $sec_pep_exists=='N') || ($ind=='OTHER' && $oth_pep_exists=='N'))
                       $qry='INSERT INTO '.$table.' (student_id,syear,school_id,'.$fields.','.$type_n.') VALUES ('.UserStudentID().','.UserSyear().','.UserSchool().','.$field_vals.','.$ind_n.') ';
               }
               if($table=='people')
               {
                  $sql_sjp='INSERT INTO students_join_people ('.$sjp_field.'student_id,emergency_type,person_id) VALUES ('.$sjp_value.UserStudentID().','.$ind_n.')';
                  if(($ind=='PRIMARY' && $pri_pep_exists=='N') || ($ind=='SECONDARY' && $sec_pep_exists=='N') || ($ind=='OTHER' && $oth_pep_exists=='N'))
                    $sql_peo='INSERT INTO people (CURRENT_SCHOOL_ID,profile,profile_id,'.$peo_fields.') VALUES ('.UserSchool().',\'parent\',4,'.$peo_field_vals.')';
               }
               if($go=='true' & $qry!='')
                    DBQuery($qry);
//                   echo $qry;
               if($log_go)
               {
                   DBQuery($sql_sjp);
//                   echo $sql_sjp;
                   if(($ind=='PRIMARY' && $pri_pep_exists=='N') || ($ind=='SECONDARY' && $sec_pep_exists=='N') || ($ind=='OTHER' && $oth_pep_exists=='N'))
                        DBQuery($sql_peo);
//                       echo $sql_peo;
               }

                if($table=='people' && $ind=='PRIMARY' && $type['PRIMARY']['USER_NAME']!='' && $pri_pep_exists=='N')
                { 
                    if(clean_param($_REQUEST['primary_portal'],PARAM_ALPHAMOD)=='Y')
                    {
                        $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD = \''.md5($type['PRIMARY']['PASSWORD']).'\'');
                        $num_pass = DBGet($res_pass_chk);
                        if(count($num_pass)==0)
                        {
//                            DBQuery('INSERT INTO people (CURRENT_SCHOOL_ID,FIRST_NAME,LAST_NAME,PHONE,EMAIL,PROFILE,PROFILE_ID) VALUES ('.UserSchool().',\''.$type['PRIMARY']['FIRST_NAME'].'\',\''.$type['PRIMARY']['LAST_NAME'].'\',\''.$type['PRIMARY']['HOME_PHONE'].'\',\''.$type['PRIMARY']['EMAIL'].'\',\'parent\','.$ins_profile.')');
//                            $staff_id=  DBGet(DBQuery('SELECT max(STAFF_ID) AS STAFF_ID FROM staff'));
//                            $staff_id=$staff_id[1]['STAFF_ID'];
//                            echo 'INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$pri_person_id.',\''.$type['PRIMARY']['USER_NAME'].'\',\''.md5($type['PRIMARY']['PASSWORD']).'\',3)';
                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$pri_person_id.',\''.$type['PRIMARY']['USER_NAME'].'\',\''.md5($type['PRIMARY']['PASSWORD']).'\',4)');
//                            DBQuery('INSERT INTO staff_school_relationship VALUES ('.$staff_id.','.UserSchool().','.UserSyear().',\'0000-00-00\',\'0000-00-00\')');
//                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
                        }
                        else
                        {
//                            $staff_id=  DBGet(DBQuery('SELECT USER_ID AS STAFF_ID FROM login_authentication WHERE USERNAME=\''.$type['PRIMARY']['USER_NAME'].'\''));
//                            $staff_id=$staff_id[1]['STAFF_ID'];
//                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
                            echo '<font color = red><b>Password already exists.</b></font>';
                        }
                    }
                    
               }
               if($table=='people' && $ind=='SECONDARY' && $sec_pep_exists=='N')
               { 
                   if(clean_param($_REQUEST['secondary_portal'],PARAM_ALPHAMOD)=='Y' && $type['SECONDARY']['USER_NAME']!='')
                   {
                        $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD = \''.md5($type['SECONDARY']['PASSWORD']).'\'');
                        $num_pass = DBGet($res_pass_chk);
                        if(count($num_pass)==0)
                        {
//                            DBQuery('INSERT INTO staff (CURRENT_SCHOOL_ID,FIRST_NAME,LAST_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,PROFILE_ID) VALUES ('.UserSchool().',\''.$type['SECONDARY']['FIRST_NAME'].'\',\''.$type['SECONDARY']['LAST_NAME'].'\',\''.$type['SECONDARY']['USER_NAME'].'\',\''.$password.'\',\''.$type['SECONDARY']['HOME_PHONE'].'\',\''.$type['SECONDARY']['EMAIL'].'\',\'parent\','.$ins_profile.')');
//                            $staff_id=  DBGet(DBQuery('SELECT max(STAFF_ID) AS STAFF_ID FROM staff'));
//                            $staff_id=$staff_id[1]['STAFF_ID'];
//                            DBQuery('INSERT INTO staff_school_relationship VALUES ('.$staff_id.','.UserSchool().','.UserSyear().',\'0000-00-00\',\'0000-00-00\')');
//                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
//                            echo 'INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$sec_person_id.',\''.$type['SECONDARY']['USER_NAME'].'\',\''.md5($type['SECONDARY']['PASSWORD']).'\',3)';
                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$sec_person_id.',\''.$type['SECONDARY']['USER_NAME'].'\',\''.md5($type['SECONDARY']['PASSWORD']).'\',4)');
                        }
                        else
                        {
//                            $staff_id=  DBGet(DBQuery('SELECT STAFF_ID FROM staff WHERE USERNAME=\''.$type['SECONDARY']['USER_NAME'].'\''));
//                            $staff_id=$staff_id[1]['STAFF_ID'];
//                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
                            echo '<font color = red><b>Password already exists.</b></font>';
                        }
                   }
                   
               }
               if($table=='people' && $ind=='OTHER' && $oth_pep_exists=='N')
                { 
                    if(clean_param($_REQUEST['other_portal'],PARAM_ALPHAMOD)=='Y' && $type['OTHER']['USER_NAME']!='')
                    {
                        $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD = \''.md5($type['OTHER']['PASSWORD']).'\'');
                        $num_pass = DBGet($res_pass_chk);
                        if(count($num_pass)==0)
                        {
//                            DBQuery('INSERT INTO people (CURRENT_SCHOOL_ID,FIRST_NAME,LAST_NAME,PHONE,EMAIL,PROFILE,PROFILE_ID) VALUES ('.UserSchool().',\''.$type['PRIMARY']['FIRST_NAME'].'\',\''.$type['PRIMARY']['LAST_NAME'].'\',\''.$type['PRIMARY']['HOME_PHONE'].'\',\''.$type['PRIMARY']['EMAIL'].'\',\'parent\','.$ins_profile.')');
//                            $staff_id=  DBGet(DBQuery('SELECT max(STAFF_ID) AS STAFF_ID FROM staff'));
//                            $staff_id=$staff_id[1]['STAFF_ID'];
                            DBQuery('INSERT INTO login_authentication (USER_ID,USERNAME,PASSWORD,PROFILE_ID) VALUES ('.$oth_person_id.',\''.$type['OTHER']['USER_NAME'].'\',\''.md5($type['OTHER']['PASSWORD']).'\',4)');
//                            DBQuery('INSERT INTO staff_school_relationship VALUES ('.$staff_id.','.UserSchool().','.UserSyear().',\'0000-00-00\',\'0000-00-00\')');
//                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
                        }
                        else
                        {
//                            $staff_id=  DBGet(DBQuery('SELECT USER_ID AS STAFF_ID FROM login_authentication WHERE USERNAME=\''.$type['PRIMARY']['USER_NAME'].'\''));
//                            $staff_id=$staff_id[1]['STAFF_ID'];
//                            DBQuery('INSERT INTO students_join_users (STAFF_ID,STUDENT_ID) values(\''.$staff_id.'\',\''.UserStudentID().'\')');
                            echo '<font color = red><b>Password already exists.</b></font>';
                        }
                    }
                    
               }
//               echo $qry.'<br><br><br><br>';
               unset($fields);
               unset($qry);
               unset($field_vals);
               unset($peo_fields);
               unset($peo_field_vals);
               unset($sjp_field);
               unset($sjp_value);
               unset($log_go);
               unset($where);
               unset($col);
               unset($col_v);
               unset($type_n);
               unset($ind_n);
               unset($go);
           }
        }
    }
}

//if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
//{
//	if($_REQUEST['values']['EXISTING'])
//	{
//		if($_REQUEST['values']['EXISTING']['address_id'] && $_REQUEST['address_id']=='old')
//		{
//			$_REQUEST['address_id'] = $_REQUEST['values']['EXISTING']['address_id'];
//			$address_RET = DBGet(DBQuery("SELECT '' FROM students_join_address WHERE ADDRESS_ID='$_REQUEST[address_id]' AND STUDENT_ID='".UserStudentID()."'"));
//			if(count($address_RET)==0)
//			{
//			DBQuery('INSERT INTO students_join_address (STUDENT_ID,ADDRESS_ID) values(\''.UserStudentID().'\',\''.$_REQUEST[address_id].'\')');
//			DBQuery('INSERT INTO students_join_people (STUDENT_ID,PERSON_ID,ADDRESS_ID) SELECT DISTINCT ON (PERSON_ID) \''.UserStudentID().'\',PERSON_ID,ADDRESS_ID FROM students_join_people WHERE ADDRESS_ID=\''.$_REQUEST[address_id].'\'');
//			}
//		}
//		elseif($_REQUEST['values']['EXISTING']['person_id'] && $_REQUEST['person_id']=='old')
//		{
//			$_REQUEST['person_id'] = $_REQUEST['values']['EXISTING']['person_id'];
//			$people_RET = DBGet(DBQuery('SELECT \'\' FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\' AND STUDENT_ID=\''.UserStudentID().'\''));
//			if(count($people_RET)==0)
//			{
//			DBQuery('INSERT INTO students_join_people (STUDENT_ID,ADDRESS_ID,PERSON_ID) values(\''.UserStudentID().'\',\''.$_REQUEST[address_id].'\',\''.$_REQUEST[person_id].'\')');
//			}
//		}
//	}
//
//	if(clean_param($_REQUEST['values']['address'],PARAM_NOTAGS))
//	{
//	// echo 'sid= '.$_REQUEST['address_id'];
//		if($_REQUEST['address_id']!='new')
//		{
//			$sql = 'UPDATE address SET ';
//
//			foreach($_REQUEST['values']['address'] as $column=>$value)
//			{
//				if(!is_array($value)){
//                                    
//                                $value=paramlib_validation($column,trim($value));
//                                $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';}
//				else
//				{
//					$sql .= $column."='||";
//					foreach($value as $val)
//					{
//						if($val)
//							$sql .= str_replace("'","''",str_replace('&quot;','"',$val)).'||';
//					}
//					$sql .= '\',';
//				}
//			}
//			$sql = substr($sql,0,-1) . ' WHERE ADDRESS_ID=\''.$_REQUEST[address_id].'\'';
//			DBQuery($sql);
//			$query='SELECT ADDRESS_ID FROM 
//students_join_address
// WHERE STUDENT_ID=\''.UserStudentID().'\'';
//			$a_ID=DBGet(DBQuery($query));
//			$a_ID=$a_ID[1]['ADDRESS_ID'];
//			if($a_ID == 0)
//			{
//				$id=DBGet(DBQuery('SELECT ADDRESS_ID  FROM address WHERE STUDENT_ID=\''.UserStudentID().'\''));
//				$id=$id[1]['ADDRESS_ID'];
//				DBQuery('UPDATE students_join_address SET ADDRESS_ID=\''.$id.'\',RESIDENCE=\''.$_REQUEST['values']['students_join_address']['RESIDENCE'].'\', MAILING=\''.$_REQUEST['values']['students_join_address']['MAILING'].'\',BUS_PICKUP=\''.$_REQUEST['values']['students_join_address']['BUS_PICKUP'].'\', BUS_DROPOFF=\''.$_REQUEST['values']['students_join_address']['BUS_DROPOFF'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
//			if($_REQUEST['r4']=='Y' && $_REQUEST['r4']!='N')
//			{
//			DBQuery('UPDATE address SET MAIL_ADDRESS=\''.$_REQUEST['values']['address']['ADDRESS'].'\',MAIL_STREET=\''.$_REQUEST['values']['address']['STREET'].'\', MAIL_CITY=\''.$_REQUEST['values']['address']['CITY'].'\',MAIL_STATE=\''.$_REQUEST['values']['address']['STATE'].'\', MAIL_ZIPCODE=\''.$_REQUEST['values']['address']['ZIPCODE'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
//			}
//			if($_REQUEST['r5']=='Y' && $_REQUEST['r5']!='N')
//			{
//			DBQuery('UPDATE address SET PRIM_ADDRESS=\''.$_REQUEST['values']['address']['ADDRESS'].'\',PRIM_STREET=\''.$_REQUEST['values']['address']['STREET'].'\', PRIM_CITY=\''.$_REQUEST['values']['address']['CITY'].'\',PRIM_STATE=\''.$_REQUEST['values']['address']['STATE'].'\', PRIM_ZIPCODE=\''.$_REQUEST['values']['address']['ZIPCODE'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
//			}
//			if($_REQUEST['r6']=='Y' && $_REQUEST['r6']!='N')
//			{
//			DBQuery('UPDATE address SET SEC_ADDRESS=\''.$_REQUEST['values']['address']['ADDRESS'].'\',SEC_STREET=\''.$_REQUEST['values']['address']['STREET'].'\', SEC_CITY=\''.$_REQUEST['values']['address']['CITY'].'\',SEC_STATE=\''.$_REQUEST['values']['address']['STATE'].'\', SEC_ZIPCODE=\''.$_REQUEST['values']['address']['ZIPCODE'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
//			}
//
//		  }		
//                  if($a_ID != 0)
//                  {
//                      $flag=false;
//                      if($_REQUEST['same_addr']=='Y')
//			{
//                          
//                          $sql = 'UPDATE address SET ';
//                         
//                          if($_REQUEST['values']['address']['ADDRESS'])
//                          {
//                              $sql .= 'MAIL_ADDRESS'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',';
//                              $flag=true;
//		}
//                          if($_REQUEST['values']['address']['STREET'])
//                          {
//                              $sql .= 'MAIL_STREET'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['CITY'])
//                          {
//                              $sql .= 'MAIL_CITY'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['STATE'])
//                          {
//                              $sql .= 'MAIL_STATE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['ZIPCODE'])
//                          {
//                              $sql .= 'MAIL_ZIPCODE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
//                              $flag=true;
//                          }
//                          $sql = substr($sql,0,-1);
//                        $sql.=' WHERE STUDENT_ID=\''.UserStudentID().'\'';
//                        if($flag)
//                            DBQuery($sql);
//
//			}
//			if($_REQUEST['prim_addr']=='Y')
//			{
//                            $sql = 'UPDATE address SET ';
//                         
//                          if($_REQUEST['values']['address']['ADDRESS'])
//                          {
//                              $sql .= 'PRIM_ADDRESS'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['STREET'])
//                          {
//                              $sql .= 'PRIM_STREET'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['CITY'])
//                          {
//                              $sql .= 'PRIM_CITY'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['STATE'])
//                          {
//                              $sql .= 'PRIM_STATE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['ZIPCODE'])
//                          {
//                              $sql .= 'PRIM_ZIPCODE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
//                              $flag=true;
//                          }
//                          $sql = substr($sql,0,-1);
//                        $sql.=' WHERE STUDENT_ID=\''.UserStudentID().'\'';
//                        if($flag)
//                            DBQuery($sql);
//			}
//			if($_REQUEST['sec_addr']=='Y')
//			{
//                            $sql = 'UPDATE address SET ';
//                         
//                          if($_REQUEST['values']['address']['ADDRESS'])
//                          {
//                              $sql .= 'SEC_ADDRESS'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['STREET'])
//                          {
//                              $sql .= 'SEC_STREET'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['CITY'])
//                          {
//                              $sql .= 'SEC_CITY'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['STATE'])
//                          {
//                              $sql .= 'SEC_STATE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',';
//                              $flag=true;
//                          }
//                          if($_REQUEST['values']['address']['ZIPCODE'])
//                          {
//                              $sql .= 'SEC_ZIPCODE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
//                              $flag=true;
//                          }
//                          $sql = substr($sql,0,-1);
//                        $sql.=' WHERE STUDENT_ID=\''.UserStudentID().'\'';
//                        if($flag)
//                            DBQuery($sql);
//			}
//                  }
//		}
//		else
//		{
//			/*
//			$id = DBGet(DBQuery('SELECT '.db_seq_nextval('ADDRESS_SEQ').' as SEQ_ID '.FROM_DUAL));
//			$id = $id[1]['SEQ_ID'];
//
//			$sql = "INSERT INTO address ";
//
//			$fields = 'ADDRESS_ID,STUDENT_ID,';
//			$values = "'".$id."','".UserStudentID()."',";
//			*/
//
//			$sql = 'INSERT INTO address ';
//
//			$fields = 'STUDENT_ID,';
//			$values = '\''.UserStudentID().'\',';
//
//
//######################################## For Same Mailing Address ###################################
//
//		if($_REQUEST['r4']=='Y' && $_REQUEST['r4']!='N')
//		{
//			$fields .= 'MAIL_ADDRESS,MAIL_STREET,MAIL_CITY,MAIL_STATE,MAIL_ZIPCODE,';
//			$values .= '\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
//		}
//
//######################################## For Same Mailing Address ###################################
//################################ For Same Primary  Emergency Contact ###################################
//
//		if($_REQUEST['r5']=='Y' && $_REQUEST['r5']!='N')
//		{
//			$fields .= 'PRIM_ADDRESS,PRIM_STREET,PRIM_CITY,PRIM_STATE,PRIM_ZIPCODE,';
//			$values .= '\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
//		}
//
//############################### For Same Primary  Emergency Contact ####################################
//
//############################# For Same Secondary  Emergency Contact ####################################
//
//		if($_REQUEST['r6']=='Y' && $_REQUEST['r6']!='N')
//		{
//			$fields .= 'SEC_ADDRESS,SEC_STREET,SEC_CITY,SEC_STATE,SEC_ZIPCODE,';
//			$values .= '\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
//		}
//###############################For Same Secondary  Emergency Contact ###################################
//
//			$go = 0;
//			foreach($_REQUEST['values']['address'] as $column=>$value)
//			{
//				if($value)
//				{
//					$fields .= $column.',';
//					$values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
//					$go = true;
//				}
//			}
//			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
//
//                       if($go)
//			{
//
//				DBQuery($sql);
//                               $id=DBGet(DBQuery('select max(address_id) as ADDRESS_ID  from address'));
//                               $id=$id[1]['ADDRESS_ID'];
//                               DBQuery('INSERT INTO students_join_address (STUDENT_ID,ADDRESS_ID,RESIDENCE,MAILING,BUS_PICKUP,BUS_DROPOFF) values(\''.UserStudentID().'\',\''.$id.'\',\''.$_REQUEST['values']['students_join_address']['RESIDENCE'].'\',\''.$_REQUEST['values']['students_join_address']['MAILING'].'\',\''.$_REQUEST['values']['students_join_address']['BUS_PICKUP'].'\',\''.$_REQUEST['values']['students_join_address']['BUS_DROPOFF'].'\')');
//				$_REQUEST['address_id'] = $id;
//			}
//		}
//	}
//
//	if(clean_param($_REQUEST['values']['people'],PARAM_NOTAGS))
//	{
//		if($_REQUEST['person_id']!='new')
//		{
//			$sql = 'UPDATE people SET ';
//
//			foreach($_REQUEST['values']['people'] as $column=>$value)
//			{
//                            $value=paramlib_validation($column,$value);
//                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
//			}
//			$sql = substr($sql,0,-1) . ' WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'';
//			DBQuery($sql);
//		}
//		else
//		{
//			//$id = DBGet(DBQuery('SELECT '.db_seq_nextval('PEOPLE_SEQ').' as SEQ_ID '.FROM_DUAL));
//                        $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \'people\''));
//                        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
//			$id = $id[1]['ID'];
//
//			$sql = 'INSERT INTO people ';
//
//			$fields = '';
//			$values = '';
//
//			$go = 0;
//			foreach($_REQUEST['values']['people'] as $column=>$value)
//			{
//                            $value=paramlib_validation($column,$value);
//				if($value)
//				{
//					$fields .= $column.',';
//					$values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
//					$go = true;
//				}
//			}
//			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
//			if($go)
//			{
//				DBQuery($sql);
//				DBQuery('INSERT INTO students_join_people (PERSON_ID,STUDENT_ID,ADDRESS_ID,CUSTODY,EMERGENCY) values(\''.$id.'\',\''.UserStudentID().'\',\''.$get_data['ADDRESS_ID'].'\',\''.str_replace("'","''",$_REQUEST['values']['students_join_people']['CUSTODY']).'\',\''.str_replace("'","''",$_REQUEST['values']['students_join_people']['EMERGENCY']).'\')');
//				$_REQUEST['person_id'] = $id;
//			}
//		}
//	}
//
//	if(clean_param($_REQUEST['values']['people_join_contacts'],PARAM_NOTAGS))
//	{
//		foreach($_REQUEST['values']['people_join_contacts'] as $id=>$values)
//		{
//			if($id!='new')
//			{
//				$sql = 'UPDATE people_join_contacts SET ';
//
//				foreach($values as $column=>$value)
//				{
//					$sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
//				}
//				$sql = substr($sql,0,-1) . ' WHERE ID=\''.$id.'\'';
//				DBQuery($sql);
//			}
//			else
//			{
//				if($info_apd || $values['TITLE'] && $values['TITLE']!='Example Phone' && $values['VALUE'] && $values['VALUE']!='(xxx) xxx-xxxx')
//				{
//					$sql = 'INSERT INTO people_join_contacts ';
//
//					$fields = 'PERSON_ID,';
//					$vals = '\''.$_REQUEST[person_id].'\',';
//
//					$go = 0;
//					foreach($values as $column=>$value)
//					{
//						if($value)
//						{
//							$fields .= $column.',';
//							$vals .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
//							$go = true;
//						}
//					}
//					$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($vals,0,-1) . ')';
//					if($go)
//						DBQuery($sql);
//				}
//			}
//		}
//	}
//
//	if($_REQUEST['values']['students_join_people'] && $_REQUEST['person_id']!='new')
//	{
//		$sql = 'UPDATE students_join_people SET ';
//
//		foreach($_REQUEST['values']['students_join_people'] as $column=>$value)
//		{ 
//                        $value=paramlib_validation($column,$value);
//			$sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
//		}
//		$sql = substr($sql,0,-1) . ' WHERE PERSON_ID=\''.$_REQUEST[person_id].'\' AND STUDENT_ID=\''.UserStudentID().'\'';
//		DBQuery($sql);
//	}
//
//	if($_REQUEST['values']['students_join_address'] && $_REQUEST['address_id']!='new')
//	{
//		$sql = 'UPDATE students_join_address SET ';
//
//		foreach($_REQUEST['values']['students_join_address'] as $column=>$value)
//		{
//			$sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
//		}
//		$sql = substr($sql,0,-1) . ' WHERE ADDRESS_ID=\''.$_REQUEST[address_id].'\' AND STUDENT_ID=\''.UserStudentID().'\'';
//		DBQuery($sql);
//	}
//############################Student Join People Address Same as ########################################
//if($_REQUEST['r7']=='Y' && $_REQUEST['r7']!='N' && isset($_REQUEST['person_id']))
//	{
//		$get_data = DBGet(DBQuery("SELECT ADDRESS_ID,ADDRESS,STREET,CITY,STATE,ZIPCODE,BUS_NO,BUS_PICKUP,BUS_DROPOFF FROM address WHERE STUDENT_ID='".UserStudentID()."'"));
//		$get_data = $get_data[1];
//		DBQuery('UPDATE students_join_people SET ADDN_ADDRESS=\''.str_replace("'","''",$get_data['ADDRESS']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//		DBQuery('UPDATE students_join_people SET ADDN_STREET=\''.str_replace("'","''",$get_data['STREET']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//		DBQuery('UPDATE students_join_people SET ADDN_CITY=\''.str_replace("'","''",$get_data['CITY']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//		DBQuery('UPDATE students_join_people SET ADDN_STATE=\''.str_replace("'","''",$get_data['STATE']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//		DBQuery('UPDATE students_join_people SET ADDN_ZIPCODE=\''.$get_data['ZIPCODE'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//		DBQuery('UPDATE students_join_people SET ADDN_BUS_PICKUP=\''.$get_data['BUS_PICKUP'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//		DBQuery('UPDATE students_join_people SET ADDN_BUS_DROPOFF=\''.$get_data['BUS_DROPOFF'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//		DBQuery('UPDATE students_join_people SET ADDN_BUSNO=\''.$get_data['BUS_NO'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
//	}
//############################Student Join People Address Same as ########################################
//	unset($_REQUEST['modfunc']);
//	unset($_REQUEST['values']);
//}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
    if($_REQUEST['person_id'])
    {
        if(DeletePrompt('contact'))
        {
            $tot_people=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL FROM students_join_people WHERE PERSON_ID='.$_REQUEST['person_id'].''));
            $tot_people=$tot_people[1]['TOTAL'];
            if($tot_people>1)
            {        
                           DBQuery('DELETE FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\' AND STUDENT_ID='.UserStudentID());
                           unset($_REQUEST['modfunc']);
            }
            else
            {
                DBQuery('DELETE FROM student_address WHERE PEOPLE_ID=\''.$_REQUEST['person_id'].'\' AND STUDENT_ID='.UserStudentID());
                DBQuery('DELETE FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\' AND STUDENT_ID='.UserStudentID());
                DBQuery('DELETE FROM people WHERE STAFF_ID='.$_REQUEST['person_id']);
                DBQuery('DELETE FROM login_authentication WHERE USER_ID='.$_REQUEST['person_id']);
                unset($_REQUEST['modfunc']);
            }
        }
    }
//	if($_REQUEST['contact_id'])
//	{
//		if(DeletePrompt('contact information'))
//		{
//			DBQuery('DELETE FROM people_join_contacts WHERE ID=\''.$_REQUEST[contact_id].'\'');
//			unset($_REQUEST['modfunc']);
//		}
//	}
//	elseif($_REQUEST['person_id'])
//	{
//		if(DeletePrompt('contact'))
//		{
//			DBQuery('DELETE FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\' AND STUDENT_ID=\''.UserStudentID().'\'');
//			if(count(DBGet(DBQuery('SELECT STUDENT_ID FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'')))==0)
//			{
//				DBQuery('DELETE FROM people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'');
//				DBQuery('DELETE FROM people_join_contacts WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'');
//			}
//			unset($_REQUEST['modfunc']);
//			unset($_REQUEST['person_id']);
//			if(!isset($_REQUEST['address_id']))
//			{
//				$stu_ad_id = DBGet(DBQuery('SELECT ADDRESS_ID FROM address WHERE STUDENT_ID=\''.UserStudentID().'\''));
//				$stu_ad_id = $stu_ad_id[1]['ADDRESS_ID'];
//				if(count($stu_ad_id))
//					$_REQUEST['address_id']=$stu_ad_id;
//				else
//					$_REQUEST['address_id']='new';
//			}
//		}
//	}
//	elseif($_REQUEST['address_id'])
//	{
//		if(DeletePrompt('address'))
//		{
//			DBQuery('UPDATE students_join_people SET ADDRESS_ID=\'0\' WHERE STUDENT_ID=\''.UserStudentID().'\' AND ADDRESS_ID=\''.$_REQUEST[address_id].'\'');
//			DBQuery('DELETE FROM students_join_address WHERE STUDENT_ID=\''.UserStudentID().'\' AND ADDRESS_ID=\''.$_REQUEST['address_id'].'\'');
//			if(count(DBGet(DBQuery('SELECT STUDENT_ID FROM students_join_address WHERE ADDRESS_ID=\''.$_REQUEST['address_id'].'\'')))==0)
//				DBQuery('DELETE FROM address WHERE ADDRESS_ID=\''.$_REQUEST['address_id'].'\'');
//			unset($_REQUEST['modfunc']);
//			$_REQUEST['address_id']='new';
//		}
//	}
}

if(!$_REQUEST['modfunc'])
{
    $addres_id=DBGet(DBQuery('SELECT ID AS ADDRESS_ID FROM student_address WHERE STUDENT_ID=\''.UserStudentID().'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND TYPE=\'Home Address\' '));
    if(count($addres_id)==1 && $addres_id[1]['ADDRESS_ID']!='')
    $_REQUEST['address_id'] = $addres_id[1]['ADDRESS_ID'];    
//    $addresses_RET = DBGet(DBQuery('SELECT a.ADDRESS_ID, sjp.STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.BUS_NO,a.BUS_PICKUP,a.BUS_DROPOFF,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,a.PRIM_STUDENT_RELATION,a.PRI_FIRST_NAME,a.PRI_LAST_NAME,a.HOME_PHONE,a.WORK_PHONE,a.MOBILE_PHONE,a.EMAIL,a.PRIM_CUSTODY,a.PRIM_ADDRESS,a.PRIM_STREET,a.PRIM_CITY,a.PRIM_STATE,a.PRIM_ZIPCODE,a.SEC_STUDENT_RELATION,a.SEC_FIRST_NAME,a.SEC_LAST_NAME,a.SEC_HOME_PHONE,a.SEC_WORK_PHONE,a.SEC_MOBILE_PHONE,a.SEC_EMAIL,a.SEC_CUSTODY,a.SEC_ADDRESS,a.SEC_STREET,a.SEC_CITY,a.SEC_STATE,a.SEC_ZIPCODE,  sjp.CUSTODY,sja.MAILING,sja.RESIDENCE FROM address a,students_join_address sja,students_join_people sjp WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=\''.UserStudentID().'\' AND a.ADDRESS_ID=sjp.ADDRESS_ID AND sjp.STUDENT_ID=sja.STUDENT_ID' .
//				  ' UNION SELECT a.ADDRESS_ID,\'\' AS STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.BUS_NO,a.BUS_PICKUP,a.BUS_DROPOFF,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,a.PRIM_STUDENT_RELATION,a.PRI_FIRST_NAME,a.PRI_LAST_NAME,a.HOME_PHONE,a.WORK_PHONE,a.MOBILE_PHONE,a.EMAIL,a.PRIM_CUSTODY,a.PRIM_ADDRESS,a.PRIM_STREET,a.PRIM_CITY,a.PRIM_STATE,a.PRIM_ZIPCODE,a.SEC_STUDENT_RELATION,a.SEC_FIRST_NAME,a.SEC_LAST_NAME,a.SEC_HOME_PHONE,a.SEC_WORK_PHONE,a.SEC_MOBILE_PHONE,a.SEC_EMAIL,a.SEC_CUSTODY,a.SEC_ADDRESS,a.SEC_STREET,a.SEC_CITY,a.SEC_STATE,a.SEC_ZIPCODE,a.PRIM_CUSTODY AS CUSTODY,sja.MAILING,sja.RESIDENCE FROM address a,students_join_address sja WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=\''.UserStudentID().'\' AND NOT EXISTS (SELECT \'\' FROM students_join_people sjp WHERE sjp.STUDENT_ID=sja.STUDENT_ID AND sjp.ADDRESS_ID=a.ADDRESS_ID) ORDER BY CUSTODY ASC,STUDENT_RELATION'),array(),array('ADDRESS_ID'));
//	if(count($addresses_RET)==1 && $_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='old' && $_REQUEST['address_id']!='0')
//		$_REQUEST['address_id'] = key($addresses_RET);

	echo '<TABLE border=0><TR><TD valign=top>'; // table 1
	echo '<TABLE border=0><TR><TD valign=top>'; // table 2
	echo '<TABLE border=0 cellpadding=0 cellspacing=0>'; // table 3
//	if(count($addresses_RET)>0 || $_REQUEST['address_id']=='new' || $_REQUEST['address_id']=='0')
//	{
//		$i = 1;
//		if(!isset($_REQUEST['address_id']))
//			$_REQUEST['address_id'] = key($addresses_RET);
//
//		if(count($addresses_RET))
//		{
//			foreach($addresses_RET as $address_id=>$addresses)
//			{
//				echo '<TR>';
//
//				// find other students associated with this address
//				$xstudents = DBGet(DBQuery('SELECT s.STUDENT_ID,CONCAT(s.FIRST_NAME,\' \',s.LAST_NAME) AS FULL_NAME,RESIDENCE,BUS_PICKUP,BUS_DROPOFF,MAILING FROM students s,students_join_address sja WHERE s.STUDENT_ID=sja.STUDENT_ID AND sja.ADDRESS_ID=\''.$address_id.'\' AND sja.STUDENT_ID!=\''.UserStudentID().'\''));
//				if(count($xstudents))
//				{
//					$warning = 'Other students associated with this address:<BR>';
//					foreach($xstudents as $xstudent)
//					{
//						$ximages = '';
//						if($xstudent['RESIDENCE']=='Y')
//							$ximages .= ' <IMG SRC=assets/house_button.gif>';
//						if($xstudent['BUS_PICKUP']=='Y' || $xstudent['BUS_DROPOFF']=='Y')
//							$ximages .= ' <IMG SRC=assets/bus_button.gif>';
//						if($xstudent['MAILING']=='Y')
//							$ximages .= ' <IMG SRC=assets/mailbox_button.gif>';
//						$warning .= '<b>'.str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['FULL_NAME']).'</b>'.$ximages.'<BR>';
//					}
//					echo '<TD>'.button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'').'</TD>';
//				}
//				else
//					echo '<TD></TD>';
//
//				$relation_list = '';
//				foreach($addresses as $address)
//					$relation_list .= ($address['STUDENT_RELATION']&&strpos($address['STUDENT_RELATION'].', ',$relation_list)==false?$address['STUDENT_RELATION']:'---').', ';
//				$address = $addresses[1];
//				$relation_list = substr($relation_list,0,-2);
//
//				$images = '';
//				if($address['RESIDENCE']=='Y')
//					#$images .= ' <IMG SRC=assets/house_button.gif>';
//				if($address['BUS_PICKUP']=='Y' || $address['BUS_DROPOFF']=='Y')
//					#$images .= ' <IMG SRC=assets/bus_button.gif>';
//				if($address['MAILING']=='Y')
//					#$images .= ' <IMG SRC=assets/mailbox_button.gif>';
//				echo '<TD colspan=2 style="border:0; border-style: none none solid none;"><B>'.$relation_list.'</B>'.($relation_list&&$images?'<BR>':'').$images.'</TD>';
//
//				echo '</TR>';
//
//				$style = '';
//				if($i!=count($addresses_RET))
//					$style = ' style="border:1; border-style: none none dashed none;"';
//				elseif($i!=1)
//					$style = ' style="border:1; border-style: dashed none none none;"';
//				$style .= ' ';
//
//				if($address_id==$_REQUEST['address_id'] && $_REQUEST['address_id']!='0' && $_REQUEST['address_id']!='new')
//					$this_address = $address;
//
//				$i++;
//				$link = 'onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$address['ADDRESS_ID'].'\';"';
//				echo '</TD>';
//				echo '<TD></TD>';
//				echo '</TR>';
//			}
//			echo '<TR><TD colspan=3 height=40></TD></TR>';
//		}
//	}
//	else
		echo '';
		
	############################################################################################
		
		$style = '';
		if($_REQUEST['person_id']=='new')
		{
			if($_REQUEST['address_id']!='new')
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'\';" ><TD>';
			else
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=new\';" ><TD>';
			echo '<A style="cursor:pointer"><b>Student\'s Address </b></A>';
		}
		else
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=$_REQUEST[address_id]\';" onmouseover=\'this.style.color="white";\'><TD>';
			if($_REQUEST['person_id']==$contact['PERSON_ID'])
			echo '<A style="cursor:pointer;color:#FF0000"><b>Student\'s Address </b></A>';
			elseif($_REQUEST['person_id']!=$contact['PERSON_ID'])
			echo '<A style="cursor:pointer"><b>Student\'s Address </b></A>';
			else
			echo '<A style="cursor:pointer;color:#FF0000"><b>Student\'s Address </b></A>';
		}
		echo '</TD>';
		echo '<TD><A><IMG SRC=assets/arrow_right.gif></A></TD>';
		echo '</TR><tr><td colspan=2 class=break></td></tr>';
			
			
//			$contacts_RET = DBGet(DBQuery('SELECT p.PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.ADDN_HOME_PHONE,sjp.ADDN_WORK_PHONE,sjp.ADDN_MOBILE_PHONE,sjp.ADDN_EMAIL,sjp.CUSTODY,sjp.ADDN_ADDRESS,sjp.ADDN_BUS_PICKUP,sjp.ADDN_BUS_DROPOFF,sjp.ADDN_BUSNO,sjp.ADDN_STREET,sjp.ADDN_CITY,sjp.ADDN_STATE,sjp.ADDN_ZIPCODE,sjp.EMERGENCY,sjp.STUDENT_RELATION FROM people p,students_join_people sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID=\''.UserStudentID().'\' ORDER BY sjp.STUDENT_RELATION'));
                        $contacts_RET = DBGet(DBQuery('SELECT PERSON_ID,RELATIONSHIP AS STUDENT_RELATION FROM students_join_people WHERE STUDENT_ID=\''.UserStudentID().'\' AND EMERGENCY_TYPE=\'Other\' ORDER BY STUDENT_RELATION'));
			$i = 1;
			if(count($contacts_RET))
			{
				foreach($contacts_RET as $contact)
				{
					$THIS_RET = $contact;
//					if($contact['PERSON_ID']==$_REQUEST['person_id'])
//						$this_contact = $contact;
					$style .= ' ';

					$i++;
					$link = 'onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id='.$contact['PERSON_ID'].'&con_info=old\';"';
					if(AllowEdit())
						$remove_button = button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$contact[PERSON_ID]",20);
					else
						$remove_button = '';
					if($_REQUEST['person_id']==$contact['PERSON_ID'])
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.'>';
					else
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.' style=white-space:nowrap>';

					$images = '';

					// find other students associated with this person
//					$xstudents = DBGet(DBQuery('SELECT s.STUDENT_ID,CONCAT(s.FIRST_NAME,\' \',s.LAST_NAME) AS FULL_NAME,STUDENT_RELATION,CUSTODY,EMERGENCY FROM students s,students_join_people sjp WHERE s.STUDENT_ID=sjp.STUDENT_ID AND sjp.PERSON_ID=\''.$contact['PERSON_ID'].'\' AND sjp.STUDENT_ID!=\''.UserStudentID().'\''));
//					if(count($xstudents))
//					{
//						$warning = 'Other students associated with this person:<BR>';
//						foreach($xstudents as $xstudent)
//						{
//							$ximages = '';
//							if($xstudent['CUSTODY']=='Y')
//								$ximages .= ' <IMG SRC=assets/gavel_button.gif>';
//							if($xstudent['EMERGENCY']=='Y')
//								$ximages .= ' <IMG SRC=assets/emergency_button.gif>';
//							$warning .= '<b>'.str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['FULL_NAME']).'</b> ('.($xstudent['STUDENT_RELATION']?str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['STUDENT_RELATION']):'---').')'.$ximages.'<BR>';
//						}
//						$images .= ' '.button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
//					}

					if($contact['CUSTODY']=='Y')
						$images .= ' <IMG SRC=assets/gavel_button.gif>';
					if($contact['EMERGENCY']=='Y')
						$images .= ' <IMG SRC=assets/emergency_button.gif>';
if ($_REQUEST['person_id']==$contact['PERSON_ID']) {
					echo '<A style="cursor:pointer; font-weight:bold;color:#ff0000" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					} else {
					echo '<A style="cursor:pointer; font-weight:bold;" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					}
					echo '</TD>';
					echo '<TD valign=middle align=right> &nbsp; <A style="cursor: pointer;"><IMG SRC=assets/arrow_right.gif></A></TD>';
					echo '</TR></table></td></tr>';
				}
			}
	############################################################################################	
	
	// New Address
	if(AllowEdit())
	{
		if($_REQUEST['address_id']!=='new' && $_REQUEST['address_id']!=='old')
		{

			echo '<TABLE width=100%><TR><TD>';
			if($_REQUEST['address_id']==0)
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			else
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			// New Contact
			if(AllowEdit())
			{
				$style = 'class=break';
			}

			echo '</TABLE>';
		}

		if(clean_param($_REQUEST['person_id'],PARAM_ALPHAMOD)=='new')
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
			echo '<A style="cursor: pointer;color:#FF0000"><b>Add New Contact</b></A>';
		}
		else
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
			echo '<A style="cursor: pointer;"><b>Add New Contact</b></A>';
		}
		echo '</TD>';
		echo '<TD><IMG SRC=assets/arrow_right.gif></TD>';
		echo '</TR>';

	}
	echo '</TABLE>';
	echo '</TD>';
	echo '<TD class=vbreak>&nbsp;</TD><TD valign=top>';

	if(isset($_REQUEST['address_id']) && $_REQUEST['con_info']!='old')
	{
            $h_addr=DBGet(DBQuery(' SELECT sa.ID AS ADDRESS_ID,sa.ADDRESS,sa.STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from student_address sa WHERE 
                                   sa.TYPE=\'Home Address\' AND sa.STUDENT_ID=\''.  UserStudentID().'\' AND sa.SCHOOL_ID=\''.  UserSchool().'\' '));
            
            $pri_par_id=  DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().' AND EMERGENCY_TYPE=\'Primary\''));
            if(count($pri_par_id)>0)
            {
               $p_addr=DBGet(DBQuery('SELECT p.STAFF_ID as CONTACT_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,p.HOME_PHONE,p.WORK_PHONE,p.CELL_PHONE,p.EMAIL,p.CUSTODY,
                                  sa.ID AS ADDRESS_ID,sa.ADDRESS,sa.STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from people p,student_address sa WHERE p.STAFF_ID=sa.PEOPLE_ID  AND p.STAFF_ID=\''.$pri_par_id[1]['PERSON_ID'].'\'  AND sa.PEOPLE_ID IS NOT NULL '));
               $p_addr[1]['RELATIONSHIP']=$pri_par_id[1]['RELATIONSHIP'];
               $p_log_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$pri_par_id[1]['PERSON_ID'].'\' AND PROFILE_ID=4'));
               $p_addr[1]['USER_NAME']=$p_log_addr[1]['USER_NAME'];
               $p_addr[1]['PASSWORD']=$p_log_addr[1]['PASSWORD'];
            }
            $m_addr=DBGet(DBQuery(' SELECT sa.ID AS ADDRESS_ID,sa.ADDRESS,sa.STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from student_address sa WHERE 
                                   sa.TYPE=\'Mail\' AND sa.STUDENT_ID=\''.  UserStudentID().'\'  AND sa.SYEAR=\''.UserSyear().'\' AND sa.SCHOOL_ID=\''.  UserSchool().'\' '));
            $sec_par_id=  DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().' AND EMERGENCY_TYPE=\'Secondary\''));
            if(count($sec_par_id)>0)
            {   
                $s_addr=DBGet(DBQuery('SELECT p.STAFF_ID as CONTACT_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,p.HOME_PHONE,p.WORK_PHONE,p.CELL_PHONE,p.EMAIL,p.CUSTODY,
                                  sa.ID AS ADDRESS_ID,sa.ADDRESS,sa.STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from people p,student_address sa WHERE p.STAFF_ID=sa.PEOPLE_ID  AND p.STAFF_ID=\''.$sec_par_id[1]['PERSON_ID'].'\'  AND sa.PEOPLE_ID IS NOT NULL '));                 
                $s_addr[1]['RELATIONSHIP']=$sec_par_id[1]['RELATIONSHIP'];
                $p_log_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$sec_par_id[1]['PERSON_ID'].'\' AND PROFILE_ID=4'));
               $s_addr[1]['USER_NAME']=$p_log_addr[1]['USER_NAME'];
               $s_addr[1]['PASSWORD']=$p_log_addr[1]['PASSWORD'];
//                $s_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$pri_par_id[1]['PERSON_ID'].'\' AND PROFILE_ID=3'));
            }
//            $pri_stf_id=  DBGet(DBQuery('SELECT st.STAFF_ID AS STAFF_ID FROM staff st ,students_join_users sju  WHERE st.STAFF_ID=sju.STAFF_ID AND sju.STUDENT_ID=\''.UserStudentID().'\' AND st.FIRST_NAME=\''.$p_addr[1]['FIRST_NAME'].'\' AND st.LAST_NAME=\''.$p_addr[1]['LAST_NAME'].'\' AND '.($p_addr[1]['HOME_PHONE']!=''?'st.PHONE=\''.$p_addr[1]['HOME_PHONE'].'\'':'st.PHONE IS NULL').'  AND  '.($p_addr[1]['EMAIL']!=''?'st.EMAIL=\''.$p_addr[1]['EMAIL'].'\'':'st.EMAIL IS NULL').''));
//            $sec_stf_id=  DBGet(DBQuery('SELECT st.STAFF_ID AS STAFF_ID FROM staff st ,students_join_users sju  WHERE st.STAFF_ID=sju.STAFF_ID AND sju.STUDENT_ID=\''.UserStudentID().'\' AND st.FIRST_NAME=\''.$s_addr[1]['FIRST_NAME'].'\' AND st.LAST_NAME=\''.$s_addr[1]['LAST_NAME'].'\' AND '.($s_addr[1]['HOME_PHONE']!=''?'st.PHONE=\''.$s_addr[1]['HOME_PHONE'].'\'':'st.PHONE IS NULL').'  AND  '.($s_addr[1]['EMAIL']!=''?'st.EMAIL=\''.$s_addr[1]['EMAIL'].'\'':'st.EMAIL IS NULL').''));
            echo "<INPUT type=hidden name=address_id value=$_REQUEST[address_id]>";

		if($_REQUEST['address_id']!='0' && $_REQUEST['address_id']!=='old')
		{
//			$query='SELECT ADDRESS_ID FROM students_join_address WHERE STUDENT_ID=\''.UserStudentID().'\'';
//			$a_ID=DBGet(DBQuery($query));
//			$a_ID=$a_ID[1]['ADDRESS_ID'];
                        
			
//			if($a_ID==0)
//				$size = true;
//			else
//				$size = false;             
//                      $city_options = _makeAutoSelect('CITY','address',array(array('CITY'=>$this_address['CITY']),array('CITY'=>$this_address['MAIL_CITY'])),$city_options);
//			$state_options = _makeAutoSelect('STATE','address',array(array('STATE'=>$this_address['STATE']),array('STATE'=>$this_address['MAIL_STATE'])),$state_options);
//			$zip_options = _makeAutoSelect('ZIPCODE','address',array(array('ZIPCODE'=>$this_address['ZIPCODE']),array('ZIPCODE'=>$this_address['MAIL_ZIPCODE'])),$zip_options);
//                        
                        if($h_addr[1]['ADDRESS_ID']==0)
				$size = true;
			else
				$size = false;

			$city_options = _makeAutoSelect('CITY','student_address','',array(array('CITY'=>$h_addr[1]['CITY']),array('CITY'=>$h_addr[1]['CITY'])),$city_options);
			$state_options = _makeAutoSelect('STATE','student_address','',array(array('STATE'=>$h_addr[1]['STATE']),array('STATE'=>$h_addr[1]['STATE'])),$state_options);
			$zip_options = _makeAutoSelect('ZIPCODE','student_address','',array(array('ZIPCODE'=>$h_addr[1]['ZIPCODE']),array('ZIPCODE'=>$h_addr[1]['ZIPCODE'])),$zip_options);
                        
                        

                        if($h_addr[1]['BUS_PICKUP']=='N')
                            unset($h_addr[1]['BUS_PICKUP']);
                        if($h_addr[1]['BUS_DROPOFF']=='N')
                            unset($h_addr[1]['BUS_DROPOFF']);
                        if($h_addr[1]['BUS_NO']=='N')
                            unset($h_addr[1]['BUS_NO']);
                         if($p_addr[1]['CUSTODY']=='N')
                            unset($p_addr[1]['CUSTODY']);
                        if($s_addr[1]['CUSTODY']=='N')
                            unset($s_addr[1]['CUSTODY']);
                        
                        //hidden fields//
                        if($h_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][HOME][ID]" id=pri_person_id value='.$h_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][HOME][ID]" id=pri_person_id value=new />';    
                        
                        if($m_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][MAIL][ID]" value='.$m_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][MAIL][ID]" value=new />';
                        
                        if($s_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][SECONDARY][ID]" value='.$s_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][SECONDARY][ID]" value=new />';    
                        
                        
                        if($p_addr[1]['ADDRESS_ID']!='')
                        echo '<input type=hidden name="values[student_address][PRIMARY][ID]" value='.$p_addr[1]['ADDRESS_ID'].' />';
                        else
                        echo '<input type=hidden name="values[student_address][PRIMARY][ID]" value=new />';
                        
                        echo '<br>';
                        
                        
                        if($p_addr[1]['CONTACT_ID']!='')
                        echo '<input type=hidden name="values[people][PRIMARY][ID]" value='.$p_addr[1]['CONTACT_ID'].' />';
                        else
                        echo '<input type=hidden name="values[people][PRIMARY][ID]" value=new />';
                        
                        if($s_addr[1]['CONTACT_ID']!='')
                        echo '<input type=hidden name="values[people][SECONDARY][ID]" value='.$s_addr[1]['CONTACT_ID'].' />';
                        else
                        echo '<input type=hidden name="values[people][SECONDARY][ID]" value=new />';
                        
			echo '<TABLE width=100%><TR><TD>'; // open 3a
			echo '<FIELDSET><LEGEND><FONT color=gray>Student\'s Home Address</FONT></LEGEND><TABLE width=100%>';
			echo '<TR><td><span class=red>*</span>Address Line 1</td><td>:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'.TextInput($h_addr[1]['ADDRESS'],'values[student_address][HOME][ADDRESS]','','class=cell_medium').'</td><td>';
//			if($_REQUEST['address_id']!='0')
//			{
//				$display_address = urlencode($this_address['ADDRESS'].', '.($this_address['CITY']?' '.$this_address['CITY'].', ':'').$this_address['STATE'].($this_address['ZIPCODE']?' '.$this_address['ZIPCODE']:''));
//				$link = 'http://google.com/maps?q='.$display_address;
//				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
//			}
                        if($h_addr[1]['ADDRESS_ID']!='0')
			{
				$display_address = urlencode($h_addr[1]['ADDRESS'].', '.($h_addr[1]['CITY']?' '.$h_addr[1]['CITY'].', ':'').$h_addr[1]['STATE'].($h_addr[1]['ZIPCODE']?' '.$h_addr[1]['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></TD></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($h_addr[1]['STREET'],'values[student_address][HOME][STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>City</td><td>:</td><TD>'.TextInput($h_addr[1]['CITY'],'values[student_address][HOME][CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>State</td><td>:</td><TD>'.TextInput($h_addr[1]['STATE'],'values[student_address][HOME][STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>*</span>Zip/Postal Code</td><td>:</td><TD>'.TextInput($h_addr[1]['ZIPCODE'],'values[student_address][HOME][ZIPCODE]','','class=cell_medium').'</TD></tr>';
			echo '<tr><TD>School Bus Pick-up</td><td>:</td><td>'.CheckboxInputMod($h_addr[1]['BUS_PICKUP'],'values[student_address][HOME][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><TD>School Bus Drop-off</td><td>:</td><td>'.CheckboxInputMod($h_addr[1]['BUS_DROPOFF'],'values[student_address][HOME][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><td>Bus No</td><td>:</td><td>'.TextInput($h_addr[1]['BUS_NO'],'values[student_address][HOME][BUS_NO]','','class=cell_small').'</TD></tr>';
			echo '</TABLE></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; //close 3a

			//if($_REQUEST['address_id']=='new')
//			if($a_ID==0)
//			{
//				$new = true;
//				$this_address['RESIDENCE'] = 'Y';
//				$this_address['MAILING'] = 'Y';
//				if($use_bus)
//				{
//					$this_address['BUS_PICKUP'] = 'Y';
//					$this_address['BUS_DROPOFF'] = 'Y';
//										
//				}
//			}
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3b
			echo '<FIELDSET><LEGEND><FONT color=gray>Student\'s Mailing Address</FONT></LEGEND>';
			
/*			$query="SELECT ADDRESS_ID FROM students_join_address WHERE STUDENT_ID='".UserStudentID()."'";
			$a_ID=DBGet(DBQuery($query));
			$a_ID=$a_ID[1]['ADDRESS_ID'];
*/			//if($_REQUEST['address_id']=='new')
//                        $s_mail_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM address WHERE ADDRESS_ID=\''.$a_ID.'\' AND ADDRESS=PRIM_ADDRESS AND MAIL_CITY=CITY AND MAIL_STATE=STATE AND MAIL_ZIPCODE=ZIPCODE'));
//                        if($s_mail_address[1]['TOTAL']!=0)
//                           $m_checked=" CHECKED=CHECKED ";
//                        else
//                            $m_checked=" ";
//                        if($a_ID!=0)
//                            echo '<div id="check_addr"><input type="checkbox" '.$m_checked.' id="same_addr" name="same_addr" value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
//			if($a_ID==0)
//			echo '<table><TR><TD><span class=red>*</span><input type="radio" id="r4" name="r4" value="Y" onClick="hidediv();" checked>&nbsp;Same as Home Address &nbsp;&nbsp; <input type="radio" id="r4" name="r4" value="N" onClick="showdiv();">&nbsp;Add New Address</TD></TR></TABLE>'; 
//                        
                        
                        
                        if($m_addr[1]['ADDRESS_ID']!='' && $h_addr[1]['ADDRESS_ID']!='')
                        {    
                        $s_mail_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM student_address WHERE ID!=\''.$m_addr[1]['ADDRESS_ID'].'\' AND ADDRESS=\''.str_replace("'","\'",$m_addr[1]['ADDRESS']).'\' AND CITY=\''.str_replace("'","\'",$m_addr[1]['CITY']).'\' AND STATE=\''.str_replace("'","\'",$m_addr[1]['STATE']).'\' AND ZIPCODE=\''.$m_addr[1]['ZIPCODE'].'\' AND TYPE=\'Home Address\' '));
                        if($s_mail_address[1]['TOTAL']!=0)
                           $m_checked=" CHECKED=CHECKED ";
                        else
                            $m_checked=" ";
                        }
                        
                        if($h_addr[1]['ADDRESS_ID']!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$m_checked.' id="same_addr" name="same_addr" set_check_value value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<table><TR><TD><span class=red>*</span><input type="radio" id="r4" name="r4" value="Y" onClick="hidediv();" checked>&nbsp;Same as Home Address &nbsp;&nbsp; <input type="radio" id="r4" name="r4" value="N" onClick="showdiv();">&nbsp;Add New Address</TD></TR></TABLE>'; 
			//if($_REQUEST['address_id']=='new')
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<div id="hideShow" style="display:none">';
			else
			echo '<div id="hideShow">';
			echo '<TABLE>';
			echo '<TR><td style=width:120px>Address Line 1</td><td>:</td><TD>'.TextInput($m_addr[1]['ADDRESS'],'values[student_address][MAIL][ADDRESS]','','class=cell_medium').'</TD>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($m_addr[1]['STREET'],'values[student_address][MAIL][STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($m_addr[1]['CITY'],'values[student_address][MAIL][CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($m_addr[1]['STATE'],'values[student_address][MAIL][STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($m_addr[1]['ZIPCODE'],'values[student_address][MAIL][ZIPCODE]','','class=cell_medium').'</TD></tr>';
			
			echo '</TABLE>';
			echo '</div>';

			echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3b
			
			
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3c
			echo '<FIELDSET><LEGEND><FONT color=gray>Primary Emergency Contact</FONT></LEGEND><TABLE width=100%><tr><td>';
			echo '<table border=0 width=100%>';
                       
                        $prim_relation_options = _makeAutoSelect('RELATIONSHIP','students_join_people','PRIMARY',$p_addr['RELATIONSHIP'],$relation_options);
//			echo '<tr><td style=width:120px><span class=red>*</span>Relationship to Student</TD><td>:</td><td><table><tr><td>'._makeAutoSelectInputX($p_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','PRIMARY','',$prim_relation_options).'</td><td>';if($p_addr[1]['CONTACT_ID']=='') echo '<input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'for_window.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=primary&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;">';echo '</td></tr></table></TD></tr>';
                         if(User('PROFILE')!='teacher')
                          echo '<tr><td style=width:120px><span class=red>*</span>Relationship to Student</TD><td>:</td><td><table><tr><td>'._makeAutoSelectInputX($p_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','PRIMARY','',$prim_relation_options).'</td><td><input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'for_window.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=primary&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;"></td></tr></table></TD></tr>';
                        
                            echo '<TR><td><span class=red>*</span>First Name</td><td>:</td><TD>'.TextInput($p_addr[1]['FIRST_NAME'],'values[people][PRIMARY][FIRST_NAME]','','id=pri_fname class=cell_medium').'</TD></tr>';
                            echo '<TR><td><span class=red>*</span>Last Name</td><td>:</td><TD>'.TextInput($p_addr[1]['LAST_NAME'],'values[people][PRIMARY][LAST_NAME]','','id=pri_lname class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Home Phone</td><td>:</td><TD>'.TextInput($p_addr[1]['HOME_PHONE'],'values[people][PRIMARY][HOME_PHONE]','','id=pri_hphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Work Phone</td><td>:</td><TD>'.TextInput($p_addr[1]['WORK_PHONE'],'values[people][PRIMARY][WORK_PHONE]','','id=pri_wphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Cell/Mobile Phone</td><td>:</td><TD>'.TextInput($p_addr[1]['CELL_PHONE'],'values[people][PRIMARY][CELL_PHONE]','','id=pri_cphone class=cell_medium').'</TD></tr>';
                            if($p_addr[1]['CONTACT_ID']=='')
                            //echo '<TR><td><span class=red>*</span>Email</td><td>:</td><TD><table><tr><td>'.TextInput($p_addr[1]['EMAIL'],'values[people][PRIMARY][EMAIL]','','autocomplete=off id=pri_email class=cell_medium onkeyup=peoplecheck_email(this,1,0) ').'</td><td> <span id="email_1"></span></td></tr></table></TD></tr>';
                                 echo '<TR><td><span class=red>*</span>Emaill</td><td>:</td><td>'.TextInput($p_addr[1]['EMAIL'],'values[people][PRIMARY][EMAIL]','','autocomplete=off id=pri_email class=cell_medium onkeyup=peoplecheck_email(this,1,0) ').'</td><td> <span id="email_1"></span></td></tr></tr>';
                            else
                            echo '<TR><td><span class=red>*</span>Email</td><td>:</td><TD><table><tr><td>'.TextInput($p_addr[1]['EMAIL'],'values[people][PRIMARY][EMAIL]','','autocomplete=off id=pri_email class=cell_medium onkeyup=peoplecheck_email(this,1,'.$p_addr[1]['CONTACT_ID'].') ').'</td><td> <span id="email_1"></span></td></tr></table></TD></tr>';    
                            echo '<TR><TD>Custody of Student</TD><td>:</td><TD>'.CheckboxInputMod($p_addr[1]['CUSTODY'],'values[people][PRIMARY][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';
                        
                           
                            if($p_addr[1]['USER_NAME']=='')
                            {    
                            $portal_check='';    
                            $style='style="display:none"';
                            }
                            else
                            {
                            $portal_check='checked="checked"';
                            $style='';
                            }
                            echo '<input type="hidden" id=pri_val_pass value="Y">';
                            echo '<input type="hidden" id=pri_val_user value="Y">';
                            echo '<input type="hidden" id=val_email_1 name=val_email_1 value="Y">';
                            if($portal_check=='')
//                                echo '<TR><TD>Portal User</TD><td>:</td><TD><input type="checkbox" name="primary_portal" value="Y" id="portal_1" onClick="portal_toggle(1);" '.$portal_check.'/></TD></TR>';   
                                echo '<tr><td>Portal User</td><td>:</td><td><input type="checkbox" width="25" name="primary_portal" value="Y" id="portal_1" onClick="portal_toggle(1);" '.$portal_check.'/></td></tr>';   
                            else
                                echo '<TR><TD>Portal User</TD><td>:</td><TD><div id=checked_1><IMG SRC=assets/check.gif width=15></div></TD></TR>';   
                                echo '<tr><td colspan=3><div id="portal_div_1" '.$style.'><TABLE>';
                            if($p_addr[1]['USER_NAME']=='' && $p_addr[1]['PASSWORD']=='')
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($p_addr[1]['USER_NAME'],'values[people][PRIMARY][USER_NAME]','','id=primary_username class=cell_medium onblur="usercheck_init_mod(this,1)" ').'<div id="ajax_output_1"></div></TD></TR>';   
                                echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($p_addr[1]['PASSWORD'],'values[people][PRIMARY][PASSWORD]','','id=primary_password class=cell_medium onkeyup="passwordStrengthMod(this.value,1);" onblur="validate_password_mod(this.value,1);"').'<span id="passwordStrength1"></span></TD></TR>';   
                            }
                            else
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD><div id=uname1>'.$p_addr[1]['USER_NAME'].'</div></TD></TR>';
                                echo '<TR><TD>Password</TD><td>:</td><TD><div id=pwd1>'.str_repeat('*',strlen($p_addr[1]['PASSWORD'])).'</div></TD></TR>';
                            }
                        
                        echo '</TABLE></td></tr></div>';
                        echo '<tr><td colspan=3><div id="portal_hidden_div_1" ><TABLE>';
                        echo '</TABLE></td></tr></div>';
			//if($_REQUEST['address_id']=='new')
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><table><TR><TD><TD><span class=red>*</span><input type="radio" id="rps" name="r5" value="Y" onClick="prim_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="rpn" name="r5" value="N" onClick="prim_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></td></tr>'; 
			//if($_REQUEST['address_id']=='new')
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><div id="prim_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=5><div id="prim_hideShow">';
			echo '<div class=break></div>';
                        
//                        $s_prim_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM address WHERE ADDRESS_ID=\''.$a_ID.'\' AND ADDRESS=PRIM_ADDRESS AND CITY=PRIM_CITY AND STATE=PRIM_STATE AND ZIPCODE=PRIM_ZIPCODE'));
//                        if($s_prim_address[1]['TOTAL']!=0)
//                           $p_checked=" CHECKED=CHECKED ";
//                        else
//                            $p_checked=" ";
//                         if($a_ID!=0)
//                            echo '<div id="check_addr"><input type="checkbox" '.$p_checked.' id="prim_addr" name="prim_addr" value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
//                         
                        if($h_addr[1]['ADDRESS_ID']!='' && $p_addr[1]['ADDRESS_ID']!='')
                        {
                        $s_prim_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM student_address WHERE ID!=\''.$p_addr[1]['ADDRESS_ID'].'\' AND ADDRESS=\''.str_replace("'","\'",$p_addr[1]['ADDRESS']).'\' AND CITY=\''.str_replace("'","\'",$p_addr[1]['CITY']).'\' AND STATE=\''.str_replace("'","\'",$p_addr[1]['STATE']).'\' AND ZIPCODE=\''.$p_addr[1]['ZIPCODE'].'\' AND TYPE=\'Home Address\' '));
                        if($s_prim_address[1]['TOTAL']!=0)
                           $p_checked=" CHECKED=CHECKED ";
                        else
                            $p_checked=" ";
                         if($p_addr[1]['ADDRESS_ID']!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$p_checked.' id="prim_addr" name="prim_addr" value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
                        }
			echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($p_addr[1]['ADDRESS'],'values[student_address][PRIMARY][ADDRESS]','','id=pri_address class=cell_medium').'</TD><td>';
			//if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
			if($p_addr[1]['ADDRESS_ID']!=0)
			{
				$display_address = urlencode($p_addr[1]['ADDRESS'].', '.($p_addr[1]['CITY']?' '.$p_addr[1]['CITY'].', ':'').$p_addr[1]['STATE'].($p_addr[1]['ZIPCODE']?' '.$p_addr[1]['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($p_addr[1]['STREET'],'values[student_address][PRIMARY][STREET]','','id=pri_street class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($p_addr[1]['CITY'],'values[student_address][PRIMARY][CITY]','','id=pri_city class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($p_addr[1]['STATE'],'values[student_address][PRIMARY][STATE]','','id=pri_state class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($p_addr[1]['ZIPCODE'],'values[student_address][PRIMARY][ZIPCODE]','','id=pri_zip class=cell_medium').'</TD>';
			echo '</table>';
			echo '</div></td></tr>';

			echo '</table></td></tr></table></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3c
			
############################################################################################		
			echo '<TABLE border=0 width=100%><TR><TD>'; // open 3d
			echo '<FIELDSET><LEGEND><FONT color=gray>Secondary Emergency Contact</FONT></LEGEND><TABLE width=100%><tr><td>';
                        echo '<table border=0 width=100%>';
			$sec_relation_options = _makeAutoSelect('RELATIONSHIP','students_join_people','SECONDARY',$s_addr[1]['RELATIONSHIP'],$relation_options);
//			echo '<table><tr><td style=width:120px><span class=red>*</span>Relationship to Student</td><td>:</td><TD><table><tr><td>'._makeAutoSelectInputX($s_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','SECONDARY','',$sec_relation_options).'</td><td>';if($s_addr[1]['CONTACT_ID']=='') echo '<input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'for_window.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=secondary&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;">';echo '</td></tr></table></TD></tr>';
                         if(User('PROFILE')!='teacher')
                        echo '<tr><td style=width:120px><span class=red>*</span>Relationship to Student</td><td>:</td><TD><table><tr><td>'._makeAutoSelectInputX($s_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','SECONDARY','',$sec_relation_options).'</td><td><input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'for_window.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=secondary&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;"></td></tr></table></TD></tr>';


                            echo '<TR><td><span class=red>*</span>First Name</td><td>:</td><TD>'.TextInput($s_addr[1]['FIRST_NAME'],'values[people][SECONDARY][FIRST_NAME]','','id=sec_fname class=cell_medium').'</TD></tr>';


                            echo '<TR><td><span class=red>*</span>Last Name</td><td>:</td><TD>'.TextInput($s_addr[1]['LAST_NAME'],'values[people][SECONDARY][LAST_NAME]','','id=sec_lname class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Home Phone</td><td>:</td><TD>'.TextInput($s_addr[1]['HOME_PHONE'],'values[people][SECONDARY][HOME_PHONE]','','id=sec_hphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Work Phone</td><td>:</td><TD>'.TextInput($s_addr[1]['WORK_PHONE'],'values[people][SECONDARY][WORK_PHONE]','','id=sec_wphone class=cell_medium').'</TD></tr>';
                            echo '<TR><td>Cell/Mobile Phone</td><td>:</td><TD>'.TextInput($s_addr[1]['CELL_PHONE'],'values[people][SECONDARY][CELL_PHONE]','','id=sec_cphone class=cell_medium').'</TD></tr>';
                            if($s_addr[1]['CONTACT_ID']=='')
//                            echo '<TR><td><span class=red>*</span>Email</td><td>:</td><TD><table><tr><td>'.TextInput($s_addr[1]['EMAIL'],'values[people][SECONDARY][EMAIL]','','autocomplete=off id=sec_email class=cell_medium onkeyup=peoplecheck_email(this,2,0) ').'</td><td><span id="email_2"></span></td></tr></table></TD></tr>';
                                 echo '<TR><td><span class=red>*</span>Email</td><td>:</td><td>'.TextInput($s_addr[1]['EMAIL'],'values[people][SECONDARY][EMAIL]','','autocomplete=off id=sec_email class=cell_medium onkeyup=peoplecheck_email(this,2,0) ').'</td><td><span id="email_2"></span></td></tr>';
                            else
                            echo '<TR><td><span class=red>*</span>Email</td><td>:</td><TD><table><tr><td>'.TextInput($s_addr[1]['EMAIL'],'values[people][SECONDARY][EMAIL]','','autocomplete=off id=sec_email class=cell_medium onkeyup=peoplecheck_email(this,2,'.$s_addr[1]['CONTACT_ID'].') ').'</td><td><span id="email_2"></span></td></tr></table></TD></tr>';    
                            echo '<TR><TD>Custody of Student</TD><td>:</td><TD>'.CheckboxInputMod($s_addr[1]['CUSTODY'],'values[people][SECONDARY][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';

                            if($s_addr[1]['USER_NAME']=='')
                            {    
                            $portal_check='';    
                            $style='style="display:none"';
                            }
                            else
                            {
                            $portal_check='checked="checked"';
                            $style='';
                            }
                            echo '<input type="hidden" id=sec_val_pass value="Y">';
                            echo '<input type="hidden" id=sec_val_user value="Y">';
                            echo '<input type="hidden" id=val_email_2 name=val_email_2 value="Y">';
                            if($portal_check=='')
                            echo '<TR><TD>Portal User</TD><td>:</td><TD><input type="checkbox" name="secondary_portal" value="Y" id="portal_2" onClick="portal_toggle(2);" '.$portal_check.'/></TD></TR>';   
                            else
                                echo '<TR><TD>Portal User</TD><td>:</td><TD><div id=checked_2><IMG SRC=assets/check.gif width=15></div></TD></TR>';
                            echo '<tr><td colspan=3><div id="portal_div_2" '.$style.'><TABLE>';
                            if($s_addr[1]['USER_NAME']=='' && $s_addr[1]['PASSWORD']=='')
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($s_addr[1]['USER_NAME'],'values[people][SECONDARY][USER_NAME]','','id=secondary_username class=cell_medium onkeyup="usercheck_init_mod(this,2)" ').'<div id="ajax_output_2"></div></TD></TR>';   
                                echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($s_addr[1]['PASSWORD'],'values[people][SECONDARY][PASSWORD]','','id=secondary_password class=cell_medium onkeyup="passwordStrengthMod(this.value,2);validate_password_mod(this.value,2);"').'<span id="passwordStrength2"></span></TD></TR>';   
                            }
                            else
                            {
                                echo '<TR><TD>Username</TD><td>:</td><TD><div id=uname2>'.$s_addr[1]['USER_NAME'].'</div></TD></TR>';
                                echo '<TR><TD>Password</TD><td>:</td><TD><div id=pwd2>'.str_repeat('*',strlen($s_addr[1]['PASSWORD'])).'</div></TD></TR>';
                            }

                        
                        echo '</TABLE></td></tr></div>';
                        
                        echo '<tr><td colspan=3><div id="portal_hidden_div_2" ><TABLE>';
                        echo '</TABLE></td></tr></div>';
                        //if($_REQUEST['address_id']=='new')
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><table><TR><TD><span class=red >*</span><input type="radio" id="rss" name="r6" value="Y" onClick="sec_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="rsn" name="r6" value="N" onClick="sec_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></td></tr>';
			//if($_REQUEST['address_id']=='new')
			if($h_addr[1]['ADDRESS_ID']==0)
			echo '<tr><td colspan=3><div id="sec_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=3><div id="sec_hideShow">';
			echo '<div class=break></div>';
                        $s_sec_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM student_address WHERE ID!=\''.$s_addr[1]['ADDRESS_ID'].'\' AND ADDRESS=\''.str_replace("'","\'",$s_addr[1]['ADDRESS']).'\' AND CITY=\''.str_replace("'","\'",$s_addr[1]['CITY']).'\' AND STATE=\''.str_replace("'","\'",$s_addr[1]['STATE']).'\' AND ZIPCODE=\''.$s_addr[1]['ZIPCODE'].'\' AND TYPE=\'Home Address\' '));
                        if($s_sec_address[1]['TOTAL']!=0)
                           $s_checked=" CHECKED=CHECKED ";
                        else
                            $s_checked=" ";
                         if($h_addr[1]['ADDRESS_ID']!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$s_checked.' id="sec_addr" name="sec_addr" value="Y">&nbsp;Same as Home Address &nbsp;</div><br>';
                         
			echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($s_addr[1]['ADDRESS'],'values[student_address][SECONDARY][ADDRESS]','','id=sec_address class=cell_medium').'</TD><td>';
			//if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
			if($h_addr[1]['ADDRESS_ID']!=0)
			{
				$display_address = urlencode($s_addr[1]['ADDRESS'].', '.($s_addr[1]['CITY']?' '.$s_addr[1]['CITY'].', ':'').$s_addr[1]['STATE'].($s_addr[1]['ZIPCODE']?' '.$s_addr[1]['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($s_addr[1]['STREET'],'values[student_address][SECONDARY][STREET]','','id=sec_street class=cell_medium').'</TD></tr>';
			echo '<TR><td>City</td><td>:</td><TD>'.TextInput($s_addr[1]['CITY'],'values[student_address][SECONDARY][CITY]','','id=sec_city class=cell_medium').'</TD></tr>';
			echo '<TR><td>State</td><td>:</td><TD>'.TextInput($s_addr[1]['STATE'],'values[student_address][SECONDARY][STATE]','','id=sec_state class=cell_medium').'</TD></tr>';
			echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($s_addr[1]['ZIPCODE'],'values[student_address][SECONDARY][ZIPCODE]','','id=sec_zip class=cell_medium').'</TD>';
			echo '</TABLE>';
			echo '</div></td></tr></table></td></tr></table>';

			#echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>';  // close 3d
			
			
############################################################################################			
			
		}

	}
	else
		echo '';
		
	
	$separator = '<HR>';
}


if($_REQUEST['person_id'] && $_REQUEST['con_info']=='old')
{
			echo "<INPUT type=hidden name=person_id value=$_REQUEST[person_id]>";
                        
			if($_REQUEST['person_id']!='old')
			{
                            if($_REQUEST['person_id']!='new')
                            {
                                $other_par_id=  DBGet(DBQuery('SELECT * FROM students_join_people WHERE STUDENT_ID='.UserStudentID().' AND PERSON_ID='.$_REQUEST['person_id'].' AND EMERGENCY_TYPE=\'Other\''));
                            
                               $o_addr=DBGet(DBQuery('SELECT p.STAFF_ID as PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,p.HOME_PHONE,p.WORK_PHONE,p.CELL_PHONE,p.EMAIL,p.CUSTODY,
                                                  sa.ID AS ADDRESS_ID,sa.ADDRESS,sa.STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from people p,student_address sa WHERE p.STAFF_ID=sa.PEOPLE_ID  AND p.STAFF_ID=\''.$_REQUEST['person_id'].'\'  AND sa.PEOPLE_ID IS NOT NULL '));
                               $o_addr[1]['RELATIONSHIP']=$other_par_id[1]['RELATIONSHIP'];
                               $o_addr[1]['IS_EMERGENCY']=$other_par_id[1]['IS_EMERGENCY'];
                               $p_log_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$_REQUEST['person_id'].'\' AND PROFILE_ID=4'));
                                $o_addr[1]['USER_NAME']=$p_log_addr[1]['USER_NAME'];
                                $o_addr[1]['PASSWORD']=$p_log_addr[1]['PASSWORD'];
                //               $p_addr=DBGet(DBQuery('SELECT USERNAME AS USER_NAME ,PASSWORD FROM login_authentication WHERE USER_ID=\''.$pri_par_id[1]['PERSON_ID'].'\' AND PROFILE_ID=3'));
                            }
//                                 $o_addr=DBGet(DBQuery(' SELECT p.ID as PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,p.HOME_PHONE,p.WORK_PHONE,p.CELL_PHONE,p.EMAIL,p.CUSTODY,p.IS_EMERGENCY,p.USER_NAME,p.PASSWORD,
//                                 p.RELATIONSHIP,sa.ID AS ADDRESS_ID,sa.ADDRESS,sa.STREET,sa.CITY,sa.STATE,sa.ZIPCODE,sa.BUS_PICKUP,sa.BUS_DROPOFF,sa.BUS_NO from people p,student_address sa WHERE p.STUDENT_ID=sa.STUDENT_ID 
//                                 and p.SCHOOL_ID=sa.SCHOOL_ID and p.SYEAR=sa.SYEAR AND p.EMERGENCY_TYPE=sa.TYPE AND sa.TYPE=\'Other\' AND sa.OTHER_ID=p.ID AND p.STUDENT_ID=\''.  UserStudentID().'\'  AND p.SYEAR=\''.UserSyear().'\' AND p.SCHOOL_ID=\''.  UserSchool().'\' AND p.ID=\''.$_REQUEST['person_id'].'\' '));
                                 
                                if($o_addr[1]['PERSON_ID']!='')
                                echo '<input type=hidden name="values[people][OTHER][ID]" id=oth_person_id value='.$o_addr[1]['PERSON_ID'].' />';
                                else
                                echo '<input type=hidden name="values[people][OTHER][ID]" id=oth_person_id value=new />';
                                
                                if($o_addr[1]['ADDRESS_ID']!='')
                                echo '<input type=hidden name="values[student_address][OTHER][ID]" value='.$o_addr[1]['ADDRESS_ID'].' />';
                                else
                                echo '<input type=hidden name="values[student_address][OTHER][ID]" value=new />';
                                
                                $relation_options = _makeAutoSelect('RELATIONSHIP','students_join_people','OTHER',$o_addr[1]['RELATIONSHIP'],$relation_options);
                                if($o_addr[1]['IS_EMERGENCY']=='N')
                                    unset($o_addr[1]['IS_EMERGENCY']);
                                if($o_addr[1]['CUSTODY']=='N')
                                    unset($o_addr[1]['CUSTODY']);
                                if($o_addr[1]['BUS_PICKUP']=='N')
                                    unset($o_addr[1]['BUS_PICKUP']);
                                if($o_addr[1]['BUS_DROPOFF']=='N')
                                    unset($o_addr[1]['BUS_DROPOFF']);
                                if($o_addr[1]['BUS_NO']=='N')
                                    unset($o_addr[1]['BUS_NO']);
				echo '<TABLE><TR><TD><FIELDSET><LEGEND><FONT color=gray>Additional Contact</FONT></LEGEND><TABLE width=100% border=0>'; // open 3e
				if($_REQUEST['person_id']!='new' && $_REQUEST['con_info']=='old')
				{
					echo '<TR><TD colspan=3><table><tr><td>'.CheckboxInputMod($o_addr[1]['IS_EMERGENCY'],'values[people][OTHER][IS_EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD> This is an Emergency Contact</TD></TR></table></td></tr>';
					echo '<tr><td colspan=3 class=break></td></tr>';
					echo '<TR><TD>Name</td><td>:</td><td><DIV id=person_'.$o_addr[1]['PERSON_ID'].'><div onclick=\'addHTML("<table><TR><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['FIRST_NAME'],'people','FIRST_NAME','OTHER','','class=cell_medium')).'</TD><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['LAST_NAME'],'people','LAST_NAME','OTHER','','class=cell_medium')).'</TD></TR></TABLE>","person_'.$o_addr[1]['PERSON_ID'].'",true);\'>'.$o_addr[1]['FIRST_NAME'].' '.$o_addr[1]['MIDDLE_NAME'].' '.$o_addr[1]['LAST_NAME'].'</div></DIV></TD></TR>';
					echo '<TR><td style="width:120px">Relationship to Student</td><td>:</td><TD><table><tr><td>'._makeAutoSelectInputX($o_addr[1]['RELATIONSHIP'],'RELATIONSHIP','people','OTHER','',$relation_options).'</td><td></td></tr></table></TD>';
					echo '<tr><TD>Home Phone</td><td>:</td><td> '.TextInput($o_addr[1]['HOME_PHONE'],'values[people][OTHER][HOME_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Work Phone</td><td>:</td><td>'.TextInput($o_addr[1]['WORK_PHONE'],'values[people][OTHER][WORK_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>Mobile Phone</td><td>:</td><td> '.TextInput($o_addr[1]['CELL_PHONE'],'values[people][OTHER][CELL_PHONE]','','class=cell_medium').'</TD></tr>';
					if($o_addr[1]['PERSON_ID']=='')
                                        echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off class=cell_medium onkeyup=peoplecheck_email(this,2,0) ').'</td><td> <span id="email_2"></span></td></tr></table></TD></tr>';
                                        else
                                        echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off class=cell_medium onkeyup=peoplecheck_email(this,2,'.$o_addr[1]['PERSON_ID'].') ').'</td><td> <span id="email_2"></span></td></tr></table></TD></tr>';    
					echo '<TR><TD>Custody</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['CUSTODY'],'values[people][OTHER][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';
					if($o_addr[1]['USER_NAME']=='')
                                        {    
                                        $portal_check='';    
                                        $style='style="display:none"';
                                        }
                                        else
                                        {
                                        $portal_check='checked="checked"';
                                        $style='';
                                        }
                                        echo '<input type="hidden" id=oth_val_pass value="Y">';
                                        echo '<input type="hidden" id=oth_val_user value="Y">';
                                        echo '<input type="hidden" id=val_email_2 name=val_email_2 value="Y">';
                                        if($portal_check=='')
                                        echo '<TR><TD>Portal User</TD><td>:</td><TD><input type="checkbox" name="other_portal" value="Y" id="portal_2" onClick="portal_toggle(2);" '.$portal_check.'/></TD></TR>';   
                                        else
                                            echo '<TR><TD>Portal User</TD><td>:</td><TD><IMG SRC=assets/check.gif width=15></TD></TR>';
                                        echo '<tr><td colspan=3><div id="portal_div_2" '.$style.'><TABLE>';
                                        if($o_addr[1]['USER_NAME']=='' && $o_addr[1]['PASSWORD']=='')
                                        {
                                            echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($o_addr[1]['USER_NAME'],'values[people][OTHER][USER_NAME]','','id=primary_username class=cell_medium onkeyup="usercheck_init_mod(this,2)" ').'<div id="ajax_output_2"></div></TD></TR>';   
                                            echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($o_addr[1]['PASSWORD'],'values[people][OTHER][PASSWORD]','','id=primary_password class=cell_medium onkeyup="passwordStrengthMod(this.value,2);validate_password_mod(this.value,2);"').'<span id="passwordStrength2"></span></TD></TR>';   
                                        }
                                        else
                                        {
                                            echo '<TR><TD>Username</TD><td>:</td><TD>'.$o_addr[1]['USER_NAME'].'</TD></TR>';
                                            echo '<TR><TD>Password</TD><td>:</td><TD>'.str_repeat('*',strlen($o_addr[1]['PASSWORD'])).'</TD></TR>';
                                        }
                                        echo '</TABLE></td></tr></div>';

                                        echo '<tr><td colspan=3><div id="portal_hidden_div_2" ><TABLE>';
                                        echo '</TABLE></td></tr></div>';
                                        echo '<tr><td colspan=3 class=break></td></tr>';	
					echo '<tr><td style="width:120px">Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($o_addr[1]['ADDRESS'],'values[student_address][OTHER][ADDRESS]','','class=cell_medium').'</TD><td>';
					if($o_addr[1]['ADDRESS_ID']!='' && $o_addr[1]['ADDRESS_ID']!='0')
					{
						$display_address = urlencode($o_addr[1]['ADDRESS'].', '.($o_addr[1]['CITY']?' '.$o_addr[1]['CITY'].', ':'').$o_addr[1]['STATE'].($o_addr[1]['ZIPCODE']?' '.$o_addr[1]['ZIPCODE']:''));
						$link = 'http://google.com/maps?q='.$display_address;
						echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
					}
					echo '</td></tr></table></td></tr>';
					echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($o_addr[1]['STREET'],'values[student_address][OTHER][STREET]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>City</td><td>:</td><TD>'.TextInput($o_addr[1]['CITY'],'values[student_address][OTHER][CITY]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>State</td><td>:</td><TD>'.TextInput($o_addr[1]['STATE'],'values[student_address][OTHER][STATE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($o_addr[1]['ZIPCODE'],'values[student_address][OTHER][ZIPCODE]','','class=cell_medium').'</TD></tr>';	
					echo '<TR><TD>School Bus Pick-up</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_PICKUP'],'values[student_address][OTHER][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>School Bus Drop-off</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_DROPOFF'],'values[student_address][OTHER][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>Bus No</td><td>:</td><TD>'.TextInput($o_addr[1]['BUS_NO'],'values[student_address][OTHER][BUS_NO]','','class=cell_small').'</TD></tr>';
					echo '</table>';
					$info_RET = DBGet(DBQuery("SELECT ID,TITLE,VALUE FROM people_join_contacts WHERE PERSON_ID='$_REQUEST[person_id]'"));
//					if($info_apd)
//						$info_options = _makeAutoSelect('TITLE','people_join_contacts',$info_RET,$info_options_x);

					echo '<TR><TD>';
					echo '<TABLE border=0 cellpadding=3 cellspacing=0>';
					if(!$info_apd)
					{
						echo '<TR><TD style="border-color: #BBBBBB; border: 1; border-style: none none solid none;"></TD><TD style="border-color: #BBBBBB; border: 1; border-style: none solid solid none;"><font color=gray>Description</font> &nbsp; </TD><TD style="border-color: #BBBBBB; border: 1; border-style: none none solid none;"><font color=gray>Value</font></TD></TR>';
						if(count($info_RET))
						{
							foreach($info_RET as $info)
							{
							echo '<TR>';
							if(AllowEdit())
								echo '<TD width=20>'.button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$_REQUEST[person_id]&contact_id=".$info['ID']).'</TD>';
							else
								echo '<TD></TD>';
							if($info_apd)
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'._makeAutoSelectInputX($info['TITLE'],'TITLE','people_join_contacts','',$info_options,$info['ID']).'</TD>';
							else
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'.TextInput($info['TITLE'],'values[people_join_contacts]['.$info['ID'].'][TITLE]','','maxlength=100').'</TD>';
							echo '<TD>'.TextInput($info['VALUE'],'values[people_join_contacts]['.$info['ID'].'][VALUE]','','maxlength=100').'</TD>';
							echo '</TR>';
							}
						}
						if(AllowEdit() && $use_contact)
						{
							echo '<TR>';
							echo '<TD width=20>'.button('add').'</TD>';
							if($info_apd)
							{
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'.(count($info_options)>1?SelectInput('','values[people_join_contacts][new][TITLE]','',$info_options,'N/A'):TextInput('','values[people_join_contacts][new][TITLE]','')).'</TD>';
								echo '<TD>'.TextInput('','values[people_join_contacts][new][VALUE]','').'</TD>';
							}
							else
							{
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;"><INPUT size=15 type=TEXT value="Example Phone" style="color: #BBBBBB;" name=values[people_join_contacts][new][TITLE] '."onfocus='if(this.value==\"Example Phone\") this.value=\"\"; this.style.color=\"000000\";' onblur='if(this.value==\"\") {this.value=\"Example Phone\"; this.style.color=\"BBBBBB\";}'></TD>";
								echo '<TD><INPUT size=15 type=TEXT value="(xxx) xxx-xxxx" style="color: #BBBBBB;" name=values[people_join_contacts][new][VALUE] '."onfocus='if(this.value==\"(xxx) xxx-xxxx\") this.value=\"\"; this.style.color=\"000000\";' onblur='if(this.value==\"\") {this.value=\"(xxx) xxx-xxxx\"; this.style.color=\"BBBBBB\";}'></TD>";
							}
							echo '</TR>';
						}
					}
					else
					{
						if(count($info_RET))
						{
							foreach($info_RET as $info)
							{
								echo '<TR>';
								if(AllowEdit())
									echo '<TD width=20>'.button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$_REQUEST[person_id]&contact_id=".$info['ID']).'</TD>';
								else
									echo '<TD></TD>';
								echo '<TD><DIV id=info_'.$info['ID'].'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',TextInput($info['VALUE'],'values[people_join_contacts]['.$info['ID'].'][VALUE]','','',false).'<BR>'.str_replace("'",'&#39;',_makeAutoSelectInputX($info['TITLE'],'TITLE','people_join_contacts','',$info_options,$info['ID'],false))).'</TD></TR></TABLE>","info_'.$info['ID'].'",true);\'>'.$info['VALUE'].'<BR><small><FONT color='.($info_options_x[$info['TITLE']]?Preferences('TITLES'):'blue').'>'.$info['TITLE'].'</FONT></small></div></DIV></TD>';
								echo '</TR>';
							}
						}
						if(AllowEdit() && $use_contact)
						{
							echo '<TR>';
							echo '</TR>';
						}
					}
					echo '</TABLE>';
					echo '</TD></TR>';
					echo '</TABLE>';
					#echo '</FIELDSET>';
					//echo '</TD></TR>';
					//echo '</TABLE>'; // close 3e
					

				}
				else
				{
					echo '<TABLE border=0><TR><TD colspan=3><table><tr><td>'.CheckboxInputMod($o_addr[1]['IS_EMERGENCY'],'values[people][OTHER][IS_EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD>This is an Emergency Contact</TD></TR></table></TD></TR><tr><td colspan=3 class=break></td></tr>';	
                                         if(User('PROFILE')!='teacher')
					echo '<TR><td style="width:120px" style=white-space:nowrap><span class=red>*</span>Relationship to Student</td><td>:</td><TD><table><tr><td>'.SelectInput($o_addr[1]['RELATIONSHIP'],'values[people][OTHER][RELATIONSHIP]','',$relation_options,'N/A').'</td><td><input type="button" name="lookup" value="Lookup" onclick="javascript:window.open(\'for_window.php?modname='.$_REQUEST['modname'].'&modfunc=lookup&type=other&ajax='.$_REQUEST['ajax'].'&address_id='.$_REQUEST['address_id'].'\',\'blank\',\'resizable=yes,scrollbars=yes,width=600,height=400\');return false;"></td></tr></table></TD></TR>';
					
                                            echo '<TR><TD><span class=red>*</span>First Name</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['FIRST_NAME'],'people','FIRST_NAME','OTHER','','id=oth_fname class=cell_medium')).'</TD></tr><tr><td ><span class=red>*</span>Last Name</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput($o_addr[1]['LAST_NAME'],'people','LAST_NAME','OTHER','','id=oth_lname class=cell_medium')).'</TD></TR>';
                                            echo '<tr><TD>Home Phone</td><td>:</td><td> '.TextInput($o_addr[1]['HOME_PHONE'],'values[people][OTHER][HOME_PHONE]','','id=oth_hphone class=cell_medium').'</TD></tr>';
                                            echo '<tr><TD>Work Phone</td><td>:</td><td>'.TextInput($o_addr[1]['WORK_PHONE'],'values[people][OTHER][WORK_PHONE]','','id=oth_wphone class=cell_medium').'</TD></tr>';
                                            echo '<tr><TD>Mobile Phone</td><td>:</td><td> '.TextInput($o_addr[1]['CELL_PHONE'],'values[people][OTHER][CELL_PHONE]','','id=oth_cphone class=cell_medium').'</TD></tr>';
                                            if($o_addr[1]['PERSON_ID']=='')
                                            echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off id=oth_email class=cell_medium onkeyup=peoplecheck_email(this,2,0) ').'</td><td><span id="email_2"></span></td></tr></table></TD></tr>';
                                            else
                                            echo '<tr><TD><span class=red>*</span>Email </td><td>:</td><td><table><tr><td>'.TextInput($o_addr[1]['EMAIL'],'values[people][OTHER][EMAIL]','','autocomplete=off id=oth_email class=cell_medium onkeyup=peoplecheck_email(this,2,'.$o_addr[1]['PERSON_ID'].') ').'</td><td><span id="email_2"></span></td></tr></table></TD></tr>';    
                                            echo '<TR><TD>Custody of Student</td><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['CUSTODY'],'values[people][OTHER][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'<small><FONT color='.Preferences('TITLES').'></FONT></small></TD></TR>';
                                            if($o_addr[1]['USER_NAME']=='')
                                            {    
                                            $portal_check='';    
                                            $style='style="display:none"';
                                            }
                                            else
                                            {
                                            $portal_check='checked="checked"';
                                            $style='';
                                            }
                                            echo '<input type="hidden" id=oth_val_pass value="Y">';
                                            echo '<input type="hidden" id=oth_val_user value="Y">';
                                            echo '<input type="hidden" id=val_email_2 name=val_email_2 value="Y">';
                                            if($portal_check=='')
                                            echo '<TR><TD>Portal User</TD><td>:</td><TD><input type="checkbox" name="other_portal" value="Y" id="portal_2" onClick="portal_toggle(2);" '.$portal_check.'/></TD></TR>';   
                                            else
                                                echo '<TR><TD>Portal User</TD><td>:</td><TD><IMG SRC=assets/check.gif width=15></TD></TR>';
                                            echo '<tr><td colspan=3><div id="portal_div_2" '.$style.'><TABLE>';
                                            if($o_addr[1]['USER_NAME']=='' && $o_addr[1]['PASSWORD']=='')
                                            {
                                                echo '<TR><TD>Username</TD><td>:</td><TD>'.TextInput($o_addr[1]['USER_NAME'],'values[people][OTHER][USER_NAME]','','id=other_username class=cell_medium onkeyup="usercheck_init_mod(this,2)" ').'<div id="ajax_output_2"></div></TD></TR>';   
                                                echo '<TR><TD>Password</TD><td>:</td><TD>'.TextInput($o_addr[1]['PASSWORD'],'values[people][OTHER][PASSWORD]','','id=other_password class=cell_medium onkeyup="passwordStrengthMod(this.value,2);validate_password_mod(this.value,2);"').'<span id="passwordStrength2"></span></TD></TR>';   
                                            }
                                            else
                                            {
                                                echo '<TR><TD>Username</TD><td>:</td><TD>'.$o_addr[1]['USER_NAME'].'</TD></TR>';
                                                echo '<TR><TD>Password</TD><td>:</td><TD>'.str_repeat('*',strlen($o_addr[1]['PASSWORD'])).'</TD></TR>';
                                            }
                                        
                                        echo '</TABLE></td></tr></div>';

                                        echo '<tr><td colspan=3><div id="portal_hidden_div_2" ><TABLE>';
                                        echo '</TABLE></td></tr></div>';
                                        echo '<TR><TD colspan=3><table><TR><TD style=white-space:nowrap><span class=red>*</span><input type="radio" id="ros" name="r7" value="Y" onClick="addn_hidediv();" checked>&nbsp;Same as Student\'s Home Address &nbsp;&nbsp; <input type="radio" id="ron" name="r7" value="N" onClick="addn_showdiv();">&nbsp;Add New Address</TD></TR></TABLE></TD></TR>';
					echo '<TR><TD colspan=3><div id="addn_hideShow" style="display:none">';
					echo '<div class=break></div>';
					echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD>'.TextInput($o_addr[1]['ADDRESS'],'values[student_address][OTHER][ADDRESS]','','id=oth_address class=cell_medium').'</TD></td>';
					
					#echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_address['SEC_ADDRESS'],'values[address][SEC_ADDRESS]','','class=cell_medium').'</TD><td>';
					
					echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($o_addr[1]['STREET'],'values[student_address][OTHER][STREET]','','id=oth_street class=cell_medium').'</TD></tr>';
					echo '<TR><td>City</td><td>:</td><TD>'.TextInput($o_addr[1]['CITY'],'values[student_address][OTHER][CITY]','','id=oth_city class=cell_medium').'</TD></tr>';
					echo '<TR><td>State</td><td>:</td><TD>'.TextInput($o_addr[1]['STATE'],'values[student_address][OTHER][STATE]','','id=oth_state class=cell_medium').'</TD></tr>';
					echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($o_addr[1]['ZIPCODE'],'values[student_address][OTHER][ZIPCODE]','','id=oth_zip class=cell_medium').'</TD></tr>';
					echo '<TR><TD>School Bus Pick-up</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_PICKUP'],'values[student_address][OTHER][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>',false).'</TD></tr>';
					echo '<TR><TD>School Bus Drop-off</TD><td>:</td><TD>'.CheckboxInputMod($o_addr[1]['BUS_DROPOFF'],'values[student_address][OTHER][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>',false).'</TD></tr>';
					echo '<TR><td>Bus No</TD><td>:</td><td>'.TextInput($o_addr[1]['BUS_NO'],'values[student_address][OTHER][BUS_NO]','','id=oth_busno class=cell_small').'</TD></tr>';
					echo '</table></div></td></tr></table>';
				}
				
				
			}
//			elseif($_REQUEST['person_id']=='old')
//			{
//				$people_RET = DBGet(DBQuery('SELECT PERSON_ID,FIRST_NAME,LAST_NAME FROM people WHERE PERSON_ID NOT IN (SELECT PERSON_ID FROM students_join_people WHERE STUDENT_ID=\''.UserStudentID().'\') ORDER BY LAST_NAME,FIRST_NAME'));
//				foreach($people_RET as $people)
//					$people_select[$people['PERSON_ID']] = $people['LAST_NAME'].', '.$people['FIRST_NAME'];
//				echo SelectInput('','values[EXISTING][person_id]',$title='Select Person',$people_select);
//			}
			
			if($_REQUEST['person_id']=='new') {
		echo '</TD></TR>';
		echo '</TABLE>'; // end of table 2
		}
		unset($_REQUEST['address_id']);
		unset($_REQUEST['person_id']);
		}
		
	echo '</TD></TR>';
	echo '</TABLE></td></tr></table>'; // end of table 1
	

function _makePeopleInput($value,$table,$column,$opt,$title='',$options='')
{	global $THIS_RET;

	if($column=='MIDDLE_NAME')
		$options = 'class=cell_medium';
	if($_REQUEST['person_id']=='new')
		$div = false;
	else
		$div = true;

//	if($column=='STUDENT_RELATION')
//		$table = 'students_join_people';
//	else
//		$table = 'people';

	return TextInput($value,"values[$table][$opt][$column]",$title,$options,false);
}

function _makeAutoSelect($column,$table,$opt,$values='',$options=array())
{
        if($opt!='')
            $where=' WHERE EMERGENCY_TYPE=\''.$opt.'\' ';
        else
            $where='';
	$options_RET = DBGet(DBQuery('SELECT DISTINCT '.$column.',upper('.$column.') AS `KEY` FROM '.$table.' '.$where.' ORDER BY `KEY`'));

	// add the 'new' option, is also the separator
	$options['---'] = '---';
	// add values already in table
	if(count($options_RET))
		foreach($options_RET as $option)
			if($option[$column]!='' && !$options[$option[$column]])
				$options[$option[$column]] = array($option[$column],'<FONT color=blue>'.$option[$column].'</FONT>');
	// make sure values are in the list
	if(is_array($values))
	{
		foreach($values as $value)
			if($value[$column]!='' && !$options[$value[$column]])
				$options[$value[$column]] = array($value[$column],'<FONT color=blue>'.$value[$column].'</FONT>');
	}
	else
		if($values!='' && !$options[$values])
			$options[$values] = array($values,'<FONT color=blue>'.$values.'</FONT>');

	return $options;
}

function _makeAutoSelectInputX($value,$column,$table,$opt,$title,$select,$id='',$div=true)
{
	if($column=='CITY' || $column=='MAIL_CITY')
		$options = 'maxlength=60';
	if($column=='STATE' || $column=='MAIL_STATE')
		$options = 'size=3 maxlength=10';
	elseif($column=='ZIPCODE' || $column=='MAIL_ZIPCODE')
		$options = 'maxlength=10';
	else
		$options = 'maxlength=100';

	if($value!='---' && count($select)>1)
		return SelectInput($value,"values[$table][$opt]".($id?"[$id]":'')."[$column]",$title,$select,'N/A','',$div);
	else
		return TextInput($value=='---'?array('---','<FONT color=red>---</FONT>'):$value,"values[$table][$opt]".($id?"[$id]":'')."[$column]",$title,$options,$div);
}
?>