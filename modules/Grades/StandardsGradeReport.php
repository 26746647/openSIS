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
//echo UserMp();
echo '<style type="text/css">.tableGrid { border-right:1px solid #000; border-top:1px solid #000; }.tableGrid td { border-left:1px solid #000; border-bottom:1px solid #000; padding:3px; }.tableGrid th { border-left:1px solid #000; border-bottom:1px solid #000; background-color:#d3d3d3; font-size:12px; font-weight:normal; padding:3px; }.course_period_tbl th{font-size:12px;text-decoration:underline; padding:3px;}.standard { padding:3px 3px 3px 15px;}.course_period{padding:3px;}</style>';
if($_REQUEST['modfunc']=='save')
{
    if(count($_REQUEST['st_arr']))
    {
        $st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
        $mp_list = '\''.implode('\',\'',$_REQUEST['mp_arr']).'\'';
//	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
//	$extra['SELECT'] .= ",rcg.TITLE as GRADE_TITLE, cp.COURSE_PERIOD_ID AS CP_ID, cp.TITLE AS CP_TITLE";
//	$extra['FROM'] .= ",STUDENT_STANDARDS ss, REPORT_CARD_GRADES rcg, COURSE_PERIODS cp";
//	$extra['WHERE'] .= " AND ss.GRADE_ID=rcg.ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.STUDENT_ID=ssm.STUDENT_ID";
//	//$extra['ORDER'] .= ",sp.SORT_ORDER,c.TITLE";
////	$extra['functions']['TEACHER'] = '_makeTeacher';
//	$extra['group']	= array('STUDENT_ID');
//	//$extra['group']	= array('STUDENT_ID','COURSE_PERIOD_ID');
//        $RET = GetStuList($extra);
      // print_r($RET);
        $RET=DBGet(DBQuery("SELECT DISTINCT s.STUDENT_ID, CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME FROM students s,student_standards ss, report_card_grades rcg, course_periods cp
        WHERE ss.GRADE_ID=rcg.ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND s.STUDENT_ID=ss.STUDENT_ID
        AND s.STUDENT_ID IN ($st_list)
        "));
        if(count($RET))
	{
            $grade_RET=DBGet(DBQuery("SELECT TITLE, COMMENT FROM report_card_grades WHERE SCHOOL_ID=".UserSchool()." AND SYEAR=".UserSyear()));
            
            
            $handle = PDFStart();
            foreach($RET as $student)
            {
                /*echo "SELECT DISTINCT cp.COURSE_PERIOD_ID AS CP_ID, cp.TITLE AS CP_TITLE, cp.MARKING_PERIOD_ID, CONCAT(st.TITLE, ' ', st.LAST_NAME, ' ',st.FIRST_NAME) AS TEACHER FROM STUDENTS s,STUDENT_STANDARDS ss, REPORT_CARD_GRADES rcg, COURSE_PERIODS cp, MARKING_PERIODS mp, STAFF st
                WHERE ss.GRADE_ID=rcg.ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND s.STUDENT_ID=ss.STUDENT_ID AND cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.TEACHER_ID=st.STAFF_ID
                AND s.STUDENT_ID=$student[STUDENT_ID]";*/
                
                $periods=DBGet(DBQuery("SELECT DISTINCT cp.COURSE_PERIOD_ID AS CP_ID, cp.TITLE AS CP_TITLE, cp.MARKING_PERIOD_ID, CONCAT(st.TITLE, ' ', st.LAST_NAME, ' ',st.FIRST_NAME) AS TEACHER FROM students s,student_standards ss, report_card_grades rcg, course_periods cp, marking_periods mp, staff st
                WHERE ss.GRADE_ID=rcg.ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND s.STUDENT_ID=ss.STUDENT_ID AND cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.TEACHER_ID=st.STAFF_ID
                AND s.STUDENT_ID=$student[STUDENT_ID]"));
				
				echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
				echo "<tr><td style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Elementary Grades ".GetMp(UserMp())."</div><div style=\"font-size:12px;\">Student: ".$student[FULL_NAME]." &nbsp;( ".$student[STUDENT_ID]." )</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
				echo "<table >";
				
                
				echo '<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0" BORDER="0" class="course_period_tbl">';
				echo '<TR><TH align="left">Attendance</TH>';

                              
                                $mp_RET=DBGet(DBQuery("SELECT DISTINCT mp.TITLE, mp.MARKING_PERIOD_ID FROM marking_periods mp, course_periods cp, attendance_period ap
                WHERE mp.MARKING_PERIOD_ID=cp.MARKING_PERIOD_ID
                AND cp.SCHOOL_ID=".UserSchool()." AND cp.SYEAR=".UserSyear()."
                AND cp.COURSE_PERIOD_ID=ap.COURSE_PERIOD_ID AND ap.STUDENT_ID=$student[STUDENT_ID]"));

                $mp_name=DBGet(DBQuery("SELECT DISTINCT mp.SHORT_NAME,mp.MARKING_PERIOD_ID FROM marking_periods mp WHERE mp.SCHOOL_ID=".UserSchool()." AND mp.SYEAR=".UserSyear().""));
                               foreach($mp_name as $mp1)
                               {
                                echo '<TH align="center" width="60px">'.$mp1[SHORT_NAME].'</TH>';
                                }
                                echo '</TR>';

                $atteadance_code_RET=DBGet(DBQuery("SELECT ID, TITLE FROM attendance_codes WHERE SCHOOL_ID=".UserSchool()." AND SYEAR=".UserSyear()." ORDER BY SORT_ORDER, TITLE"));
				foreach($atteadance_code_RET as $ac)
                                 {
				echo '<TR>';
				echo '<TD class="course_period">'.$ac[TITLE].'</TD>';
				//echo '<TD class="course_period"></TD>';
				//echo '<TD class="course_period"></TD>';
				//echo '<TD class="course_period"></TD>';
				//echo '</TR>';
				

                                
                   // echo '<TR>';
                   
                                 foreach ($mp_name as $mp)
                                 {
                                $atteadance_RET=DBGet(DBQuery("SELECT COUNT(ap.ATTENDANCE_CODE) AS ATTENDANCE FROM attendance_period ap, course_periods cp
                                WHERE ap.ATTENDANCE_CODE=$ac[ID]
                                 AND ap.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID
                                 AND ap.MARKING_PERIOD_ID=$mp[MARKING_PERIOD_ID]
                                 AND ap.STUDENT_ID=$student[STUDENT_ID]
                                 AND cp.SCHOOL_ID=".UserSchool()." AND cp.SYEAR=".UserSyear()));
                      //  print_r($atteadance_RET);
                                if(count($atteadance_RET)>0)
                                    {
                                    echo '<TD align="center" class="course_period">'.$atteadance_RET[1][ATTENDANCE].'</TD>';
                                     }
//                                 else {
//                                    echo '<TD class="course_period"></TD>';
//                                     }
                                 }
                                 echo '</TR>';
                     
                              }
				echo '</TABLE>';
				
				
				
				
//                echo '<TABLE aling="left" border="0" cellspacing="0" cellspacing="0" class="tableGrid"><TR><TH>Attendance</TH>';
//                $mp_RET=DBGet(DBQuery("SELECT DISTINCT mp.TITLE, mp.MARKING_PERIOD_ID FROM MARKING_PERIODS mp, COURSE_PERIODS cp, ATTENDANCE_PERIOD ap
//                WHERE mp.MARKING_PERIOD_ID=cp.MARKING_PERIOD_ID
//                AND cp.SCHOOL_ID=".UserSchool()." AND cp.SYEAR=".UserSyear()."
//                AND cp.COURSE_PERIOD_ID=ap.COURSE_PERIOD_ID AND ap.STUDENT_ID=$student[STUDENT_ID]"));
//
//                $atteadance_code_RET=DBGet(DBQuery("SELECT ID, TITLE FROM ATTENDANCE_CODES WHERE SCHOOL_ID=".UserSchool()." AND SYEAR=".UserSyear()." ORDER BY SORT_ORDER, TITLE"));
//                foreach($atteadance_code_RET as $ac)
//                {
//                    echo '<TH>'.$ac[TITLE].'</TH>';
//                }
//                echo '</TR>';
//                foreach ($mp_name as $mp)
//                {
//                    echo '<TR><TD>'.$mp[SHORT_NAME].'</TD>';
//                    $atteadance_code_RET=DBGet(DBQuery("SELECT ID FROM ATTENDANCE_CODES WHERE SCHOOL_ID=".UserSchool()." AND SYEAR=".UserSyear()." ORDER BY SORT_ORDER, TITLE"));
//                    foreach($atteadance_code_RET as $ac)
//                    {
//                        $atteadance_RET=DBGet(DBQuery("SELECT COUNT(ap.ATTENDANCE_CODE) AS ATTENDANCE FROM ATTENDANCE_PERIOD ap, COURSE_PERIODS cp
//                        WHERE ap.ATTENDANCE_CODE=$ac[ID]
//                        AND ap.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID
//                        AND ap.MARKING_PERIOD_ID=$mp[MARKING_PERIOD_ID]
//                        AND ap.STUDENT_ID=$student[STUDENT_ID]
//                        AND cp.SCHOOL_ID=".UserSchool()." AND cp.SYEAR=".UserSyear()));
//                        foreach($atteadance_RET as $attendance)
//                        {
//                            echo '<TD>'.$attendance[ATTENDANCE].'</TD>';
//                        }
//
//                    }
//                     echo '</TR>';
//                }
//
                 echo'<BR />';
//
				  
				  
				  
				  echo '<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0" BORDER="0" class="course_period_tbl">';
				  echo '<TR><TH align="left">Results</TH>';
                                  foreach($mp_name as $mp1)
                                    {
                                     echo '<TH align="center">'.$mp1[SHORT_NAME].'</TH>';
                                    }
                                 echo '</TR>';
				  $flag=0;
				  foreach($periods as $cp)
                	{
					  echo '<TR>';
					  echo '<TD class="course_period" align="left"><strong>'.$cp['CP_TITLE'].'</strong> </TD>';
					  
					  foreach($mp_name as $mp1) {
					  echo '<TD class="course_period" align="center">&nbsp;</TD>';
					  }
					  //echo '<TD class="course_period" align="center"></TD>';
//					  echo '<TD class="course_period" align="center"></TD>';
					  echo '</TR>';
                                             $standards=DBGet(DBQuery("SELECT DISTINCT cs.TITLE AS STANDARED_TITLE,cs.ID FROM student_standards ss, course_standards cs, report_card_grades rcg, course_periods cp WHERE
                    cs.ID=ss.COURSE_STANDARD_ID
                    AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID
                    AND rcg.id=ss.GRADE_ID
                    AND ss.COURSE_PERIOD_ID=$cp[CP_ID] AND ss.STUDENT_ID=$student[STUDENT_ID]"));
                                            
                                            if(count($standards)>0)
                    { 
                        foreach($standards as $value)
                        {
                             echo '<TR>';
                            
                             echo '<TD class="standard" align="left">'.$value['STANDARED_TITLE'].'</TD>';
                                                    
                            if(count($standards)>0){

                                
						  foreach($mp_name as $mp1)
                                                    {  
                                                      $standards1=DBGet(DBQuery("SELECT ss.MARKING_PERIOD_ID,rcg.TITLE AS GRADE_TITLE FROM student_standards ss, course_standards cs, report_card_grades rcg, course_periods cp WHERE
                    cs.ID=ss.COURSE_STANDARD_ID
                    AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID
                    AND rcg.id=ss.GRADE_ID
                    AND ss.MARKING_PERIOD_ID=".$mp1['MARKING_PERIOD_ID']."
                    AND ss.COURSE_PERIOD_ID=$cp[CP_ID] AND ss.STUDENT_ID=$student[STUDENT_ID] AND ss.COURSE_STANDARD_ID=".$value['ID']));
                                                      
                                                      if(count($standards1)>0)
                                                   { 
                                                    
                                                      echo '<TD class="standard" align="center" width="50px">'.$standards1[1]['GRADE_TITLE'].'</TD>';
                                              
                                                    

                                                   }
                                                  else{ 
                                                    echo '<TD class="standard" align="center" width="50px">&nbsp;</TD>';
                                                  
                                                     
                                                  }
                                                
                                                     } 
                                          

                            }

						  echo '</TR>';
						}
					  }
                                         
                        }
				  $flag++;
				  echo '</TABLE>';
				  
				  
                

				
				
                $comments=DBGet(DBQuery("SELECT srcg.COMMENT, CONCAT(st.TITLE, ' ', st.LAST_NAME) AS TEACHER FROM student_report_card_grades srcg, course_periods cp, staff st WHERE srcg.STUDENT_ID=$student[STUDENT_ID]
                AND srcg.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID
                AND st.STAFF_ID=cp.teacher_id
                AND srcg.SCHOOL_ID=".  UserSchool()." AND srcg.SYEAR=".  UserSyear()));
                echo '<TABLE class="tableGrid" cellpadding="0" cellspacing="0"><TR><TH style="border-bottom:none;"><strong>Notes / Comments</strong></TH></TR></TABLE>';
                echo '<TABLE class="tableGrid" cellpadding="0" cellspacing="0" width="100%" aling="left"><TR><TD>';
                foreach($comments as $comment)
                {
                    echo '<strong>'.$comment['TEACHER'].'</strong>&nbsp;&nbsp;:&nbsp;&nbsp;'.$comment['COMMENT'].'<br/>';
                }
                echo '</TD></TR></TABLE>';
				
				
				echo '<TABLE CELLPADDING="0" CELLSPACING="0" BORDER="0" class="course_period_tbl">';
				echo '<TR><TH align="left" colspan="3">Standards Grade Key</TH></TR>';
				foreach($grade_RET as $grade)
                {
				echo '<TR>';
				echo '<TD class="course_period">'.$grade['TITLE'].'</TD>';
				echo '<TD class="course_period">-</TD>';
				echo '<TD class="course_period">'.(($grade['COMMENT']!='')? $grade['COMMENT']:'&nbsp;').'</TD>';
				echo '</TR>';
				}
				echo '</TABLE>';
				
				
				//echo '<TABLE align="right" border="0" class="tableGrid" cellspacing="0" cellspacing="0">';
//                echo '<TR><TH colspan="2">Grade Legend</TH></TR>';
//                foreach($grade_RET as $grade)
//                {
//                    echo '<TR><TH>'.$grade['TITLE'].'</TH><TD>'.(($grade['COMMENT']!='')? $grade['COMMENT']:'&nbsp;').'</TD></TR>';
//                }
//                echo '</TABLE>';
//				
				
				
                $mp_array=DBGet(DBQuery("SELECT TITLE, SHORT_NAME FROM marking_periods WHERE SCHOOL_ID=".UserSchool()." AND PARENT_ID!=-1 AND SYEAR=".UserSyear()));
                echo '<br/><div style="float:left; background-color:#d3d3d3; padding:3px; border:1px solid #000;">Legend</div>';
                foreach($mp_array as $legend)
                {
                    echo '<div style="float:left; padding:3px; border:1px solid #000; margin-left:2px; margin-bottom:2px;">'.$legend['SHORT_NAME'].' - '.$legend['TITLE'].'</div>';
                }
                echo '<div style="clear:both;"></div>';
                
                echo '<!-- NEW PAGE -->';
                echo "<DIV style=\"page-break-before: always;\"></DIV>";
        
            }
        
            PDFStop($handle);
        }
        else
            echo '<center><font color="red"><b>No Data Found</b></font></center>';
    }
    else
        BackPrompt('You must choose at least one student.');
}
if(!$_REQUEST['modfunc'])
{
	DrawBC("Gradebook >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_openSIS_PDF=true method=POST target=_blank>";
		$attendance_codes = DBGet(DBQuery("SELECT SHORT_NAME,ID FROM attendance_codes WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL) AND TABLE_NAME='0'"));
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
	}
        /******************************MP FILTARATION************************************************/
//        $mps_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,SHORT_NAME FROM MARKING_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
//	$extra['extra_header_left'] .= '<TABLE><TR><TD align=right>Marking Periods</TD><TD><TABLE><TR>';
//	foreach($mps_RET as $mp)
//	{
//            $extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$mp['MARKING_PERIOD_ID'].'>'.$mp['SHORT_NAME'].'</TD>';
//
//	}
//        $extra['extra_header_left'] .= '</TR></TABLE></TD></TABLE>';
	
	
         /*********************************************************************************/
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;
	$extra['WHERE'] .= " AND s.STUDENT_ID IN (SELECT DISTINCT STUDENT_ID FROM student_standards)";

        Widgets('course');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');

	Search('student_id',$extra,'true');
	if($_REQUEST['search_modfunc']=='list')
	{

		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Standard Report Cards for Selected Students\'></CENTER>';
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
