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
function GetStaffList(& $extra)
{	global $profiles_RET;
	$functions = array('PROFILE'=>'makeProfile');
	switch(User('PROFILE'))
	{
		case 'admin':
		$profiles_RET = DBGet(DBQuery('SELECT * FROM user_profiles'),array(),array('ID'));
//                  $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
//					s.PROFILE,s.PROFILE_ID,s.STAFF_ID '.$extra['SELECT'].'
//                FROM
//					people s '.$extra['FROM'].',login_authentication la,students st,student_enrollment ssm,students_join_people sjp
//				WHERE
//					st.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='.UserSyear().' AND s.STAFF_ID=sjp.PERSON_ID AND st.STUDENT_ID=sjp.STUDENT_ID AND s.PROFILE IS NOT NULL AND s.PROFILE_ID=3 AND s.STAFF_ID=la.USER_ID AND la.PROFILE_ID=3';
//                $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
//                        la.USERNAME,s.PROFILE,s.PROFILE_ID,s.STAFF_ID '.$extra['SELECT'].'
//                        FROM
//                        people s '.$extra['FROM'].',login_authentication la,students st,student_enrollment ssm
//                        WHERE
//                        st.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='.UserSyear().'  AND s.PROFILE IS NOT NULL AND s.PROFILE_ID=4 AND s.STAFF_ID=la.USER_ID AND la.PROFILE_ID=4';
                     $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
                        la.USERNAME,s.PROFILE,s.PROFILE_ID,s.IS_DISABLE,s.STAFF_ID '.$extra['SELECT'].'
                        FROM
                        people s '.$extra['FROM'].',login_authentication la,students st,student_enrollment ssm
                        WHERE
                        st.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='.UserSyear().'  AND s.PROFILE IS NOT NULL AND s.PROFILE_ID=4 AND s.STAFF_ID=la.USER_ID AND la.PROFILE_ID=4';
		if($_REQUEST['_search_all_schools']!='Y')
			$sql .= ' AND ssm.SCHOOL_ID='.  UserSchool().' ';
                   else
                       $sql .= ' AND ssm.SCHOOL_ID IN('.  GetUserSchools(UserID(),true).') ';
//		if($_REQUEST['_dis_user']=='Y')
//		{
//			$sql .= " OR s.IS_DISABLE='Y' ";
//		#	$extra['columns_after'] = array('IS_DISABLE'=>'Disable');
//		}
		if($_REQUEST['_dis_user']!='Y')
			$sql .= ' AND (s.IS_DISABLE<>\'Y\' OR  s.IS_DISABLE IS NULL)';
		if($_REQUEST['username'])
			$sql .= 'AND UPPER(la.USERNAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['username'])).'%\' ';
		if($_REQUEST['last'])
			$sql .= 'AND UPPER(s.LAST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['last'])).'%\' ';
		if($_REQUEST['first'])
			$sql .= 'AND UPPER(s.FIRST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['first'])).'%\' ';
		if($_REQUEST['profile'])
                {
                    if(is_number($_REQUEST['profile'])==FALSE)
                        $sql .= ' AND s.PROFILE=\''.$_REQUEST['profile'].'\' AND s.PROFILE_ID IS NULL ';
                     else 
                        $sql .= ' AND s.PROFILE_ID=\''.$_REQUEST['profile'].'\' ';
                }

		$sql .= $extra['WHERE'].' ';
		$sql .= 'ORDER BY FULL_NAME';
//		echo $sql;
/**************************************for Back to User*************************************************************/
            if($_SESSION['staf_search']['sql'] && $_REQUEST['return_session']) {
               $sql= $_SESSION['staf_search']['sql'];
            }
            else
            {
                if ($_REQUEST['sql_save_session_staf'])
                    $_SESSION['staf_search']['sql'] = $sql;
            }
/***************************************************************************************************/
		if ($extra['functions'])
			$functions += $extra['functions'];
		return DBGet(DBQuery($sql),$functions);
		break;
	}
}
function GetUserStaffList(& $extra)
{	global $profiles_RET;
	$functions = array('PROFILE'=>'makeProfile');
	switch(User('PROFILE'))
	{
		case 'admin':
		$profiles_RET = DBGet(DBQuery('SELECT * FROM user_profiles'),array(),array('ID'));
                  $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
					s.PROFILE,s.PROFILE_ID,ssr.END_DATE,s.STAFF_ID '.$extra['SELECT'].'
                FROM
					staff s INNER JOIN staff_school_relationship ssr USING(staff_id) '.$extra['FROM'].',login_authentication la
				WHERE
					(s.PROFILE_ID!=4 OR s.PROFILE_ID IS NULL) AND ssr.SYEAR=\''.UserSyear().'\' AND s.STAFF_ID=la.USER_ID AND la.PROFILE_ID NOT IN (3,4)';
//                $sql = 'SELECT DISTINCT(la.USER_ID),CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
//					s.PROFILE,s.PROFILE_ID,ssr.END_DATE,s.STAFF_ID,la.LAST_LOGIN 
//                FROM
//					staff s,staff_school_relationship ssr,login_authentication la
//                                       
//				WHERE
//					(s.PROFILE_ID<>3 OR s.PROFILE_ID IS NULL OR la.PROFILE_ID <> 3) AND ssr.SYEAR=\''.UserSyear().'\' AND s.STAFF_ID=la.USER_ID ';
                    if(User('PROFILE_ID')=='1')
                    $sql.=' AND s.PROFILE_ID!=0 ';
//                if(User('PROFILE_ID')=='24')
//                    $sql.=' AND s.PROFILE_ID!=1 AND s.PROFILE_ID!=11 AND s.PROFILE_ID!=9999 ';
		if($_REQUEST['_search_all_schools']!='Y')
			$sql .= ' AND school_id='.  UserSchool().' ';
                   else
                       $sql .= ' AND school_id IN('.  GetUserSchools(UserID(),true).') ';
//		if($_REQUEST['_dis_user']=='Y')
//		{
//			$sql .= " OR s.IS_DISABLE='Y' ";
//		#	$extra['columns_after'] = array('IS_DISABLE'=>'Disable');
//		}
		if($_REQUEST['_dis_user']!='Y')
			$sql .= ' AND (s.IS_DISABLE<>\'Y\' OR  s.IS_DISABLE IS NULL) AND (ssr.END_DATE>=\''.date('Y-m-d').'\' OR ssr.END_DATE=\'0000-00-00\' )';
		if($_REQUEST['username'])
			$sql .= 'AND UPPER(la.USERNAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['username'])).'%\' ';
		if($_REQUEST['last'])
			$sql .= 'AND UPPER(s.LAST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['last'])).'%\' ';
		if($_REQUEST['first'])
			$sql .= 'AND UPPER(s.FIRST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['first'])).'%\' ';
		if($_REQUEST['profile']=="")
                {
                     $sql .= ' ';
                }
                else
                {
                if($_REQUEST['profile']==0 || $_REQUEST['profile'])
                {
                    if(is_number($_REQUEST['profile'])==FALSE)
                        $sql .= ' AND s.PROFILE=\''.$_REQUEST['profile'].'\' AND s.PROFILE_ID IS NULL ';
                     else 
                        $sql .= ' AND s.PROFILE_ID=\''.$_REQUEST['profile'].'\' ';
                }
                }

		$sql .= $extra['WHERE'].' ';
                
		$sql .= 'ORDER BY FULL_NAME ';
//                $sql.='AND s.PROFILE_ID=la.PROFILE_ID AND la.PROFILE_ID <> 3';
                
//		echo $sql;
/**************************************for Back to User*************************************************************/
            if($_SESSION['staf_search']['sql'] && $_REQUEST['return_session']) {
               $sql= $_SESSION['staf_search']['sql'];
            }
            else
            {
                if ($_REQUEST['sql_save_session_staf'])
                    $_SESSION['staf_search']['sql'] = $sql;
            }
/***************************************************************************************************/
		if ($extra['functions'])
			$functions += $extra['functions'];

		return DBGet(DBQuery($sql),$functions);
		break;
	}
}

//function GetUserStaffList1(& $extra)
//{	global $profiles_RET;
//	$functions = array('PROFILE'=>'makeProfile');
//	switch(User('PROFILE'))
//	{
//		case 'admin':
//		$profiles_RET = DBGet(DBQuery('SELECT * FROM user_profiles'),array(),array('ID'));
////                  $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
////					s.PROFILE,s.PROFILE_ID,ssr.END_DATE,s.STAFF_ID '.$extra['SELECT'].'
////                FROM
////					staff s INNER JOIN staff_school_relationship ssr USING(staff_id) '.$extra['FROM'].',login_authentication la
////				WHERE
////					(s.PROFILE_ID!=4 OR s.PROFILE_ID IS NULL) AND ssr.SYEAR=\''.UserSyear().'\' AND s.STAFF_ID=la.USER_ID AND la.PROFILE_ID NOT IN (3,4)';
//                $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
//					s.PROFILE,s.PROFILE_ID,ssr.END_DATE,s.STAFF_ID,la.LAST_LOGIN 
//                FROM
//					staff s,staff_school_relationship ssr,login_authentication la
//                                       
//				WHERE
//					(la.PROFILE_ID<>3) AND ssr.SYEAR=\''.UserSyear().'\' AND s.STAFF_ID=la.USER_ID ';
//                    if(User('PROFILE_ID')=='1')
//                    $sql.=' AND s.PROFILE_ID!=0 ';
////                if(User('PROFILE_ID')=='24')
////                    $sql.=' AND s.PROFILE_ID!=1 AND s.PROFILE_ID!=11 AND s.PROFILE_ID!=9999 ';
//		if($_REQUEST['_search_all_schools']!='Y')
//			$sql .= ' AND school_id='.  UserSchool().' ';
//                   else
//                       $sql .= ' AND school_id IN('.  GetUserSchools(UserID(),true).') ';
////		if($_REQUEST['_dis_user']=='Y')
////		{
////			$sql .= " OR s.IS_DISABLE='Y' ";
////		#	$extra['columns_after'] = array('IS_DISABLE'=>'Disable');
////		}
//		if($_REQUEST['_dis_user']!='Y')
//			$sql .= ' AND (s.IS_DISABLE<>\'Y\' OR  s.IS_DISABLE IS NULL) AND (ssr.END_DATE>=\''.date('Y-m-d').'\' OR ssr.END_DATE=\'0000-00-00\' )';
//		if($_REQUEST['username'])
//			$sql .= 'AND UPPER(la.USERNAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['username'])).'%\' ';
//		if($_REQUEST['last'])
//			$sql .= 'AND UPPER(s.LAST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['last'])).'%\' ';
//		if($_REQUEST['first'])
//			$sql .= 'AND UPPER(s.FIRST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['first'])).'%\' ';
//		if($_REQUEST['profile']=="")
//                {
//                     $sql .= ' ';
//                }
//                else
//                {
//                if($_REQUEST['profile']==0 || $_REQUEST['profile'])
//                {
//                    if(is_number($_REQUEST['profile'])==FALSE)
//                        $sql .= ' AND s.PROFILE=\''.$_REQUEST['profile'].'\' AND s.PROFILE_ID IS NULL ';
//                     else 
//                        $sql .= ' AND s.PROFILE_ID=\''.$_REQUEST['profile'].'\' ';
//                }
//                }
//
//		$sql .= $extra['WHERE'].' ';
//                
//		$sql .= 'ORDER BY FULL_NAME ';
////                $sql.='AND s.PROFILE_ID=la.PROFILE_ID';
//                
////		echo $sql;
///**************************************for Back to User*************************************************************/
//            if($_SESSION['staf_search']['sql'] && $_REQUEST['return_session']) {
//               $sql= $_SESSION['staf_search']['sql'];
//            }
//            else
//            {
//                if ($_REQUEST['sql_save_session_staf'])
//                    $_SESSION['staf_search']['sql'] = $sql;
//            }
///***************************************************************************************************/
//		if ($extra['functions'])
//			$functions += $extra['functions'];
//
//		return DBGet(DBQuery($sql),$functions);
//		break;
//	}
//}

function makeProfile($value)
{	global $THIS_RET,$profiles_RET;

	if($THIS_RET['PROFILE_ID']!='')
		$return = $profiles_RET[$THIS_RET['PROFILE_ID']][1]['TITLE'];
	elseif($value=='admin')
		$return = 'Administrator w/Custom';
	elseif($value=='teacher')
		$return = 'Teacher w/Custom';
	elseif($value=='parent')
		$return = 'Parent w/Custom';
	elseif($value=='none')
		$return = 'No Access';
	else $return = $value;

	return $return;
}



# ---------------------------------------- For Missing attn ------------------------------------------------- #

function GetStaffList_Miss_Atn(& $extra)
{

		global $profiles_RET;
		$functions = array('PROFILE'=>'makeProfile');
		switch(User('PROFILE'))
		{
			case 'admin':
			$profiles_RET = mysql_query('SELECT * FROM user_profiles');
			$sql = 'SELECT CONCAT(s.LAST_NAME, \' \', s.FIRST_NAME) AS FULL_NAME,
						s.PROFILE,s.PROFILE_ID,s.STAFF_ID '.$extra['SELECT'].'
					FROM
						staff s INNER JOIN staff_school_relationship ssr USING(staff_id) '.$extra['FROM'].'
					WHERE
						ssr.SYEAR=\''.UserSyear().'\'';
			if($_REQUEST['_search_all_schools']!='Y')
				$sql .= ' AND ssr.school_id='.UserSchool().' ';
			if($_REQUEST['username'])
				$sql .= 'AND UPPER(s.USERNAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['username'])).'%\' ';
			if($_REQUEST['last'])
				$sql .= 'AND UPPER(s.LAST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['last'])).'%\' ';
			if($_REQUEST['first'])
				$sql .= 'AND UPPER(s.FIRST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['first'])).'%\' ';
			if($_REQUEST['profile'])
				$sql .= 'AND s.PROFILE=\''.$_REQUEST['profile'].'\' ';


$sql_st = 'SELECT teacher_id FROM missing_attendance mi where school_id=\''.  UserSchool().'\' AND syear=\''.  UserSyear().'\''.$extra['WHERE2'].' UNION SELECT secondary_teacher_id FROM missing_attendance mi where school_id=\''.  UserSchool().'\' AND syear=\''.  UserSyear().'\''.$extra['WHERE2'];
$res_st = mysql_query($sql_st) or die(mysql_error());
//echo count($res_st);
$a = 0;

while($row_st = mysql_fetch_array($res_st))
 {



    /* $RET = DBGET(DBQuery("SELECT DISTINCT s.TITLE AS SCHOOL,acc.SCHOOL_DATE,cp.TITLE FROM attendance_calendar acc,course_periods cp,school_periods sp,SCHOOLS s,staff st,schedule sch WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND st.STAFF_ID='".User('STAFF_ID')."' AND cp.TEACHER_ID='".$row_st['staff_id']."' AND (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0  AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND acc.SCHOOL_DATE>=sch.START_DATE AND acc.SCHOOL_DATE<'".DBDate()."'
		AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM school_years WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM school_semesters WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM school_quarters WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
		AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0
			OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
		AND NOT EXISTS(SELECT '' FROM attendance_completed ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' AND s.ID=acc.SCHOOL_ID ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));
*/
       
//	   $RET = DBGET(DBQuery("SELECT DISTINCT s.TITLE AS SCHOOL,acc.SCHOOL_DATE,cp.TITLE FROM attendance_calendar acc,course_periods cp,school_periods sp,SCHOOLS s,staff st,schedule sch WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND st.STAFF_ID='".User('STAFF_ID')."' AND (cp.TEACHER_ID='".$row_st['staff_id']."' OR cp.SECONDARY_TEACHER_ID='".$row_st['staff_id']."') AND (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0  AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID ".$extra['WHERE1']."
//		AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM school_years WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM school_semesters WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM school_quarters WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
//		AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0
//			OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
//		AND NOT EXISTS(SELECT '' FROM attendance_completed ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND (ac.STAFF_ID=cp.TEACHER_ID OR ac.STAFF_ID=cp.SECONDARY_TEACHER_ID) AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' AND s.ID=acc.SCHOOL_ID AND s.ID='".UserSchool()."' ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));


		$teacher_str .= "'".$row_st['teacher_id']."',";
		$a++;

	 }
// echo $a;
 		if($a != 0){
                                        $teacher_str = substr($teacher_str, 0, -1);
                                        $sql .= 'AND s.STAFF_ID IN ('.$teacher_str.')';
                                        
 }
//		else
//		{
//			$sql = substr($sql, 0, -5);
//		}



			$sql .= $extra['WHERE'].' ';
			$sql .= 'ORDER BY FULL_NAME';


			if ($extra['functions'])
				$functions += $extra['functions'];


		if($a != 0)
			return DBGet(DBQuery($sql),$functions);

			break;
	}

}








?>
