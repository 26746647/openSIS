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
include 'modules/Grades/config.inc.php';

if($_REQUEST['modfunc']=='save')
{
 $cur_session_RET=DBGet(DBQuery('SELECT YEAR(start_date) AS PRE,YEAR(end_date) AS POST FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
 if($cur_session_RET[1]['PRE']==$cur_session_RET[1]['POST'])
 {
    $cur_session=$cur_session_RET[1]['PRE'];
 }
 else
 {
    $cur_session=$cur_session_RET[1]['PRE'].'-'.$cur_session_RET[1]['POST'];
 }

	if(count($_REQUEST['mp_arr']) && count($_REQUEST['st_arr']))
	{
	$mp_list = '\''.implode('\',\'',$_REQUEST['mp_arr']).'\'';
	$last_mp = end($_REQUEST['mp_arr']);
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = ' AND s.STUDENT_ID IN ('.$st_list.')';
   // for standard grade card
       /////////for standard grade
        
        if($_REQUEST['elements']['grade_type']=='standard_grade' && !$_REQUEST['elements']['percents'] ){
           //  $extra['SELECT'] .= ',cp.USE_STANDARDS,cp.STANDARD_SCALE_ID,c.TITLE as COURSE_TITLE,cp.TITLE AS TEACHER,sp.SORT_ORDER';
        $extra['SELECT'] .= ',rc_cp.USE_STANDARDS,rc_cp.STANDARD_SCALE_ID,sst.STUDENT_ID,sst.COURSE_PERIOD_ID,sst.MARKING_PERIOD_ID,c.TITLE as COURSE_TITLE,rc_cp.TITLE AS TEACHER,sp.SORT_ORDER';
     //   $extra['SELECT'] .= ',rc_cp.USE_STANDARDS,rc_cp.STANDARD_SCALE_ID,sst.STUDENT_ID,sst.COURSE_PERIOD_ID,sst.MARKING_PERIOD_ID,c.TITLE as COURSE_TITLE,rc_cp.TITLE AS TEACHER,sp.SORT_ORDER';    
   //     $extra['GROUP']	= 'sst.STUDENT_ID';
        }
        else if($_REQUEST['elements']['grade_type']=='effort_grade' && !$_REQUEST['elements']['percents'] )
         $extra['SELECT'] .= ',ste.STUDENT_ID';    
        else  
	 $extra['SELECT'] .= ',rc_cp.USE_STANDARDS,rc_cp.STANDARD_SCALE_ID,rc_cp.COURSE_WEIGHT,rpg.TITLE as GRADE_TITLE,sg1.GRADE_PERCENT,sg1.WEIGHTED_GP,sg1.UNWEIGHTED_GP ,sg1.CREDIT_ATTEMPTED , sg1.COMMENT as COMMENT_TITLE,sg1.STUDENT_ID,sg1.COURSE_PERIOD_ID,sg1.MARKING_PERIOD_ID,c.TITLE as COURSE_TITLE,rc_cp.TITLE AS TEACHER,sp.SORT_ORDER,
                                                    stat.cum_unweighted_factor*sc.reporting_gp_scale as UNWEIGHTED_GPA';
        
        
        
	if(($_REQUEST['elements']['period_absences']=='Y' && !$_REQUEST['elements']['grade_type']) || ($_REQUEST['elements']['period_absences']=='Y' && $_REQUEST['elements']['grade_type'] && $_REQUEST['elements']['percents'] )  )
		$extra['SELECT'] .= ',rc_cp.DOES_ATTENDANCE,
				(SELECT count(*) FROM attendance_period ap,attendance_codes ac
					WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.STATE_CODE=\'A\' AND ap.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND ap.STUDENT_ID=ssm.STUDENT_ID) AS YTD_ABSENCES,
				(SELECT count(*) FROM attendance_period ap,attendance_codes ac
					WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.STATE_CODE=\'A\' AND ap.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND sg1.MARKING_PERIOD_ID=ap.MARKING_PERIOD_ID AND ap.STUDENT_ID=ssm.STUDENT_ID) AS MP_ABSENCES';
        if(($_REQUEST['elements']['gpa']=='Y' && !$_REQUEST['elements']['grade_type']) || ($_REQUEST['elements']['gpa']=='Y' && $_REQUEST['elements']['grade_type'] && $_REQUEST['elements']['percents']) )
            $extra['SELECT'] .=",sg1.weighted_gp as GPA";
        if(($_REQUEST['elements']['comments']=='Y' && !$_REQUEST['elements']['grade_type'] ) || ($_REQUEST['elements']['comments']=='Y' && $_REQUEST['elements']['grade_type'] && $_REQUEST['elements']['percents']) )
                $extra['SELECT'] .= ',s.gender AS GENDER,s.common_name AS NICKNAME';
        /////////for standard grade
        if($_REQUEST['elements']['grade_type']=='standard_grade' && !$_REQUEST['elements']['percents'] )
             //  $extra['FROM'] .= ',courses c,school_periods sp,marking_periods mp,schools sc '; 
        $extra['FROM'] .= ',student_standards sst,course_periods rc_cp,courses c,course_period_var cpv,school_periods sp,marking_periods mp,schools sc '; 
        elseif ($_REQUEST['elements']['grade_type']=='effort_grade' && !$_REQUEST['elements']['percents']) {
        $extra['FROM'] .= ', student_efforts ste '; 
        }
        else
//        $extra['FROM'] .= ',student_report_card_grades sg1 LEFT OUTER JOIN report_card_grades rpg ON (rpg.ID=sg1.REPORT_CARD_GRADE_ID),
//					course_periods rc_cp,course_period_var cpv,courses c,school_periods sp,student_mp_stats stat,marking_periods mp,schools sc ';    
	$extra['FROM'] .= ',student_report_card_grades sg1 LEFT OUTER JOIN report_card_grades rpg ON (rpg.ID=sg1.REPORT_CARD_GRADE_ID),
					course_periods rc_cp,course_period_var cpv,courses c,school_periods sp,student_gpa_calculated stat,schools sc ';
        
         /////////for standard grade
        if($_REQUEST['elements']['grade_type']!='standard_grade' && $_REQUEST['elements']['grade_type']!='effort_grade' && !$_REQUEST['elements']['percents'] )  
            
            
//        $extra['WHERE'] .= ' AND sst.MARKING_PERIOD_ID IN ('.$mp_list.') AND rc_cp.COURSE_PERIOD_ID=sst.COURSE_PERIOD_ID AND cpv.COURSE_PERIOD_ID=rc_cp.COURSE_PERIOD_ID
//					AND c.COURSE_ID = rc_cp.COURSE_ID AND sst.STUDENT_ID=ssm.STUDENT_ID AND sp.PERIOD_ID=cpv.PERIOD_ID
//                                                                                          AND  sc.id=mp.school_id ';
                $extra['WHERE'] .= ' AND sst.MARKING_PERIOD_ID IN ('.$mp_list.') AND rc_cp.COURSE_PERIOD_ID=sst.COURSE_PERIOD_ID AND cpv.COURSE_PERIOD_ID=rc_cp.COURSE_PERIOD_ID
					AND c.COURSE_ID = rc_cp.COURSE_ID AND sst.STUDENT_ID=ssm.STUDENT_ID AND sp.PERIOD_ID=cpv.PERIOD_ID
                                                                                          AND  sc.ID=sg1.SCHOOL_ID';
        if($_REQUEST['elements']['grade_type']=='standard_grade' && !$_REQUEST['elements']['percents'] )
                $extra['WHERE'] .= ' AND sst.MARKING_PERIOD_ID IN ('.$mp_list.') AND rc_cp.COURSE_PERIOD_ID=sst.COURSE_PERIOD_ID AND cpv.COURSE_PERIOD_ID=rc_cp.COURSE_PERIOD_ID
					AND c.COURSE_ID = rc_cp.COURSE_ID AND sst.STUDENT_ID=ssm.STUDENT_ID AND sp.PERIOD_ID=cpv.PERIOD_ID';
        
        elseif ($_REQUEST['elements']['grade_type']=='effort_grade' && !$_REQUEST['elements']['percents'])
        {
           $extra['WHERE'] .= '	AND ste.STUDENT_ID=s.STUDENT_ID ';
          
           $extra['GROUP'] .= 'ste.STUDENT_ID ';
        }
        
       else
	$extra['WHERE'] .= ' AND sg1.MARKING_PERIOD_ID IN ('.$mp_list.')
					AND rc_cp.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND c.COURSE_ID = rc_cp.COURSE_ID AND sg1.STUDENT_ID=ssm.STUDENT_ID AND sp.PERIOD_ID=cpv.PERIOD_ID
                                                                                          AND sg1.marking_period_id=stat.marking_period_id AND sc.ID=sg1.SCHOOL_ID and stat.student_id=ssm.student_id';
        if($_REQUEST['elements']['grade_type']!='effort_grade' || $_REQUEST['elements']['percents'] )
	$extra['ORDER'] .= ',sp.SORT_ORDER,c.TITLE';
        
	$extra['functions']['TEACHER'] = '_makeTeacher';
        if ($_REQUEST['elements']['grade_type']=='effort_grade' && !$_REQUEST['elements']['percents'])
         $extra['group']= array('STUDENT_ID');
        else
	$extra['group']	= array('STUDENT_ID','COURSE_PERIOD_ID','MARKING_PERIOD_ID');
       // echo 'SELECT '.$extra['SELECT'].' FROM '.$extra['FROM'].' WHERE '.$extra['WHERE'].' ORDER BY '.$extra['ORDER'].'GROUP BY '.$extra['group'] ;
	$RET = GetStuList($extra);

	if(($_REQUEST['elements']['comments']=='Y' && !$_REQUEST['elements']['grade_type'] ) || ($_REQUEST['elements']['comments']=='Y' && $_REQUEST['elements']['grade_type']  && $_REQUEST['elements']['percents'] ) ) 
	{
		// GET THE COMMENTS
		unset($extra);
		$extra['WHERE'] = ' AND s.STUDENT_ID IN ('.$st_list.')';
		$extra['SELECT_ONLY'] = 's.STUDENT_ID,sc.COURSE_PERIOD_ID,sc.MARKING_PERIOD_ID,sc.REPORT_CARD_COMMENT_ID,sc.COMMENT,(SELECT SORT_ORDER FROM report_card_comments WHERE ID=sc.REPORT_CARD_COMMENT_ID) AS SORT_ORDER';
		$extra['FROM'] = ',student_report_card_comments sc';
		$extra['WHERE'] .= ' AND sc.STUDENT_ID=s.STUDENT_ID AND sc.MARKING_PERIOD_ID=\''.$last_mp.'\'';
		$extra['ORDER_BY'] = 'SORT_ORDER';
		$extra['group'] = array('STUDENT_ID','COURSE_PERIOD_ID','MARKING_PERIOD_ID');
		$comments_RET = GetStuList($extra);
		//echo '<pre>'; var_dump($comments_RET); echo '</pre>'; exit;

		$all_commentsA_RET = DBGet(DBQuery('SELECT ID,TITLE,SORT_ORDER FROM report_card_comments WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND COURSE_ID IS NOT NULL AND COURSE_ID=\'0\' ORDER BY SORT_ORDER,ID'),array(),array('ID'));
		$commentsA_RET = DBGet(DBQuery('SELECT ID,TITLE,SORT_ORDER FROM report_card_comments WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND COURSE_ID IS NOT NULL AND COURSE_ID!=\'0\''),array(),array('ID'));
		$commentsB_RET = DBGet(DBQuery('SELECT ID,TITLE,SORT_ORDER FROM report_card_comments WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND COURSE_ID IS NULL'),array(),array('ID'));
	}

	if((($_REQUEST['elements']['mp_tardies']=='Y' || $_REQUEST['elements']['ytd_tardies']=='Y') && !$_REQUEST['elements']['grade_type']) || (($_REQUEST['elements']['mp_tardies']=='Y' || $_REQUEST['elements']['ytd_tardies']=='Y') && $_REQUEST['elements']['grade_type'] && $_REQUEST['elements']['percents'] )  )
	{
		// GET THE ATTENDANCE
		unset($extra);
		$extra['WHERE'] = ' AND s.STUDENT_ID IN ('.$st_list.')';
		$extra['SELECT_ONLY'] = 'ap.SCHOOL_DATE,ap.COURSE_PERIOD_ID,ac.ID AS ATTENDANCE_CODE,ap.MARKING_PERIOD_ID,ssm.STUDENT_ID';
		$extra['FROM'] = ',attendance_codes ac,attendance_period ap';
		$extra['WHERE'] .= ' AND ac.ID=ap.ATTENDANCE_CODE AND (ac.DEFAULT_CODE!=\'Y\' OR ac.DEFAULT_CODE IS NULL) AND ac.SYEAR=ssm.SYEAR AND ap.STUDENT_ID=ssm.STUDENT_ID';
		$extra['group'] = array('STUDENT_ID','ATTENDANCE_CODE','MARKING_PERIOD_ID');
		$attendance_RET = GetStuList($extra);
	}

	if((($_REQUEST['elements']['mp_absences']=='Y' || $_REQUEST['elements']['ytd_absences']=='Y') && !$_REQUEST['elements']['grade_type']) || (($_REQUEST['elements']['mp_absences']=='Y' || $_REQUEST['elements']['ytd_absences']=='Y') && $_REQUEST['elements']['grade_type'] && $_REQUEST['elements']['percents']) )
	{
		// GET THE DAILY ATTENDANCE
		unset($extra);
		$extra['WHERE'] = ' AND s.STUDENT_ID IN ('.$st_list.')';
		$extra['SELECT_ONLY'] = 'ad.SCHOOL_DATE,ad.MARKING_PERIOD_ID,ad.STATE_VALUE,ssm.STUDENT_ID';
		$extra['FROM'] = ',attendance_day ad';
		$extra['WHERE'] .= ' AND ad.STUDENT_ID=ssm.STUDENT_ID AND ad.SYEAR=ssm.SYEAR AND (ad.STATE_VALUE=\'0.0\' OR ad.STATE_VALUE=\'.5\') AND ad.SCHOOL_DATE<=\''.GetMP($last_mp,'END_DATE').'\'';
		$extra['group'] = array('STUDENT_ID','MARKING_PERIOD_ID');
		$attendance_day_RET = GetStuList($extra);
	}

//	if($_REQUEST['mailing_labels']=='Y')
//	{
//		// GET THE ADDRESSES
//		unset($extra);
//		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
//		$extra['SELECT'] = 's.STUDENT_ID,s.ALT_ID';
//		Widgets('mailing_labels',true);
//		$extra['SELECT_ONLY'] = $extra['SELECT'];
//		$extra['SELECT'] = '';
//		$extra['group'] = array('STUDENT_ID','ADDRESS_ID');
//		$addresses_RET = GetStuList($extra);
//	}

	if(count($RET))
	{
		$columns = array('COURSE_TITLE'=>'Course');
		if($_REQUEST['elements']['teacher']=='Y')
			$columns += array('TEACHER'=>'Teacher');
		if($_REQUEST['elements']['period_absences']=='Y')
			$columns += array('ABSENCES'=>'Abs<BR>YTD / MP');
		if(count($_REQUEST['mp_arr'])>4)
			$mp_TITLE = 'SHORT_NAME';
		else
			$mp_TITLE = 'TITLE';
		foreach($_REQUEST['mp_arr'] as $mp)
			$columns[$mp] = GetMP($mp,$mp_TITLE);
		if($_REQUEST['elements']['comments']=='Y' )  //for standard grade
		{
			foreach($all_commentsA_RET as $comment)
				$columns['C'.$comment[1]['ID']] = $comment[1]['TITLE'];
			$columns['COMMENT'] = 'Comment';
		}
                                    if($_REQUEST['elements']['gpa']=='Y')
                                        $columns['GPA'] = 'GPA';
   //start of report card print
   //print_r($RET);
		$handle = PDFStart();
                  
       if($_REQUEST['elements']['grade_type']!='effort_grade' || $_REQUEST['elements']['percents'])
       {    
		foreach($RET as $student_id=>$course_periods)
		{                // echo   count($course_periods);
                    //echo $student_id;
			echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td width=105>".DrawLogo()."</td><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool()).' ('.$cur_session.')'."<div style=\"font-size:12px;\">Student Report Card</div></td><td align=right style=\"padding-top:20px\">". ProperDate(DBDate()) ."<br \>Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			echo '<!-- MEDIA SIZE 8.5x11in -->';
          if($_REQUEST['elements']['grade_type']=='' || $_REQUEST['elements']['percents'] )   //when Standard Grade is not selected
          {               
			$comments_arr = array();
			$comments_arr_key = count($all_commentsA_RET)>0;
			unset($grades_RET);
			$i = 0;
//
                        $total_grade_point=0;
                        $Total_Credit_Hr_Attempted=0;
			foreach($course_periods as $course_period_id=>$mps)
			{
				$i++;
				$grades_RET[$i]['COURSE_TITLE'] = $mps[key($mps)][1]['COURSE_TITLE'];
				$grades_RET[$i]['TEACHER'] = $mps[key($mps)][1]['TEACHER'];
                                $grades_RET[$i]['TEACHER_ID']=$mps[key($mps)][1]['TEACHER_ID'];
                $grades_RET[$i]['CGPA'] = round($mps[key($mps)][1]['UNWEIGHTED_GPA'],3);
                                 if($mps[key($mps)][1]['WEIGHTED_GP'] && $mps[key($mps)][1]['COURSE_WEIGHT'])
                                 {
                                       $total_grade_point +=  ($mps[key($mps)][1]['WEIGHTED_GP'] * $mps[key($mps)][1]['CREDIT_ATTEMPTED'] );
                                       $Total_Credit_Hr_Attempted += $mps[key($mps)][1]['CREDIT_ATTEMPTED'];
                                 }
                                 elseif($mps[key($mps)][1]['UNWEIGHTED_GP'])
                                 {
                                       $total_grade_point +=  ($mps[key($mps)][1]['UNWEIGHTED_GP'] * $mps[key($mps)][1]['CREDIT_ATTEMPTED'] );
                                       $Total_Credit_Hr_Attempted += $mps[key($mps)][1]['CREDIT_ATTEMPTED'];
                                 }

                if($_REQUEST['elements']['gpa']=='Y')
                    $grades_RET[$i]['GPA'] = sprintf("%01.3f",($total_grade_point/$Total_Credit_Hr_Attempted));
                                $total_grade_point=0;
                                $Total_Credit_Hr_Attempted=0;

				foreach($_REQUEST['mp_arr'] as $mp)
				{
					if($mps[$mp])
					{
                                                    
                                                    
                                                $dbf=  DBGet(DBQuery('SELECT DOES_BREAKOFF,GRADE_SCALE_ID,TEACHER_ID FROM course_periods WHERE COURSE_PERIOD_ID=\''.$course_period_id.'\''));
                                                $rounding=DBGet(DBQuery('SELECT VALUE FROM program_user_config WHERE USER_ID=\''.$dbf[1]['TEACHER_ID'].'\' AND TITLE=\'ROUNDING\' AND PROGRAM=\'Gradebook\' '));
                                                if(count($rounding))
                                                    $_SESSION['ROUNDING']=$rounding[1]['VALUE'];
                                                else
                                                    $_SESSION['ROUNDING']='';
                                                if($_SESSION['ROUNDING']=='UP')
                                                            $mps[$mp][1]['GRADE_PERCENT'] = ceil($mps[$mp][1]['GRADE_PERCENT']);
                                                    elseif($_SESSION['ROUNDING']=='DOWN')
                                                            $mps[$mp][1]['GRADE_PERCENT'] = floor($mps[$mp][1]['GRADE_PERCENT']);
                                                    elseif($_SESSION['ROUNDING']=='NORMAL')
                                                            $mps[$mp][1]['GRADE_PERCENT'] = round($mps[$mp][1]['GRADE_PERCENT']);
                                                if($dbf[1]['DOES_BREAKOFF']=='Y'&& $mps[$mp][1]['GRADE_PERCENT']!=='')
                                                {
                                                    $tc_grade='n';
                                                    $get_details=DBGet(DBQuery('SELECT TITLE,VALUE FROM program_user_config WHERE TITLE LIKE \''.$course_period_id.'-%'.'\' AND USER_ID=\''.$grades_RET[$i]['TEACHER_ID'].'\' AND PROGRAM=\'Gradebook\' ORDER BY VALUE DESC '));
                                                    if(count($get_details))
                                                    {
                                                    unset($id_mod);
                                                     foreach($get_details as $i_mod=>$d_mod)
                                                     {
                                                         if($mps[$mp][1]['GRADE_PERCENT']>=$d_mod['VALUE'] && !isset($id_mod))
                                                         {
                                                             $id_mod=$i_mod;
                                                         }
                                                     }
                                                     $grade_id_mod=explode('-',$get_details[$id_mod]['TITLE']);
                                                     $grade_title=DBGet(DBQuery('SELECT TITLE FROM report_card_grades WHERE id=\''.$grade_id_mod[1].'\' '));
                                                     $grades_RET[$i][$mp]=$grade_title[1]['TITLE'];
                                                     $tc_grade='y';
                                                    }
                                                    if($tc_grade=='n')
                                                    $grades_RET[$i][$mp] =$mps[$mp][1]['GRADE_TITLE'];
                                                }
                                                else
						$grades_RET[$i][$mp] =$mps[$mp][1]['GRADE_TITLE'];
						if($_REQUEST['elements']['percents']=='Y' && $mps[$mp][1]['GRADE_PERCENT']>0)
                                                {
//                                                    if($_SESSION['ROUNDING']=='UP')
//                                                            $mps[$mp][1]['GRADE_PERCENT'] = ceil($mps[$mp][1]['GRADE_PERCENT']);
//                                                    elseif($_SESSION['ROUNDING']=='DOWN')
//                                                            $mps[$mp][1]['GRADE_PERCENT'] = floor($mps[$mp][1]['GRADE_PERCENT']);
//                                                    elseif($_SESSION['ROUNDING']=='NORMAL')
//                                                            $mps[$mp][1]['GRADE_PERCENT'] = round($mps[$mp][1]['GRADE_PERCENT']);
							$grades_RET[$i][$mp] .= '<br>'.$mps[$mp][1]['GRADE_PERCENT'].'%';
                                                }
						$last_mp = $mp;
					}
				}
				if($_REQUEST['elements']['period_absences']=='Y')
					if($mps[$last_mp][1]['DOES_ATTENDANCE'])
						$grades_RET[$i]['ABSENCES'] = $mps[$last_mp][1]['YTD_ABSENCES'].' / '.$mps[$last_mp][1]['MP_ABSENCES'];
					else
						$grades_RET[$i]['ABSENCES'] = 'n/a';
				if($_REQUEST['elements']['comments']=='Y')
				{
					$sep = '';
					foreach($comments_RET[$student_id][$course_period_id][$last_mp] as $comment)
					{
						if($all_commentsA_RET[$comment['REPORT_CARD_COMMENT_ID']])
							$grades_RET[$i]['C'.$comment['REPORT_CARD_COMMENT_ID']] = $comment['COMMENT']!=' '?$comment['COMMENT']:'&middot;';
						else
						{
							if($commentsA_RET[$comment['REPORT_CARD_COMMENT_ID']])
							{
								$grades_RET[$i]['COMMENT'] .= $sep.$commentsA_RET[$comment['REPORT_CARD_COMMENT_ID']][1]['SORT_ORDER'];
								$grades_RET[$i]['COMMENT'] .= '('.($comment['COMMENT']!=' '?$comment['COMMENT']:'&middot;').')';
								$comments_arr_key = true;
							}
							else
								$grades_RET[$i]['COMMENT'] .= $sep.$commentsB_RET[$comment['REPORT_CARD_COMMENT_ID']][1]['SORT_ORDER'];
							$sep = ', ';
							$comments_arr[$comment['REPORT_CARD_COMMENT_ID']] = $comment['SORT_ORDER'];
						}
					}
					if($mps[$last_mp][1]['COMMENT_TITLE'])
						$grades_RET[$i]['COMMENT'] .= $sep.$mps[$last_mp][1]['COMMENT_TITLE'];
				}
			}
			asort($comments_arr,SORT_NUMERIC);

//			if($_REQUEST['mailing_labels']=='Y')
//				if($addresses_RET[$student_id] && count($addresses_RET[$student_id]))
//					$addresses = $addresses_RET[$student_id];
//				else
//					$addresses = array(0=>array(1=>array('STUDENT_ID'=>$student_id,'ADDRESS_ID'=>'0','MAILING_LABEL'=>'<BR><BR>')));
//			else
				$addresses = array(0=>array());

			foreach($addresses as $address)
			{
				unset($_openSIS['DrawHeader']);
//				if($_REQUEST['mailing_labels']=='Y')
//					echo '<BR><BR><BR>';
				//DrawHeader(Config('TITLE').' Report Card');
				echo '<table border=0>';
                                
                                if($_REQUEST['elements']['picture']=='Y')
                                {
                                $picture_c_jpg=$StudentPicturesPath.$mps[key($mps)][1]['STUDENT_ID'].'.JPG';
                                $picture_l_jpg=$StudentPicturesPath.$mps[key($mps)][1]['STUDENT_ID'].'.jpg';
                                if(file_exists($picture_c_jpg) || file_exists($picture_l_jpg))
                                echo '<tr><td><IMG SRC="'.$picture_c_jpg.'" width=150 class=pic></td></tr>';
				else
                                echo '<tr><td><IMG src="assets/noimage.jpg" width=150 class=pic></td></tr>';
                                }
                                echo '<tr><td>Student Name :</td>';
				echo '<td>'.$mps[key($mps)][1]['FULL_NAME'].'</td></tr>';
				echo '<tr><td>Student ID :</td>';
				echo '<td>'.$mps[key($mps)][1]['STUDENT_ID'].'</td></tr>';
                                echo '<tr><td>Alternate ID :</td>';
				echo '<td>'.$mps[key($mps)][1]['ALT_ID'].'</td></tr>';
				echo '<tr><td>Student Grade :</td>';
				echo '<td>'.$mps[key($mps)][1]['GRADE_ID'].'</td></tr>';
				echo '</table>';
				//DrawHeader($mps[key($mps)][1]['FULL_NAME'],$mps[key($mps)][1]['STUDENT_ID']);
				//DrawHeader($mps[key($mps)][1]['GRADE_ID'],GetSchool(UserSchool()));

				$count_lines = 3;
				if($_REQUEST['elements']['mp_absences']=='Y')
				{
					$count = 0;
					foreach($attendance_day_RET[$student_id][$last_mp] as $abs)
						$count += 1-$abs['STATE_VALUE'];
					$mp_absences = 'Daily Absences this '.GetMP($last_mp,'TITLE').': '.$count;
				}
				if($_REQUEST['elements']['ytd_absences']=='Y')
				{
					$count = 0;
					foreach($attendance_day_RET[$student_id] as $mp_abs)
						foreach($mp_abs as $abs)
							$count += 1-$abs['STATE_VALUE'];
					DrawHeader('Year-to-Date Daily Absences: '.$count,$mp_absences);
					$count_lines++;
				}
				elseif($_REQUEST['elements']['mp_absences']=='Y')
				{
					DrawHeader($mp_absences);
					$count_lines++;
				}

				if($_REQUEST['elements']['mp_tardies']=='Y')
				{
                                                                                          $attendance_title = DBGet(DBQuery("SELECT TITLE FROM attendance_codes WHERE id='".$_REQUEST['mp_tardies_code']."'"));
                                                                                          $attendance_title=$attendance_title[1]['TITLE'];
					$count = 0;
					foreach($attendance_RET[$student_id][$_REQUEST['mp_tardies_code']][$last_mp] as $abs)
						$count++;
					$mp_tardies = $attendance_title.' in '.GetMP($last_mp,'TITLE').': '.$count;
				}
				if($_REQUEST['elements']['ytd_tardies']=='Y')
				{
                                                                                          $attendance_title = DBGet(DBQuery("SELECT TITLE FROM attendance_codes WHERE id='".$_REQUEST['ytd_tardies_code']."'"));
                                                                                          $attendance_title=$attendance_title[1]['TITLE'];
					$count = 0;
					foreach($attendance_RET[$student_id][$_REQUEST['ytd_tardies_code']] as $mp_abs)
						foreach($mp_abs as $abs)
							$count++;
					DrawHeader($attendance_title.' this year: '.$count,$mp_tardies);
					$count_lines++;
				}
				elseif($_REQUEST['elements']['mp_tardies']=='Y')
				{
					DrawHeader($mp_tardies);
					$count_lines++;
				}

//				if($_REQUEST['mailing_labels']=='Y')
//				{
//					//DrawHeader(ProperDate(DBDate()));
//					$count_lines++;
//					for($i=$count_lines;$i<=6;$i++)
//						echo '<BR>';
//					echo '<TABLE border=0><TR><TD >Student Mailling Label:</TD><TD>'.$address[1]['MAILING_LABEL'].'</TD></TR></TABLE>';
//				}
				#echo '<BR>';
				ListOutputPrint($grades_RET,$columns,'','',array(),array(),array('print'=>false));
				
				if($_REQUEST['elements']['comments']=='Y' && ($comments_arr_key || count($comments_arr)))
				{
					$gender = substr($mps[key($mps)][1]['GENDER'],0,1);
					$personalizations = array('^n'=>($mps[key($mps)][1]['NICKNAME']?$mps[key($mps)][1]['NICKNAME']:$mps[key($mps)][1]['FIRST_NAME']),
								'^s'=>($gender=='M'?'his':($gender=='F'?'her':'his/her')) );

					echo '<TABLE width=100%><TR><TD colspan=2><b>Explanation of Comment Codes</b></TD>';
					$i = 0;
					if($comments_arr_key)
						foreach($commentsA_select as $key=>$comment)
						{
							if($i++%3==0)
								echo '</TR><TR valign=top>';
							echo '<TD>('.($key!=' ' ? $key : '&middot;').'): '.$comment[2].'</TD>';
						}
					foreach($comments_arr as $comment=>$so)
					{
						if($i++%3==0)
							echo '</TR><TR valign=top>';
						if($commentsA_RET[$comment])
							echo '<TD width=33%><small>'.$commentsA_RET[$comment][1]['SORT_ORDER'].': '.str_replace(array_keys($personalizations),$personalizations,$commentsA_RET[$comment][1]['TITLE']).'</small></TD>';
						else
							echo '<TD width=33%><small>'.$commentsB_RET[$comment][1]['SORT_ORDER'].': '.str_replace(array_keys($personalizations),$personalizations,$commentsB_RET[$comment][1]['TITLE']).'</small></TD>';
					}
					echo '</TR></TABLE>';
				}
                if($_REQUEST['elements']['signature']=='Y' && !$_REQUEST['elements']['grade_type'] )
                {
                	echo '<br/>';
                    echo '<span style="font-size:13px; font-weight:bold;"></span>';
					echo '<br/><br/><br/>';
                    echo '<table width="100%" border=0>';
                    echo '<tr><td align="left" width=190><span style="font-size:13px; font-weight:bold; height:30px;">Teacher&rsquo;s Signature</span><br/><br/><br/><br/></td><td width="5" valign=top>:</td><td align=left  valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align=left><span style="font-size:13px; font-weight:bold; height:30px;">Principal&rsquo;s Signature</span><br/><br/><br/><br/></td><td  valign=top>:</td><td  valign=top>______________________________________</td>';
                    echo '<td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align="left"><span style="font-size:13px; font-weight:bold; height:30px;">Parent/Guardian&rsquo;s Signature</span></td><td  valign=top>:</td><td valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '</table>';
					echo '<table width="100%"><tr><td style="font-style:italic; font-size:12px;"></td></tr></table>';
                 }  echo '<br/><br/>';
                 if(!$_REQUEST['elements']['grade_type'])
                 {    
                echo '<span style="font-size:13px; font-weight:bold;"></span>';
				echo '<!-- NEW PAGE -->';
				echo "<div style=\"page-break-before: always;\"></div>";
                 }               
			}
       }
   ################### //when standard grade selected    
       if($_REQUEST['elements']['grade_type']=='standard_grade')     //when standard grade selected pinki
       {
         //echo count($course_periods);
           
           foreach($course_periods as $course_period_id=>$mps)
	   {             //  print_r($mps)        ;
			$i++;
		   $grades_RET[$i]['COURSE_TITLE'] = $mps[key($mps)][1]['COURSE_TITLE'];
		   $grades_RET[$i]['TEACHER'] = $mps[key($mps)][1]['TEACHER'];
                   $grades_RET[$i]['COURSE_PERIOD_ID'] = $mps[key($mps)][1]['COURSE_PERIOD_ID'];
                   $grades_RET[$i]['USE_STANDARDS'] = $mps[key($mps)][1]['USE_STANDARDS'];
                   $grades_RET[$i]['STANDARD_SCALE_ID'] = $mps[key($mps)][1]['STANDARD_SCALE_ID'];
	//echo '<br/></br>aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa<br/></br>';			
	   }
         if(!$_REQUEST['elements']['percents'])  
            echo '<table border="0" style="width:30%; float:left"><tr><td>';
           $addresses = array(0=>array());    
           foreach($addresses as $address)
	   {
		unset($_openSIS['DrawHeader']);
                if(!$_REQUEST['elements']['percents'])
                {    
                echo 'Student Name: '.$mps[key($mps)][1]['FULL_NAME'].'<br/>';
                echo 'Student ID: '.$mps[key($mps)][1]['STUDENT_ID'].'<br/>';
                echo 'Alternate ID : '.$mps[key($mps)][1]['ALT_ID'].'<br/>';
                echo 'Student Grade : '.$mps[key($mps)][1]['GRADE_ID'].'<br/>';
                echo '<br/>';
                echo '</td></tr></table>';
                }
                else
                  echo 'Standard Grade of '.$mps[key($mps)][1]['FULL_NAME'].'<br/>';  
               // print_r($columns);
                // echo '<br/></br>aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa<br/></br>';
               $garde_values = DBGet(DBQuery('SELECT TITLE,COMMENT FROM report_card_grades WHERE GRADE_SCALE_ID="'.$mps[key($mps)][1]['STANDARD_SCALE_ID'].'" ORDER BY SORT_ORDER ')) ;
               if(count($garde_values)!=0)
               {
                 echo '<table valign="" border="1" style=" margin-right:1%; margin-bottom:20px; width:30%;border-collapse:collapse; float:right">';  
                 echo '<tr><td colspan=2 >Grade Legend:</td></tr>';
               foreach ($garde_values as $gkey => $gvalue) {
                 echo '<tr><td>'.$gvalue['TITLE'].'</td><td>'.$gvalue['COMMENT'].'</td></tr>';  
               }
                 echo '</table>';
               }
                
                
               
              
                    //   print_r($grades_RET);
                       foreach ($grades_RET as $key => $s_grade_value) {
                         if($s_grade_value['USE_STANDARDS']=="Y" && $s_grade_value['STANDARD_SCALE_ID']!="" )  
                         {    
                          echo '<table border="1" style=" margin-right:1%; margin-bottom:20px; width:100%;border-collapse:collapse; float:left">'; 
                          echo '<tr bgcolor="#ccc">';
                          echo '<th height="30"><p style="float:left; padding:5px; font-size:16px;">'.$s_grade_value['COURSE_TITLE'].'</p><p style="float:right; padding:12px;">Teacher: '.$s_grade_value['TEACHER'].'</p></th>';
                       //   print_r($_REQUEST['mp_arr']);
                          if(count($_REQUEST['mp_arr'])>4)
			     $mp_TITLE = 'SHORT_NAME';
		          else
			     $mp_TITLE = 'TITLE';
		foreach($_REQUEST['mp_arr'] as $mp)
                {    
			$marking_p = GetMP($mp,$mp_TITLE);
                        echo '<th style=" padding:5px;font-size:12px;">'.$marking_p.'</th>';
                }
                echo '</tr>';
         $course_id= DBGet(DBQuery('SELECT COURSE_ID FROM course_periods WHERE COURSE_PERIOD_ID="'.$s_grade_value['COURSE_PERIOD_ID'].'" '));
         $course_id=$course_id[1]['COURSE_ID'];
         $us_course_standards= DBGet(DBQuery('SELECT cs.ID AS ID,u.STANDARD_REF_NO AS STANDARD_REF_NO,u.STANDARD_DETAILS AS STANDARD_DETAILS FROM course_standards cs,us_common_core_standards u WHERE cs.STANDARD_ID=u.STANDARD_ID AND cs.COURSE_ID="'.$course_id.'" AND cs.STANDARD_TYPE="us_common_core" '));
         $school_course_standards= DBGet(DBQuery('SELECT cs.ID AS ID,s.STANDARD_REF_NO AS STANDARD_REF_NO,s.STANDARD_DETAILS AS STANDARD_DETAILS FROM course_standards cs,school_specific_standards s WHERE cs.STANDARD_ID=s.STANDARD_ID AND cs.COURSE_ID="'.$course_id.'" AND cs.STANDARD_TYPE="school_specific" '));
         $course_standards = array_merge($us_course_standards,$school_course_standards);
        // print_r($course_standards);
              foreach ($course_standards as $cskey => $cs_value) {
                  echo '<tr>';
                  echo '<td style="padding:5px;">'.$cs_value['STANDARD_REF_NO'].': '.$cs_value['STANDARD_DETAILS'].'</td>';
                  foreach($_REQUEST['mp_arr'] as $mp)
                  {    
    $student_standard_grdes = DBGet(DBQuery('SELECT  rcg.TITLE AS STANDARD_GRADE_VALUE FROM student_standards ss,report_card_grades rcg WHERE rcg.GRADE_SCALE_ID="'.$s_grade_value['STANDARD_SCALE_ID'].'" AND rcg.ID=ss.GRADE_ID  AND ss.COURSE_STANDARD_ID="'.$cs_value['ID'].'" AND STUDENT_ID="'.$student_id.'" AND ss.COURSE_PERIOD_ID="'.$s_grade_value['COURSE_PERIOD_ID'].'" AND ss.MARKING_PERIOD_ID="'.$mp.'" '));
                        echo '<td align="center">'.$student_standard_grdes[1]['STANDARD_GRADE_VALUE'].'</td>';
                  }
                  echo '</tr>';
              }
                          
                          
                          
                          
                          echo '</tr>';
                          echo '</table>';
                          
                       }
                       
                       }    
                       
                       
                
        if($_REQUEST['elements']['signature']=='Y' )
                {
                	echo '<br/>';
                    echo '<span style="font-size:13px; font-weight:bold;"></span>';
					echo '<br/><br/><br/>';
                    echo '<table width="100%" border=1>';
                    echo '<tr><td align="left" width=190><span style="font-size:13px; font-weight:bold; height:30px;">Teacher&rsquo;s Signature</span><br/><br/><br/><br/></td><td width="5" valign=top>:</td><td align=left  valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align=left><span style="font-size:13px; font-weight:bold; height:30px;">Principal&rsquo;s Signature</span><br/><br/><br/><br/></td><td  valign=top>:</td><td  valign=top>______________________________________</td>';
                    echo '<td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align="left"><span style="font-size:13px; font-weight:bold; height:30px;">Parent/Guardian&rsquo;s Signature</span></td><td  valign=top>:</td><td valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '</table>';
					echo '<table width="100%"><tr><td style="font-style:italic; font-size:12px;"></td></tr></table>';
                 }   
             echo '<br/><br/>';
                echo '<span style="font-size:13px; font-weight:bold;"></span>';
				echo '<!-- NEW PAGE -->';
				echo "<div style=\"page-break-before: always;\"></div>";     
                
           }
           unset($grades_RET);
           
       }
    ###############for effort grade with percents############################
    elseif($_REQUEST['elements']['grade_type']=='effort_grade') {
        //    echo '<table border="0" style="width:30%; float:left"><tr><td>';
           $addresses = array(0=>array());    
           foreach($addresses as $address)
	   {
		unset($_openSIS['DrawHeader']);
//                if(!$_REQUEST['elements']['percents'])
//                {    
//                echo 'Student Name: '.$mps[key($mps)][1]['FULL_NAME'].'<br/>';
//                echo 'Student ID: '.$mps[key($mps)][1]['STUDENT_ID'].'<br/>';
//                echo 'Alternate ID : '.$mps[key($mps)][1]['ALT_ID'].'<br/>';
//                echo 'Student Grade : '.$mps[key($mps)][1]['GRADE_ID'].'<br/>';
//                echo '<br/>';
//                echo '</td></tr></table>';
//                }
//                else
                 echo 'Effort Grade of '.$mps[key($mps)][1]['FULL_NAME'].'<br/>'; 
          //get $efforts_for_student  for every student
              $eff_garde_values = DBGet(DBQuery('SELECT  VALUE,COMMENT FROM effort_grade_scales WHERE SCHOOL_ID="'.UserSchool().'" AND SYEAR="'.UserSyear().'"  ')) ;
              $efforts_for_student=DBGet(DBQuery('SELECT egc.ID AS EFFORT_CAT_ID,egc.TITLE AS EFFORT_CAT_TITLE,eg.ID AS EFFORT,eg.TITLE,se.MARKING_PERIOD_ID,se.GRADE_VALUE FROM student_efforts se,effort_grades eg,effort_grade_categories egc WHERE egc.ID=eg.EFFORT_CAT AND se.EFFORT_VALUE=eg.ID AND se.STUDENT_ID="'.$mps[key($mps)][1]['STUDENT_ID'].'" AND se.MARKING_PERIOD_ID IN ('.$mp_list.') ORDER BY egc.SORT_ORDER  '));
               if(count($eff_garde_values)!=0 && count($efforts_for_student)!=0 )
               {
                 echo '<table valign="" border="1" style=" margin-right:1%; margin-bottom:20px; width:30%;border-collapse:collapse; float:right">';  
                 echo '<tr><td colspan=2 >Grade Legend:</td></tr>';
               foreach ($eff_garde_values as $gkey => $gvalue) {
                 echo '<tr><td>'.$gvalue['VALUE'].'</td><td>'.$gvalue['COMMENT'].'</td></tr>';  
               }
                 echo '</table>';
               }
               
   
   foreach ($efforts_for_student as $effkey => $eff_value) {
     $efforts_for_students[$eff_value['EFFORT_CAT_ID']][] = $eff_value;  
   }             
               
               
    if(count($efforts_for_student)!=0)
     {
       foreach ($efforts_for_students as $effort_key => $effvalue) {
		   
          echo '<table border="1" style=" text-align:left; font-size:13px;width:100%; margin-bottom:20px;border-collapse:collapse;"><tr bgcolor="#CCC"><td style=" padding:7px;">'.$effvalue[0]['EFFORT_CAT_TITLE'].'</td>';
          if(count($_REQUEST['mp_arr'])>4)
			     $mp_TITLE = 'SHORT_NAME';
		          else
			     $mp_TITLE = 'TITLE';
          foreach($_REQUEST['mp_arr'] as $mp)
                {    
		$marking_p = GetMP($mp,$mp_TITLE);
          echo '<td>'.$marking_p.'</td>';
          
                }
          echo '</tr>';
          foreach ($effvalue as $key => $evalue) {
             $used_efforts[$evalue['EFFORT']][$evalue['MARKING_PERIOD_ID']] = $evalue;    
          }
          
          foreach ($used_efforts as $ekey => $mp_evalue) {
//            echo '<tr><td style=" padding:7px;">'.$mp_evalue[$mp]['TITLE'].'</td>';
            echo '<tr>';
            $effort_title = DBGet(DBQuery('SELECT TITLE FROM effort_grades WHERE ID="'.$ekey.'" ')); //po
            echo '<td style=" padding:7px;">'.$effort_title[1]['TITLE'].'</td>';
            
            
            foreach($_REQUEST['mp_arr'] as $mp)
            {
              $grade = DBGet(DBQuery('SELECT VALUE FROM effort_grade_scales WHERE ID="'.$mp_evalue[$mp]['GRADE_VALUE'].'" '));
              echo '<td style=" padding:7px;">'.$grade[1]['VALUE'].'</td>';  
            }  
            echo '</tr>';
          }
          unset($used_efforts);
          echo '</table>';   
       }
     }
     else
       echo 'No effort grade is available for this student.';   
         
     
     if($_REQUEST['elements']['signature']=='Y')
                {
                	echo '<br/>';
                    echo '<span style="font-size:13px; font-weight:bold;"></span>';
					echo '<br/><br/><br/>';
                    echo '<table width="100%" border="1">';
                    echo '<tr><td align="left" width=190><span style="font-size:13px; font-weight:bold; height:30px;">Teacher&rsquo;s Signature</span><br/><br/><br/><br/></td><td width="5" valign=top>:</td><td align=left  valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align=left><span style="font-size:13px; font-weight:bold; height:30px;">Principal&rsquo;s Signature</span><br/><br/><br/><br/></td><td  valign=top>:</td><td  valign=top>______________________________________</td>';
                    echo '<td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align="left"><span style="font-size:13px; font-weight:bold; height:30px;">Parent/Guardian&rsquo;s Signature</span></td><td  valign=top>:</td><td valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '</table>';
					echo '<table width="100%"><tr><td style="font-style:italic; font-size:12px;"></td></tr></table>';
                 }
       echo '<span style="font-size:13px; font-weight:bold;"></span>';
				echo '<!-- NEW PAGE -->';
				echo "<div style=\"page-break-before: always;\"></div>";          
                
                
           }     
      
     }
       
   ############################################################    
       
		}
        }
    #################only for Effort Grade ########################################  
       else if($_REQUEST['elements']['grade_type']=='effort_grade' && !$_REQUEST['elements']['percents'])
       {      //   print_r($RET);
 // echo  $effort_sql ='SELECT CONCAT(s.LAST_NAME,", ",coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,s.STUDENT_ID,s.ALT_ID,ssm.GRADE_ID FROM students s,student_enrollment ssm,student_efforts se WHERE ssm.STUDENT_ID=s.STUDENT_ID AND se.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR="'.UserSyear().'" AND ssm.SCHOOL_ID="'.UserSchool().'" AND se.STUDENT_ID IN ('.$st_list.') ';
 //  $RET = DBGet(DBQuery($effort_sql));
          
           foreach($RET as $student_id=>$stu_value)
           { 
               echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td width=105>".DrawLogo()."</td><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool()).' ('.$cur_session.')'."<div style=\"font-size:12px;\">Student Report Card</div></td><td align=right style=\"padding-top:20px\">". ProperDate(DBDate()) ."<br \>Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			echo '<!-- MEDIA SIZE 8.5x11in -->';
                        
                echo '<table border="0" style="width:30%; float:left"><tr><td>';
                echo 'Student Name: '.$stu_value[1]['FULL_NAME'].'<br/>';
                echo 'Student ID: '.$stu_value[1]['STUDENT_ID'].'<br/>';
                echo 'Alternate ID : '.$stu_value[1]['ALT_ID'].'<br/>';
                echo 'Student Grade : '.$stu_value[1]['GRADE_ID'].'<br/>';
                echo '<br/>';
                echo '</td></tr></table>';
              
           $eff_garde_values = DBGet(DBQuery('SELECT VALUE,COMMENT FROM effort_grade_scales WHERE SCHOOL_ID="'.UserSchool().'" AND SYEAR="'.UserSyear().'"  ')) ;
               if(count($eff_garde_values)!=0)
               {
                  echo '<table valign="" border="1" style=" margin-right:1%; margin-bottom:20px; width:30%;border-collapse:collapse; float:right">';  
                 echo '<tr><td colspan=2 >Grade Legend:</td></tr>';
               foreach ($eff_garde_values as $gkey => $gvalue) {
                 echo '<tr><td>'.$gvalue['VALUE'].'</td><td>'.$gvalue['COMMENT'].'</td></tr>';  
               }
                 echo '</table>';
               }
               
   $efforts_for_student=DBGet(DBQuery('SELECT egc.ID AS EFFORT_CAT_ID,egc.TITLE AS EFFORT_CAT_TITLE,eg.ID AS EFFORT,eg.TITLE,se.MARKING_PERIOD_ID,se.GRADE_VALUE FROM student_efforts se,effort_grades eg,effort_grade_categories egc WHERE egc.ID=eg.EFFORT_CAT AND se.EFFORT_VALUE=eg.ID AND se.STUDENT_ID="'.$stu_value[1]['STUDENT_ID'].'" AND se.MARKING_PERIOD_ID IN ('.$mp_list.') ORDER BY egc.SORT_ORDER  '));
 //  print_r($efforts_for_student);
  // echo count($efforts_for_student);
   foreach ($efforts_for_student as $effkey => $eff_value) {
     $efforts_for_students[$eff_value['EFFORT_CAT_ID']][] = $eff_value;  
   }
 //  print_r($efforts_for_students); echo count($efforts_for_students);
     if(count($efforts_for_student)!=0)
     {
       foreach ($efforts_for_students as $effort_key => $effvalue) {
          echo '<table border="1" style=" text-align:left; font-size:13px;width:100%; margin-bottom:20px;border-collapse:collapse;"><tr bgcolor="#CCC"><td style=" padding:7px;">'.$effvalue[0]['EFFORT_CAT_TITLE'].'</td>';
          if(count($_REQUEST['mp_arr'])>4)
			     $mp_TITLE = 'SHORT_NAME';
		          else
			     $mp_TITLE = 'TITLE';
          foreach($_REQUEST['mp_arr'] as $mp)
                {    
		$marking_p = GetMP($mp,$mp_TITLE);
          echo '<td  style=" padding:7px;">'.$marking_p.'</td>';
          
                }
          echo '</tr>';
          foreach ($effvalue as $key => $evalue) {
             $used_efforts[$evalue['EFFORT']][$evalue['MARKING_PERIOD_ID']] = $evalue;    
          }
      //    print_r($used_efforts);
          
          
          foreach ($used_efforts as $ekey => $mp_evalue) {
         //   echo '<tr><td style=" padding:7px;">'.$mp_evalue[$mp]['TITLE'].'</td>';
            echo '<tr>';
            $effort_title = DBGet(DBQuery('SELECT TITLE FROM effort_grades WHERE ID="'.$ekey.'" ')); //po
            echo '<td style=" padding:7px;">'.$effort_title[1]['TITLE'].'</td>';  
            
            foreach($_REQUEST['mp_arr'] as $mp)
            {
              $grade = DBGet(DBQuery('SELECT VALUE FROM effort_grade_scales WHERE ID="'.$mp_evalue[$mp]['GRADE_VALUE'].'" '));
              echo '<td style=" padding:7px;">'.$grade[1]['VALUE'].'</td>';  
               // print_r($mp_evalue[$mp]);
            }  
            
//            foreach ($mp_evalue as $mpkey => $mpvalue) {
//             echo '<td>'.$mpvalue['GRADE_VALUE'].'</td>';   
//            }
          //  print_r($mp_evalue);  echo 'dddddd';
            echo '</tr>';
          }
          unset($used_efforts);
          
       //   print_r($effvalue); echo count($effvalue);
          echo '</table>';   
       }
    //   unset($efforts_for_students);
         
//       echo '<table><tr><th>Effort Title</th>'; 
//        
//       echo '</tr>';
////      foreach ($efforts_for_students as $effort_key => $effvalue) {
//        echo '<tr><td>'.$effvalue[0]['TITLE'].'<td>';
//        foreach($_REQUEST['mp_arr'] as $mp)
//        {
//         $student_effort_value = DBGet(DBQuery('SELECT egs.VALUE FROM student_efforts se,effort_grade_scales egs WHERE se.GRADE_VALUE=egs.ID AND se.STUDENT_ID="'.$stu_value[1]['STUDENT_ID'].'" AND se.MARKING_PERIOD_ID="'.$mp.'" AND se.EFFORT_VALUE="'.$effvalue[0]['ID'].'"  '));    
//	 echo '<td>'.$student_effort_value[1]['VALUE'].'</td>';	
//        }
//        echo '</tr>';   
////        foreach ($eff_garde_values as $gkey => $gvalue) {
////                 echo '<tr><td>'.$gvalue['COMMENT'].'</td>';
////        foreach($_REQUEST['mp_arr'] as $mp)
////                {
////      //  echo 'SELECT egs.VALUE FROM student_efforts se,effort_grade_scales egs WHERE se.GRADE_VALUE=egs.ID AND se.STUDENT_ID="'.$stu_value[1]['STUDENT_ID'].'" AND se.MARKING_PERIOD_ID="'.$mp.'" AND se.EFFORT_VALUE="'.$effvalue[0]['ID'].'" ';    
////        $student_effort_value = DBGet(DBQuery('SELECT egs.VALUE FROM student_efforts se,effort_grade_scales egs WHERE se.GRADE_VALUE=egs.ID AND se.STUDENT_ID="'.$stu_value[1]['STUDENT_ID'].'" AND se.MARKING_PERIOD_ID="'.$mp.'" AND se.EFFORT_VALUE="'.$effvalue[0]['ID'].'"  '));
////            echo '<td>'.$student_effort_value[1]['VALUE'].'</td>';
////                }         
////                 echo '</tr>';  
////               }
//         
//       //  print_r($effvalue);
////      }
//      echo '</table>';
     
     }
     else
     {
         BackPrompt('Missing effort grades or No Students were found.');
     }    
          
       if($_REQUEST['elements']['signature']=='Y')
                {
                	echo '<br/>';
                    echo '<span style="font-size:13px; font-weight:bold;"></span>';
					echo '<br/><br/><br/>';
                    echo '<table width="100%" border=0>';
                    echo '<tr><td align="left" width=190><span style="font-size:13px; font-weight:bold; height:30px;">Teacher&rsquo;s Signature</span><br/><br/><br/><br/></td><td width="5" valign=top>:</td><td align=left  valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align=left><span style="font-size:13px; font-weight:bold; height:30px;">Principal&rsquo;s Signature</span><br/><br/><br/><br/></td><td  valign=top>:</td><td  valign=top>______________________________________</td>';
                    echo '<td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '<tr><td align="left"><span style="font-size:13px; font-weight:bold; height:30px;">Parent/Guardian&rsquo;s Signature</span></td><td  valign=top>:</td><td valign=top>______________________________________</td><td valign=top align=left><span style="font-size:13px; font-weight:bold; height:30px;">Date : ______________</span></td></tr>';
                    echo '</table>';
					echo '<table width="100%"><tr><td style="font-style:italic; font-size:12px;"></td></tr></table>';
                 }
       echo '<span style="font-size:13px; font-weight:bold;"></span>';
				echo '<!-- NEW PAGE -->';
				echo "<div style=\"page-break-before: always;\"></div>"; 
     
     
           }    
       }    
    #################end####################################### 
		PDFStop($handle);
	}
	else
		BackPrompt('Missing grades or No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student and marking period.');
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Gradebook >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_openSIS_PDF=true&head_html=Student+Report+Card method=POST target=_blank>";
		#$extra['header_right'] = '<INPUT type=submit value=\'Create Report Cards for Selected Students\'>';

		$attendance_codes = DBGet(DBQuery("SELECT SHORT_NAME,ID FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL) AND TABLE_NAME='0'"));
		#PopTable_wo_header ('header');
		#$extra['extra_header_left'] = PopTable_wo_header ('header');
		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD colspan=2><b>Include on Report Card:</b></TD></TR>';

		$extra['extra_header_left'] .= '<TR><TD></TD><TD><TABLE>';
		$extra['extra_header_left'] .= '<TR>';
                $extra['extra_header_left'] .= '<TD colspan=2 ><INPUT type=radio name=elements[grade_type] value=standard_grade >Standard Grade';  //sg
                $extra['extra_header_left'] .= '<INPUT type=radio name=elements[grade_type] value=effort_grade >Effort Grade</TD>';  //sg
                $extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[teacher] value=Y CHECKED>Teacher</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[signature] value=Y>Include Signature Line</TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[comments] value=Y CHECKED>Comments</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[percents] value=Y>Percents</TD>';

		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[ytd_absences] value=Y CHECKED>Year-to-date Daily Absences</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[mp_absences] value=Y'.(GetMP(UserMP(),'SORT_ORDER')!=1?' CHECKED':'').'>Daily Absences this Marking Period</TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[ytd_tardies] value=Y>Other Attendance Year-to-Date: <SELECT name="ytd_tardies_code">';
		foreach($attendance_codes as $code)
			$extra['extra_header_left'] .= '<OPTION value='.$code['ID'].'>'.$code['SHORT_NAME'].'</OPTION>';
		$extra['extra_header_left'] .= '</SELECT></TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[mp_tardies] value=Y>Other Attendance this Marking Period: <SELECT name="mp_tardies_code">';
		foreach($attendance_codes as $code)
			$extra['extra_header_left'] .= '<OPTION value='.$code['ID'].'>'.$code['SHORT_NAME'].'</OPTION>';
		$extra['extra_header_left'] .= '</SELECT></TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[period_absences] value=Y>Period-by-Period Absences</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[gpa] value=Y>GPA</TD>';
		$extra['extra_header_left'] .= '</TR>';
                $extra['extra_header_left'] .= '<TR>';
		$extra['extra_header_left'] .= '<TD colspan=2><INPUT type=checkbox name=elements[picture] value=Y>Include Student\'s Picture</TD>';
		$extra['extra_header_left'] .= '</TR>';
		$extra['extra_header_left'] .= '</TABLE></TD></TR>';

		#$mps_RET = DBGet(DBQuery("SELECT SEMESTER_ID,MARKING_PERIOD_ID,SHORT_NAME FROM school_quarters WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('SEMESTER_ID'));
		
		$mps_RET = DBGet(DBQuery("SELECT SEMESTER_ID,MARKING_PERIOD_ID,SHORT_NAME FROM school_quarters WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('SEMESTER_ID'));
		
		
if(!$mps_RET)
{
		$mps_RET = DBGet(DBQuery("SELECT YEAR_ID,MARKING_PERIOD_ID,SHORT_NAME FROM school_semesters WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('YEAR_ID'));
}

if(!$mps_RET)
{
		$mps_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,SHORT_NAME FROM school_years WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('MARKING_PERIOD_ID'));
}
		

		
		$extra['extra_header_left'] .= '<TR><TD align=right>Marking Periods</TD><TD><TABLE><TR><TD><TABLE>';
		foreach($mps_RET as $sem=>$quarters)
		{
			#$extra['extra_header_left'] .= '<TR>';
			foreach($quarters as $qtr)
			{
				$pro = GetChildrenMP('PRO',$qtr['MARKING_PERIOD_ID']);
				if($pro)
				{
					$pros = explode(',',str_replace("'",'',$pro));
					foreach($pros as $pro)
						if(GetMP($pro,'DOES_GRADES')=='Y')
							$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$pro.'>'.GetMP($pro,'SHORT_NAME').'</TD>';
				}
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$qtr['MARKING_PERIOD_ID'].'>'.$qtr['SHORT_NAME'].'</TD>';
			}
//			if(GetMP($sem,'DOES_EXAM')=='Y')
//				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$sem.'>'.GetMP($sem,'SHORT_NAME').' Exam</TD>';
//			if(GetMP($sem,'DOES_GRADES')=='Y')
//				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$sem.'>'.GetMP($sem,'SHORT_NAME').'</TD>';
			if(GetMP($sem,'DOES_EXAM')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$sem.'>'.GetMP($sem,'SHORT_NAME').' Exam</TD>';
                                if(GetMP($sem,'DOES_GRADES')=='Y' && $sem != $quarters[1]['MARKING_PERIOD_ID'])
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$sem.'>'.GetMP($sem,'SHORT_NAME').'</TD>';
			#$extra['extra_header_left'] .= '</TR>';
		}
		$extra['extra_header_left'] .= '</TABLE></TD>';
		if($sem)
		{
			$fy = GetParentMP('FY',$sem);
			$extra['extra_header_left'] .= '<TD><TABLE><TR>';
			if(GetMP($fy,'DOES_EXAM')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$fy.'>'.GetMP($fy,'SHORT_NAME').' Exam</TD>';
			if(GetMP($fy,'DOES_GRADES')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$fy.'>'.GetMP($fy,'SHORT_NAME').'</TD>';
			$extra['extra_header_left'] .= '</TR></TABLE></TD>';
		}
		$extra['extra_header_left'] .= '</TD></TR></TABLE></TR>';
//		Widgets('mailing_labels',true);
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
		$extra['extra_header_left'] .= '</TABLE>';
		#$extra['extra_header_left'] .= PopTable ('footer');
		#$extra['extra_header_left'] .= '</TABLE>';
		#$extra['extra_header_left'] .= '<div style=" position:relative; top:40px; left:520px"><INPUT type=submit class=btn_xxlarge value=\'Create Report Cards for Selected Students\'></div>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;
	//$extra['force_search'] = true;

	Widgets('course');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');

	Search('student_id',$extra,'true');
	if($_REQUEST['search_modfunc']=='list')
	{
		if($_SESSION['count_stu']!=0)
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Report Cards for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function _makeTeacher($teacher,$column)
{
	return substr($teacher,strrpos(str_replace(' - ',' ^ ',$teacher),'^')+2);
}
?>