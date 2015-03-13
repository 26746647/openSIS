<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that
#  include staff demographic info, scheduling, grade book, attendance,
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
if($_REQUEST['staff_id']!='new')
    {
        if($_SESSION['fn']=='' && $_REQUEST['staff_id']=='')
        {
            $_SESSION['fn']='user';
        }
        
    }
else
    {
        $_SESSION['fn']='';
    }
###########################################
if(isset($_REQUEST['staff_id']) && $_REQUEST['staff_id']!='new')
{

            $RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM staff WHERE STAFF_ID='".$_REQUEST['staff_id']."'"));
            $count_staff_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM staff"));
            if($count_staff_RET[1]['NUM']>1){
                DrawHeaderHome( 'Selected Staff: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Users/User.php&ajax=true&bottom_back=true&return_session=true target=body>Back to User List</A>');
            }else{
                DrawHeaderHome( 'Selected Staff: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>)');
            }
}
#############################################
if(User('PROFILE')!='admin' && User('PROFILE')!='teacher' && $_REQUEST['staff_id'] && $_REQUEST['staff_id']!='new')
{
	if(User('USERNAME'))
	{
		HackingLog();

    }
	exit;
}

if(!$_REQUEST['include'])
{
    $_REQUEST['include'] = 'Demographic_Info';
    $_REQUEST['category_id'] = '1';
}
elseif(!$_REQUEST['category_id'])
if($_REQUEST['include']=='Demographic_Info')
$_REQUEST['category_id'] = '1';
elseif($_REQUEST['include']=='Address')
$_REQUEST['category_id'] = '2';
elseif($_REQUEST['include']=='Schools_Info')
$_REQUEST['category_id'] = '3';
elseif($_REQUEST['include']=='Certification_Info')
$_REQUEST['category_id'] = '4';
elseif($_REQUEST['include']=='Library')
$_REQUEST['category_id'] = '6';

elseif($_REQUEST['include']!='Other_Info')
{
    $include = DBGet(DBQuery("SELECT ID FROM staff_field_categories_for_staff WHERE INCLUDE='$_REQUEST[include]'"));
    $_REQUEST['category_id'] = $include[1]['ID'];
}


if(User('PROFILE')!='admin')
{
	if(User('PROFILE_ID'))
		$can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Users/User.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
	else
		$can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM staff_exceptions WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Users/User.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
	if($can_edit_RET)
		$_openSIS['allow_edit'] = true;
}

unset($schools);

if($_REQUEST['category_id']==2 && !isset($_REQUEST['address_id']))
{
    $address_id = DBGet(DBQuery("SELECT STAFF_ADDRESS_ID AS ADDRESS_ID FROM staff_address WHERE STAFF_ID='".UserStaffID()."'"));
    $address_id = $address_id[1]['ADDRESS_ID'];
    if(count($address_id)>0)
    $_REQUEST['address_id'] = $address_id;
    else
    $_REQUEST['address_id'] = 'new';
}


if($_REQUEST['category_id']==3 && !isset($_REQUEST['school_info_id']))
{
    $school_info_id_RET = DBGet(DBQuery("SELECT STAFF_SCHOOL_INFO_ID AS SCHOOL_INFO_ID
        FROM staff_school_info
         WHERE STAFF_ID='".UserStaffID()."'"));
    $school_info_id = $school_info_id_RET[1]['SCHOOL_INFO_ID'];
    if($school_info_id && $school_info_id > 0)
    $_REQUEST['school_info_id'] = $school_info_id;
    else
    $_REQUEST['school_info_id'] = 'new';
}

if($_REQUEST['category_id']==4 && !isset($_REQUEST['certification_id']))
{
    $certification_id_RET = DBGet(DBQuery("SELECT STAFF_CERTIFICATION_ID AS CERTIFICATION_ID
        FROM staff_certification
         WHERE STAFF_ID='".UserStaffID()."'"));
    $certification_id = $certification_id_RET[1]['CERTIFICATION_ID'];
    if($certification_id && $certification_id > 0)
    $_REQUEST['certification_id'] = $certification_id;
    else
    $_REQUEST['certification_id'] = 'new';
}
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
    {

    if($_REQUEST['values']['SCHOOLS'])
	{
        $school_array=$_REQUEST['values']['SCHOOLS'];
        $cur_school=array_keys($school_array);
       if($_REQUEST['staff_id']=='new')
        $_REQUEST['staff']['CURRENT_SCHOOL_ID'] = $cur_school[0];
       else
       {
           if($cur_school[0])
            $_REQUEST['staff_school']['CURRENT_SCHOOL_ID'] = $cur_school[0];
       }
	}
         $password = md5($_REQUEST[staff][PASSWORD]);
            $ins_profile=$_REQUEST[staff][PROFILE];
            $res_pass_chk = DBQuery('SELECT * FROM login_authentication WHERE PASSWORD = \''.$password.'\'');
            $num_pass = mysql_num_rows($res_pass_chk);
            $day_valid=true;
	if(count($_POST['staff']) && (User('PROFILE')=='admin' || basename($_SERVER['PHP_SELF'])=='index.php') || $_REQUEST['ajax'])
	{   
		if($_REQUEST['staff_id'] && $_REQUEST['staff_id']!='new' )
		{
			$profile_RET = DBGet(DBQuery("SELECT s.PROFILE,la.PROFILE_ID,la.USERNAME FROM login_authentication la,staff s WHERE la.USER_ID='$_REQUEST[staff_id]' and la.USER_ID=s.STAFF_ID AND la.PROFILE_ID NOT IN (0,3) "));

			if(isset($_REQUEST['staff']['PROFILE']) && $_REQUEST['staff']['PROFILE']!=$profile_RET[1]['PROFILE_ID'])
			{
				if($_REQUEST['staff']['PROFILE']=='admin')
					$_REQUEST['staff']['PROFILE_ID'] = '1';
				elseif($_REQUEST['staff']['PROFILE']=='teacher')
					$_REQUEST['staff']['PROFILE_ID'] = '2';
				elseif($_REQUEST['staff']['PROFILE']=='parent')
					$_REQUEST['staff']['PROFILE_ID'] = '4';
			}

			if($_REQUEST['staff']['PROFILE_ID'])
				DBQuery('DELETE FROM staff_exceptions WHERE USER_ID=\''.$_REQUEST[staff_id].'\'');
			elseif(isset($_REQUEST['staff']['PROFILE_ID']) && $profile_RET[1]['PROFILE_ID'])
			{ 
				DBQuery('DELETE FROM staff_exceptions WHERE USER_ID=\''.$_REQUEST[staff_id].'\'');
				DBQuery('INSERT INTO staff_exceptions (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT s.STAFF_ID,e.MODNAME,e.CAN_USE,e.CAN_EDIT FROM staff s,profile_exceptions e WHERE s.STAFF_ID=\''.$_REQUEST[staff_id].'\' AND s.PROFILE_ID=e.PROFILE_ID');
			}

                        elseif(!$profile_RET[1]['PROFILE_ID'] && $_REQUEST['values']['SCHOOL']['OPENSIS_PROFILE'])
                        {
                            DBQuery('DELETE FROM staff_exceptions WHERE USER_ID=\''.$_REQUEST[staff_id].'\'');
                            DBQuery('INSERT INTO staff_exceptions (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT s.STAFF_ID,e.MODNAME,e.CAN_USE,e.CAN_EDIT FROM staff s,profile_exceptions e WHERE s.STAFF_ID=\''.$_REQUEST[staff_id].'\' AND e.PROFILE_ID='.$_REQUEST['values']['SCHOOL']['OPENSIS_PROFILE']);
                        }

			if($_REQUEST['staff']['USERNAME'] && $_REQUEST['staff']['USERNAME']!=$profile_RET[1]['USERNAME'])
			{
				$existing_staff = DBGet(DBQuery('SELECT ssr.SYEAR FROM staff s,staff_school_relationship ssr WHERE s.STAFF_ID=ssr.STAFF_ID AND s.USERNAME=\''.$_REQUEST['staff']['USERNAME'].'\' AND ssr.SYEAR=(SELECT SYEAR FROM staff_school_relationship WHERE STAFF_ID=\''.$_REQUEST[staff_id].'\')'));
				if(count($existing_staff))
					BackPrompt('A user with that username already exists for the '.$existing_staff[1]['SYEAR'].' school year. Choose a different username and try again.');
			}

                    if(count($_REQUEST['month_staff']))
                    {
                        foreach($_REQUEST['month_staff'] as $column=>$value)
                        {
                            $_REQUEST['staff'][$column] = $_REQUEST['day_staff'][$column].'-'.$_REQUEST['month_staff'][$column].'-'.$_REQUEST['year_staff'][$column];
                            if($_REQUEST['staff'][$column]=='--')
                            $_REQUEST['staff'][$column] = '';
                            elseif(!VerifyDate($_REQUEST['staff'][$column]))
                            {
                                unset($_REQUEST['staff'][$column]);
                                $note = "The invalid date could not be saved. ";
                            }
                        }
                    }
                    unset($_REQUEST['day_staff']); unset($_REQUEST['month_staff']); unset($_REQUEST['year_staff']);          
	  if($_REQUEST['staff']){
			$sql = "UPDATE staff SET ";
                        
                        if($_REQUEST['staff']['PHYSICAL_DISABILITY']=='N' && !isset($_REQUEST['staff']['DISABILITY_DESC']))
                        DBQuery('UPDATE staff SET DISABILITY_DESC=Null WHERE STAFF_ID='.$_REQUEST[staff_id]);
                        
                        
                        if(!isset($_REQUEST['staff']['PHYSICAL_DISABILITY']) && isset($_REQUEST['staff']['DISABILITY_DESC']))
                        {
                         foreach($_REQUEST['staff'] as $i=>$d)
                         {
                             if($i!='DISABILITY_DESC')
                                 $new_REQUEST[$i]=$d;
                         }
                         unset($_REQUEST['staff']);
                         $_REQUEST['staff']=$new_REQUEST;
                         unset($new_REQUEST);
                        }
                         
			foreach($_REQUEST['staff'] as $column_name=>$value)
                        {
                            if($column_name=='SCHOOLS')
                                continue;
                            if(strpos($column_name,"CUSTOM")==0)
                            {
                                $go=true;
                                $custom=DBGet(DBQuery("SHOW COLUMNS FROM staff WHERE FIELD='".$column_name."'"));
                                 $custom=$custom[1];
                                if($custom['NULL']=='NO' && trim($value)=='' && $custom['DEFAULT']){
                                    $value=$custom['DEFAULT'];
                                }elseif($custom['NULL']=='NO' && (is_array($value)? count($value)==0 : trim($value)=='')){
				$custom_id=str_replace("CUSTOM_","",$column_name);
				$custom_TITLE=DBGet(DBQuery("SELECT TITLE FROM staff_fields WHERE ID=".$custom_id));
				$custom_TITLE=$custom_TITLE[1]['TITLE'];
                                if($custom_TITLE!='')
				echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                else 
                                echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                $error=true;
				}else{
				
                                        $custom_id=str_replace("CUSTOM_","",$column_name);
                                        $m_custom_RET=DBGet(DBQuery("SELECT ID,TITLE,TYPE from staff_fields WHERE ID='".$custom_id."' AND TYPE='multiple'"));
                                        if($m_custom_RET)
                                        {
                                                $str="";

                                                foreach($value as $m_custom_val)
                                                {
                                                    if($m_custom_val)
                                                        $str.="||".$m_custom_val;
                                                    }
                                                    if($str)
                                                $value=$str."||";
                                                    else
                                                        $value='';
                                          }
                                                            
                                        }
                                }
                                
				if($column_name=='PASSWORD'){
				    if($value!="")
					{
					$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",md5($value)))."',";
                                        $execute='yes';
					}
				}

                                    if($column_name=='FIRST_NAME' || $column_name=='LAST_NAME'){
					if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$sql .= "$column_name='".mysql_real_escape_string($value)." ',";
					}else
                                       $sql .= "$column_name='".mysql_real_escape_string($value)." ',";
					}
                                        
                                    else{
					if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                         	$sql .= "$column_name='".str_replace("'","\'",str_replace("`","''",$value))."',";
					}else
                                      $sql .= "$column_name='".str_replace("'","''",str_replace("'`","''",$value))."',";
                               
                                    }
                                    $execute='yes';
					
				

			}

		 	 $sql = substr($sql,0,-1) . " WHERE STAFF_ID='$_REQUEST[staff_id]'";
			if(User('PROFILE')=='admin' && $execute=='yes' && $error!=true) 
                        {
				DBQuery($sql);
                 
               echo $err;
                        }
		}
		}
		else
		{
                        unset($error);
			if($_REQUEST['staff']['PROFILE']=='admin')
				$_REQUEST['staff']['PROFILE_ID'] = '1';
			elseif($_REQUEST['staff']['PROFILE']=='teacher')
				$_REQUEST['staff']['PROFILE_ID'] = '2';
			elseif($_REQUEST['staff']['PROFILE']=='parent')
				$_REQUEST['staff']['PROFILE_ID'] = '4';

			$existing_staff = DBGet(DBQuery("SELECT 'exists' FROM login_authentication WHERE USERNAME='".$_REQUEST['staff']['USERNAME']."'"));
			

			$sql = "INSERT INTO staff ";
			

                        $fields ='CURRENT_SCHOOL_ID,';
                        $values =UserSchool().',';

                           if(count($_REQUEST['month_staff']))
                    {
                        foreach($_REQUEST['month_staff'] as $column=>$value)
                        {
                            $_REQUEST['staff'][$column] = $_REQUEST['day_staff'][$column].'-'.$_REQUEST['month_staff'][$column].'-'.$_REQUEST['year_staff'][$column];
                            if($_REQUEST['staff'][$column]=='--')
                            $_REQUEST['staff'][$column] = '';
                            elseif(!VerifyDate($_REQUEST['staff'][$column]))
                            {
                                unset($_REQUEST['staff'][$column]);
                                $note = "The invalid date could not be saved.";
                            }
                        }
                    }
                    unset($_REQUEST['day_staff']); unset($_REQUEST['month_staff']); unset($_REQUEST['year_staff']);
                        foreach($_REQUEST['staff'] as $column=>$value)
			{
                            if($column_name=='SCHOOLS')
                                continue;
                            if(strpos($column,"CUSTOM")==0)
                            {
                                    $custom=DBGet(DBQuery("SHOW COLUMNS FROM staff WHERE FIELD='".$column."'"));
                                    $custom=$custom[1];
                                    if($custom['NULL']=='NO' && trim($value)=='' && !$custom['DEFAULT'])
                                    {
                                        $custom_id=str_replace("CUSTOM_","",$column);
                                        $custom_TITLE=DBGet(DBQuery("SELECT TITLE FROM staff_fields WHERE ID='".$custom_id."' "));
                                        $custom_TITLE=$custom_TITLE[1]['TITLE'];

                                        $error=true;
                                    }
                                    else
                                    {
                                           $custom_id=str_replace("CUSTOM_","",$column);
                                           $m_custom_RET=DBGet(DBQuery("SELECT ID,TITLE,TYPE from staff_fields WHERE ID='".$custom_id."' AND TYPE='multiple'"));
                                           if($m_custom_RET)
                                             {
                                               $str="";
                                               foreach($value as $m_custom_val)
                                                {
                                                   if($m_custom_val)
                                                    $str.="||".$m_custom_val;
                                                }
                                                if($str)
                                               $value=$str."||";
                                                else
                                                    $value='';
                                              }

                                         }
                                     }
				if($value)
				{
					if($column=='FN')
					 $column='FIRST_NAME';
					elseif($column=='LN')
					 $column='LAST_NAME';
					elseif($column=='MN')
					 $column='MIDDLE_NAME';
					$fields .= $column.',';
                                          if($column=='FIRST_NAME' || $column=='LAST_NAME'){
					if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
					$values .= "'".mysql_real_escape_string($value)." ',";
					}else
                                       $values .= "'".mysql_real_escape_string($value)." ',";
					}
                                        else{
					 if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux')){
                                                $values .= "'".str_replace("'","\'",$value)."',";
                                            }else
                                            $values .= "'".str_replace("'","''",$value)."',";
				#	$values .= "'".str_replace("\'","''",$value)."',";
                                        }
				}
			}
			 $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
                         
                        if($error!=true)
                        {
                         DBQuery($sql);
			// possible modification start
			$staff_id = DBGet(DBQuery("select max(staff_id) as id from staff"));
			$staff_id = $staff_id[1]['ID'];
			// possible modification end

                        
			$_SESSION['staff_id'] = $_REQUEST['staff_id'] = $staff_id;
                        if($school_array)
                    {
                    $rel_value='';
                    foreach($school_array as $school_id=>$yes)
                    {
                            if($_REQUEST['day_values']['START_DATE'][$school_id])
                            {
                                $start_date=$_REQUEST['day_values']['START_DATE'][$school_id]."-".$_REQUEST['month_values']['START_DATE'][$school_id]."-".$_REQUEST['year_values']['START_DATE'][$school_id];
                            }
                            else
                            {
                                $start_date='';
                            }
                            if($_REQUEST['day_values']['END_DATE'][$school_id])
                            {
                                $end_date=$_REQUEST['day_values']['END_DATE'][$school_id]."-".$_REQUEST['month_values']['END_DATE'][$school_id]."-".$_REQUEST['year_values']['END_DATE'][$school_id];
                            }
                            else
                            {
                                $end_date='';
                            }
                            if($end_date!='')
                            {
                                $end_date=date('Y-m-d',strtotime($end_date));
                            }
                            else
                            {
                                $end_date='0000-00-00';
                            }
                            		
                if(($start_date!='' && VerifyDate($start_date)) || ($end_date!='' && VerifyDate($end_date)) || ($start_date=='' && $end_date==''))
                    {
                        $day_valid=true; 
                        
                        $user_syear_RET=  DBGet(DBQuery('SELECT MAX(syear) AS USERSYEAR FROM school_years WHERE school_id=\''.$school_id.'\''));
                        $usersyear = $user_syear_RET[1]['USERSYEAR'];
                        $rel_value .="($staff_id,$school_id,$usersyear,'".date('Y-m-d',strtotime($start_date))."','".$end_date."'),";
                    }
                    else
                    {
                        $day_valid=true;
                        $user_syear_RET=  DBGet(DBQuery('SELECT MAX(syear) AS USERSYEAR FROM school_years WHERE school_id=\''.$school_id.'\''));
                        $usersyear = $user_syear_RET[1]['USERSYEAR'];
                        $rel_value .="($staff_id,$school_id,$usersyear,'0000-00-00','".$end_date."'),";
                    }
                    }
                    $rel_value=substr($rel_value,0,-1);
                
           
               
                
                DBQuery("INSERT INTO staff_school_relationship(staff_id,school_id,syear,start_date,end_date)VALUES ($rel_value)");
				$_SESSION['staff_id'] = $_REQUEST['staff_id'] = $staff_id;
                }
                else
                {
                $val=DBGet(DBQuery("SELECT MAX(syear) as syear FROM school_years WHERE school_id='".UserSchool()."'"));
                DBQuery("INSERT INTO staff_school_relationship(staff_id,school_id,syear,start_date) values ('".$staff_id."','".UserSchool()."','".$val[1]['SYEAR']."','".date('Y-m-d')."')");    
                }
               
                        }
                        else
                        {
                             echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                                                       
				$_REQUEST['staff_id'] = 'new';
                                $staff=array();
                                $value='';
                        }
			
		}
	}
	if($day_valid==false)
        {
            echo "<center><font color=red><b>Invalid date could not be saved.</b><font></center>";
        }
        if($error=='end_date')
        {
            echo "<font color=red><b>Start date can not be greater than End date</b></font><br/>";
            unset($error);
            if($_REQUEST['staff_id']=='new')
            {
                header("location:modules/Users/Staff.php&staff_id=new");
            }
        }
        if($error=='start_date')
        {
            echo "<font color=red><b>Start date can not be blank</b></font><br/>";
            unset($error);

        }
	unset($_REQUEST['staff']);
	unset($_REQUEST['modfunc']);
	unset($_SESSION['_REQUEST_vars']['staff']);
	unset($_SESSION['_REQUEST_vars']['modfunc']);


	if(User('STAFF_ID')==$_REQUEST['staff_id'])
	{
		unset($_openSIS['User']);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}
} 

$extra['SELECT'] = ',LAST_LOGIN';
$extra['columns_after'] = array('LAST_LOGIN'=>'Last Login');
$extra['functions'] = array('LAST_LOGIN'=>'makeLogin');

if(basename($_SERVER['PHP_SELF'])!='index.php')
{
	if($_REQUEST['staff_id']=='new')
		DrawBC("Users > Add a User");

                else
		DrawBC("Users > ".ProgramTitle());
	SearchStaff('staff_id',$extra);
}
else
	DrawHeader('Create Account');
if($_REQUEST['modfunc']=='delete' && basename($_SERVER['PHP_SELF'])!='index.php' && AllowEdit())
{
	
        # ------------------------------------  For Certification Start ------------------------------------------- #

    if(DeletePrompt('certification'))
	{
		DBQuery("DELETE FROM staff_certification WHERE STAFF_CERTIFICATION_ID='$_REQUEST[certification_id]'");
		
		unset($_REQUEST['modfunc']);

	
		$_REQUEST['certification_id'] = 'new';
		
	}



} 
if((UserStaffID() || $_REQUEST['staff_id']=='new')  && ((basename($_SERVER['PHP_SELF'])!='index.php') || !$_REQUEST['staff']['USERNAME']) && $_REQUEST['modfunc']!='delete' && $_SESSION['fn']!='user' && $_REQUEST['modfunc']!='remove')
 { 
    if($_REQUEST['modfunc']!='delete' || $_REQUEST['delete_ok']=='1')
    {
       if($_REQUEST['staff_id']!='new')
	{
		
            /*$sql = "SELECT * FROM STAFF  WHERE STAFF_ID='".UserStaffID()."'";*/
			$sql="SELECT * FROM staff  WHERE STAFF_ID='".UserStaffID()."'";
		$QI = DBQuery($sql);
		$staff = DBGet($QI);
		$staff = $staff[1];
		echo "<FORM name=staff id=staff action=Modules.php?modname=$_REQUEST[modname]&custom=staff&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&staff_id=".UserStaffID()."&modfunc=update method=POST >";
	}
       elseif(basename($_SERVER['PHP_SELF'])!='index.php')
		echo "<FORM name=staff id=staff action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&modfunc=update method=POST>";
                else
		echo "<FORM name=F2 id=F2 action=index.php?modfunc=create_account METHOD=POST>";

        if(User('PROFILE')!='student')
        {
            if(User('PROFILE_ID')!='')
            $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
            else
            $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM staff_exceptions WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
        }
        else
        $can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID='3' AND CAN_USE='Y'"),array(),array('MODNAME'));
        $categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM staff_field_categories_for_staff ORDER BY SORT_ORDER,TITLE"));
            $tabs=array();   
        foreach($categories_RET as $category)
        {  
            if($can_use_RET['Users/Staff.php&category_id='.$category['ID']])
            {
                if($category['ID']=='1')
                $include = 'Demographic_Info';
                elseif($category['ID']=='2')
                $include = 'Address';
                elseif($category['ID']=='3')
                $include = 'Schools_Info';
                elseif($category['ID']=='4')
                $include = 'Certification_Info';
                elseif($category['ID']=='6')
		$include = 'Library';
                elseif($category['INCLUDE'])
                $include = $category['INCLUDE'];
                else
                $include = 'Other_Info';

                $tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&include=$include&custom=staff&category_id=".$category['ID']);
            }
            
        }
        
        $_openSIS['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&custom=staff";
        if($_REQUEST['category_id'])
        $_openSIS['selected_tab'] .= '&category_id='.$_REQUEST['category_id']; 

        
        echo  '<BR>';
        echo '<div id=sh_err></div>';
        echo '<div id=prof_err></div>';
        echo '<div id=cat_err></div>';
        if(Count($tabs)==0)
            echo  PopTable('header','Demographic Info');
        else
            echo  PopTable('header',$tabs,'');
		echo '<div id=sh_err></div>';
        if(!strpos($_REQUEST['include'],'/'))
        include('modules/Users/includes/'.$_REQUEST['include'].'.inc.php');
        else
        {   
            include('modules/'.$_REQUEST['include'].'.inc.php');
            $separator = '<HR>';
            include('modules/Users/includes/Other_Info.inc.php');
        }
        echo PopTable('footer');
        if(User('PROFILE')=='admin')
        {
            if($_REQUEST['category_id']!=3)
            {
             echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onClick="formcheck_add_staff(0);"').'</CENTER>';
            }
            else
            {
             if($_SESSION[staff_school_chkbox_id]!='')
             echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onClick="return formcheck_add_staff('.$_SESSION[staff_school_chkbox_id].');"').'</CENTER>';
             else
                 echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onClick="formcheck_add_staff(0);"').'</CENTER>';
             unset($_SESSION[staff_school_chkbox_id]);
            }

        
        }
        echo '</FORM>';
    }
    else
    if(!strpos($_REQUEST['include'],'/'))
    include('modules/Users/includes/'.$_REQUEST['include'].'.inc.php');
    else
    {
        include('modules/'.$_REQUEST['include'].'.inc.php');
        $separator = '<div class=break></div>';
        include('modules/Users/includes/Other_Info.inc.php');
    }
 }
if($_REQUEST['modfunc']=='remove' )
{
include('modules/Users/includes/Certification_Info.inc.php');
}
?>
