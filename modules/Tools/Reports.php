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
ini_set('memory_limit', '120000000000M');
ini_set('max_execution_time','50000000');
if($_REQUEST['func']=='Basic')
{
//    $get_schools=DBGet(DBQuery('SELECT s.ID,s.TITLE,s.PRINCIPAL,st.TYPES,s.DISTRICT,sc.CATEGORY,s.SCH_LVL,s.EMIS_DISTRICT_ADMIN AS DISTRICT_ADMIN FROM schools s,school_types st,school_categories sc WHERE s.SCH_TYPE=st.ID AND s.SCH_CAT_ID=sc.ID'));
//    $columns=array('ID'=>'School Id','PRINCIPAL'=>'Principal','TYPES'=>'School Type','DISTRICT'=>'School District','CATEGORY'=>'Category','SCH_LVL'=>'School Level','DISTRICT_ADMIN'=>'District Admin');
//    ListOutput($get_schools, $columns,'School','Schools');
//    echo "<table cellpadding=5 cellspacing=5 class=alert_box align=center ><tr><td class=notice></td><td class=notice_msg ><b>Statistics</b></td></tr></table>";
    echo '<br/>';
//    echo '<form action="Modules.php?modname='.$_REQUEST[modname].'" method="post">';
//    echo '<b>Select Region : </b><select name="regions" onChange="this.form.submit();">
//        <option value="All" '.($_REQUEST['regions']=='All'? 'selected="selected"':'').'>All Regions</option>
//        <option value="East" '.($_REQUEST['regions']=='East'? 'selected="selected"':'').'>East</option>
//        <option value="West" '.($_REQUEST['regions']=='West'? 'selected="selected"':'').'>West</option>
//        <option value="North" '.($_REQUEST['regions']=='North'? 'selected="selected"':'').'>North</option>
//        <option value="South" '.($_REQUEST['regions']=='South'? 'selected="selected"':'').'>South</option></select>';
//      
//         echo '</form>';    
        
   
    $num_schools=DBGet(DBQuery('SELECT COUNT(ID) as TOTAL_SCHOOLS FROM schools'));
    $num_schools=$num_schools[1]['TOTAL_SCHOOLS'];
//    $primary=DBGet(DBQuery('SELECT COUNT(ID) AS PRI FROM schools WHERE SCH_LVL=\'Primary\''));
//    $primary=$primary[1]['PRI'];
//    $secondary=DBGet(DBQuery('SELECT COUNT(ID) AS SECON FROM schools WHERE SCH_LVL=\'Secondary\''));
//    $secondary=$secondary[1]['SECON'];
    $num_students=DBGet(DBQuery('SELECT COUNT(STUDENT_ID) as TOTAL_STUDENTS FROM students WHERE GENDER IS NOT NULL AND STUDENT_ID IN (SELECT DISTINCT STUDENT_ID FROM student_enrollment WHERE SYEAR='.UserSyear().')'));
    $num_students=$num_students[1]['TOTAL_STUDENTS'];
    $male=DBGet(DBQuery('SELECT COUNT(STUDENT_ID) as MALE FROM students WHERE GENDER=\'Male\' AND STUDENT_ID IN (SELECT DISTINCT STUDENT_ID FROM student_enrollment WHERE SYEAR='.UserSyear().')'));
    $male=$male[1]['MALE'];
    $female=DBGet(DBQuery('SELECT COUNT(STUDENT_ID) as FEMALE FROM students WHERE GENDER=\'Female\' AND STUDENT_ID IN (SELECT DISTINCT STUDENT_ID FROM student_enrollment WHERE SYEAR='.UserSyear().')'));
    $female=$female[1]['FEMALE'];
   
//    if($_REQUEST['regions']=='East' || $_REQUEST['regions']=='West' || $_REQUEST['regions']=='North' || $_REQUEST['regions']=='South')
//    {
//   
//    $primary=DBGet(DBQuery('SELECT COUNT(ID) as PRI FROM schools WHERE DISTRICT=\''.$_REQUEST['regions'].'\' AND SCH_LVL=\'Primary\''));
//    $primary=$primary[1]['PRI'];
//    $id=DBGet(DBQuery('SELECT ID FROM schools where DISTRICT=\''.$_REQUEST['regions'].'\' AND SCH_LVL=\'Primary\''));
//   
//    $students=0;
//    $male=0;$female=0;
//    foreach($id as $i=>$j)
//    {
//        $stu=DBGet(DBQuery('SELECT COUNT(DISTINCT STUDENT_ID) AS NUMBER FROM student_enrollment WHERE SCHOOL_ID='.$j['ID'].' AND SYEAR='.UserSyear().''));
//        $stu_id=DBGet(DBQuery('SELECT DISTINCT STUDENT_ID AS ID FROM student_enrollment WHERE SCHOOL_ID='.$j['ID'].' AND SYEAR='.UserSyear().''));
//        $students=$students+$stu[1]['NUMBER']; 
//        foreach($stu_id as $i=>$j)
//        {
//            $stu_gen=DBGet(DBQuery('SELECT GENDER FROM students WHERE STUDENT_ID='.$j['ID'].' AND GENDER IS NOT NULL'));
//            if($stu_gen[1]['GENDER']=='Female') 
//                $female++;
//            else 
//                $male++;
//       
//        }
//    }
//    $secondary=DBGet(DBQuery('SELECT COUNT(ID) as SECONDARY FROM schools WHERE DISTRICT=\''.$_REQUEST['regions'].'\' AND SCH_LVL=\'Secondary\''));
//    $secondary=$secondary[1]['SECONDARY'];
//    $id1=DBGet(DBQuery('SELECT ID FROM schools where DISTRICT=\''.$_REQUEST['regions'].'\' AND SCH_LVL=\'Secondary\''));
//    
//    $students1=0; 
//    foreach($id1 as $i=>$j)
//    {
//        $stu1=DBGet(DBQuery('SELECT COUNT(DISTINCT STUDENT_ID) AS NUMBER FROM student_enrollment WHERE SCHOOL_ID='.$j['ID'].' AND SYEAR='.UserSyear().''));
//        $students1=$students1+$stu1[1]['NUMBER'];
//        $stu_id1=DBGet(DBQuery('SELECT DISTINCT STUDENT_ID AS ID FROM student_enrollment WHERE SCHOOL_ID='.$j['ID'].' AND SYEAR='.UserSyear().''));
//        foreach($stu_id1 as $i=>$j)
//        {
//            $stu_gen1=DBGet(DBQuery('SELECT GENDER FROM students WHERE STUDENT_ID='.$j['ID'].' AND GENDER IS NOT NULL'));
//
//            if($stu_gen1[1]['GENDER']=='Female') 
//                $female++;
//            else 
//                $male++;
//       
//        }
//    }
//    
//   $num_students=$students+$students1;
//   $num_schools=$primary+$secondary;
//  }
    $num_staff=0;
    $num_teacher=0;
    $num_users=DBGet(DBQuery('SELECT COUNT(DISTINCT s.STAFF_ID) as TOTAL_USER,IF(PROFILE_ID=2,\'Teacher\',\'Staff\') as PROFILEID FROM staff s,staff_school_relationship ssr WHERE s.STAFF_ID=ssr.STAFF_ID AND SYEAR = '.UserSyear().' AND SCHOOL_ID IN (SELECT ID FROM schools ) GROUP BY PROFILEID'));
    
    foreach($num_users as $gt_dt)
    {
        if($gt_dt['PROFILEID']=='Staff')
            $num_staff=$gt_dt['TOTAL_USER'];
        else
            $num_teacher=$gt_dt['TOTAL_USER'];
    }
    
    $num_parent=DBGet(DBQuery('SELECT COUNT(distinct p.STAFF_ID) as TOTAL_PARENTS FROM people p,students_join_people sjp WHERE sjp.PERSON_ID=p.STAFF_ID AND sjp.STUDENT_ID IN (SELECT DISTINCT STUDENT_ID FROM student_enrollment WHERE SYEAR='.UserSyear().')'));
    if($num_parent[1]['TOTAL_PARENTS']=='')
    $num_parent=0;
    else
    $num_parent=$num_parent[1]['TOTAL_PARENTS'];    
    echo '<br/>';
     echo '<div id="d"><TABLE align=center cellpadding=5 cellspacing=5>';
    echo '<tr><td><b>Number of Institutions</b></td><td>:</td><td>&nbsp '.$num_schools.' &nbsp </td></tr>';
    echo '<tr><td><b>Number of Students</b></td><td>:</td><td>&nbsp '.$num_students.' &nbsp </td><td> &nbsp Male : '.$male.' &nbsp| &nbspFemale : '.$female.'</td></tr>';
    echo '<tr><td><b>Number of Teachers</b></td><td>:</td><td colspan=2>&nbsp '.$num_teacher.'</td></tr>';
    echo '<tr><td><b>Number of Staff</b></td><td>:</td><td colspan=2>&nbsp '.$num_staff.'</td></tr>';
    echo '<tr><td><b>Number of Parents</b></td><td>:</td><td colspan=2>&nbsp '.$num_parent.'</td></tr>';
    echo '</TABLE></div>';
    
}

if($_REQUEST['func']=='Ins_r')
{
    if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='save')
    { 
        echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td width=105>".DrawLogo()."</td><td style=\"font-size:15px; font-weight:bold; padding-top:20px;\">Institute Reports</td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			echo "<table >";

      
        $arr=array();
               
             if($_REQUEST['fields'])   
             { $i=0;
                  foreach($_REQUEST['fields'] as $field => $on)
{
                        $columns .= $field.',';
                        $arr[$field]=$field;
                        
                    }
                  
                    $columns=substr($columns,0,-1);
                   foreach($arr as $m=>$n)
    {
//                       if($m=='SCH_TYPE')
//                           $arr[$m]='School Type';
//                            else
//                           if($m=='SCH_CAT_ID')
//                           $arr[$m]='School Category';
//                           else
//                               if($m=='SCH_LVL')
//                                   $arr[$m]='School Level';
//                               else
                                   if($m=='E_MAIL')
                                       $arr[$m]='Email';
                                   else
                                       if($m=='TITLE')
                                       $arr[$m]='School Name';
                                        else
                                        if($m=='REPORTING_GP_SCALE')
                                        $arr[$m]='Base Grading Scale';
                                        else
                                        if($m=='MAIL_ADDRESS')
                                        $arr[$m]='Mailling Address';
                                        else
                                        if($m=='MAIL_CITY')
                                        $arr[$m]='Mailling City';
                                        else
                                        if($m=='MAIL_STATE')
                                        $arr[$m]='Malling State';
                                        else
                                        if($m=='MAIL_ZIP')
                                        $arr[$m]='Malling Zip';
                               else
    {
                               $col=explode('_',$m);
        foreach($col as $col_i=>$col_d)
        {

            $f_c=substr($col_d,0,1);
            $r_c=substr($col_d,1);
            $txt=$f_c.strtolower($r_c);
            unset($f_c);
            unset($r_c);

            $col[$col_i]=$txt;
            unset($txt);
            
        }
        unset($col_i);
        unset($col_d);
        $col=implode(' ',$col);
                                    $arr[$m]=$col;
    }

   }
                        echo '<br>';
               //   echo 'SELECT '.$columns.' FROM schools';
                        $get_school_info=DBGet(DBQuery('SELECT '.$columns.' FROM schools'));
//                      $get_school_info=DBGet(DBQuery('SELECT * FROM schools'));
                       echo '<br>';
                        foreach($get_school_info as $key=>$value)
                        {
                            
                            foreach($value as $i=>$j)
                            {

//                            if($i=='SCH_TYPE')
//                            {
//                                $get_type=DBGet(DBQuery('SELECT st.TYPES as TYPE FROM school_types st,schools s WHERE s.SCH_TYPE=st.ID'));
//                                $get_school_info[$key]['SCH_TYPE']=trim($get_type[1]['TYPE']);
//                                
//                            
//                            }
//                            else if($i=='SCH_CAT_ID')
//                                {
//                            
//                                $get_category=DBGet(DBQuery('SELECT sc.CATEGORY as CATEGORY FROM school_categories sc,schools s WHERE s.SCH_CAT_ID=sc.ID'));
//                                $get_school_info[$key]['SCH_CAT_ID']=trim($get_category[1]['CATEGORY']);
//                            
//                                }
//                                else
//                                {
                                    $get_school_info[$key][$i]=trim($j);
//                                }
                                
                            }  
                            
                        }
  
 echo "<html><link rel='stylesheet' type='text/css' href='styles/export.css'><body style=\" font-family:Arial; font-size:12px;\">";
                    ListOutputPrint_Institute_Report($get_school_info,$arr);
                    
                    echo "</body></html>";
             }            
}
  else
  {
    echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&head_html=Institute+Report&modfunc=save&_openSIS_PDF=true method=POST target=_blank>";
	echo '<DIV id=fields_div></DIV>';
	echo '<br/>';
//           $fields_list['Available School Fields'] = array('TITLE'=>'School Name','ADDRESS'=>'Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode','MAIL_ADDRESS'=>'Mailing Address','MAIL_CITY'=>'Mailing City','MAIL_STATE'=>'Mailing state','MAIL_ZIP'=>'Mailing Zip','PHONE'=>'Telephone','FAX_NUMBER'=>'Fax Number','PRINCIPAL'=>'Principal','VICE_PRINCIPAL'=>'Vice Principal','CARETAKER'=>'Caretaker','BURSAR'=>'Bursar','REPORTING_GP_SCALE'=>'Base Grading Scale','E_MAIL'=>'Email','CEEB'=>'CEEB','DISTRICT'=>'District','SCH_CAT_ID'=>'School Category','SCH_LVL'=>'School Level','SCH_TYPE'=>'School Type','WWW_ADDRESS'=>'Website');
             $fields_list['Available School Fields'] = array('TITLE'=>'School Name','ADDRESS'=>'Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode','PHONE'=>'Telephone','PRINCIPAL'=>'Principal','REPORTING_GP_SCALE'=>'Base Grading Scale','E_MAIL'=>'Email','CEEB'=>'Ceeb','WWW_ADDRESS'=>'Website');
 
	echo '<TABLE><TR><TD valign="top" width="300">';
	DrawHeader("<div><a class=big_font><img src=\"themes/Blue/expanded_view.png\" />Select Fields To Generate Report</a></div>",$extra['header_right']);
	PopTable_wo_header('header');
	echo '<TABLE><TR>';
	foreach($fields_list as $category=>$fields)
	{
		
		echo '<TD colspan=2 class=break_headers ><b>'.$category.'<BR></b></TD></TR><TR>';
		foreach($fields as $field=>$title)
		{
			$i++;
			echo '<TD><INPUT type=checkbox onclick="addHTML(\'<LI>'.$title.'</LI>\',\'names_div\',false);addHTML(\'<INPUT type=hidden name=fields['.$field.'] value=Y>\',\'fields_div\',false);addHTML(\'\',\'names_div_none\',true);this.disabled=true">'.$title.'</TD>';
			if($i%2==0)
				echo '</TR><TR>';
		}
		if($i%2!=0)
		{
			echo '<TD></TD></TR><TR>';
			$i++;
		}
	}
	echo '</TR></TABLE>';
	PopTable('footer');
	echo '</TD><TD valign=top>';
	DrawHeader("<div><a class=big_font><img src=\"themes/Blue/expanded_view.png\" />Selected Fields</a></div>",$extra['header_right']);
	echo '<div id="names_div_none" class="error_msg" style="padding:6px 0px 0px 6px;">No fields selected</div><ol id=names_div class="selected_report_list"></ol>';
	echo '</TD></TR></TABLE>';
        echo '<BR><CENTER><INPUT type=submit value=\'Create Report for Institutes\' class=btn_xxlarge></CENTER>';
	echo "</FORM>";
  }
}
if($_REQUEST['func']=='Ins_cf')
{
$get_schools_cf=DBGet(DBQuery('SELECT s.TITLE AS SCHOOL,s.ID,sc.* FROM schools s,school_custom_fields sc WHERE s.ID=sc.SCHOOL_ID ORDER BY sc.SCHOOL_ID'));
foreach($get_schools_cf as $cf_i=>$cf_d)
{
    foreach($cf_d as $cfd_i=>$cfd_d)
    {
        if($cfd_i=='TYPE')
        {
            $fc=substr($cfd_d,0,1);
            $lc=substr($cfd_d,1);
            $cfd_d=strtoupper($fc).$lc;
            $get_schools_cf[$cf_i][$cfd_i]=$cfd_d;
            unset($fc);
            unset($lc);
        }
        if($cfd_i=='SELECT_OPTIONS' && $cf_d['TYPE']!='text')
        {

            for($i=0;$i<strlen($cfd_d);$i++)
            {
                $char=substr($cfd_d,$i,1);
                if(ord($char)=='13')
                $char='<br/>';  
                $new_char[]=$char;    
                
            }
            
            $cfd_d=implode('',$new_char);
            $get_schools_cf[$cf_i][$cfd_i]=$cfd_d;
            unset($char);
            unset($new_char);
        }
        if($cfd_i=='SYSTEM_FIELD' || $cfd_i=='REQUIRED')
        {
            if($cfd_d=='N')
             $get_schools_cf[$cf_i][$cfd_i]='No';    
            if($cfd_d=='Y')
             $get_schools_cf[$cf_i][$cfd_i]='Yes';    
        }
    }
    unset($cfd_i);
    unset($cfd_d);
}
//print_r($get_schools_cf);
foreach($get_schools_cf as $g_i=>$gd)
{
    $gt_fld_v=DBGet(DBQuery('SELECT CUSTOM_'.$gd['ID'].' as FIELD from schools WHERE ID='.$gd['SCHOOL_ID']));
    $get_schools_cf[$g_i]['C_VALUE']=$gt_fld_v[1]['FIELD'];
}

//print_r($get_schools_cf);
$column=array('SCHOOL'=>'School','TYPE'=>'Custom Field Type','TITLE'=>'Custom Field Name','SELECT_OPTIONS'=>'Options','SYSTEM_FIELD'=>'System Field','REQUIRED'=>'Required Field');
//$columns=array('ID'=>'School Id','TITLE'=>'School Name','ADDRESS'=>'Address','ZIPCODE'=>'Postal Code','PHONE'=>'Phone','FAX_NUMBER'=>'Fax','E_MAIL'=>'Email','WWW_ADDRESS'=>'Website','PRINCIPAL'=>'Principal','CATEGORY'=>'Status','SCH_LVL'=>'School Level','EMIS_DISTRICT_ADMIN'=>'District Admin');
//    ListOutput($get_schools, $columns,'School','Schools');
//        ListOutputPrint_Report($get_schools,$columns);
//echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
//echo "<tr><td width=105>".DrawLogo()."</td><td style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Advanced Report</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
//echo "<table >";
//echo "<html><link rel='stylesheet' type='text/css' href='styles/export.css'><body style=\" font-family:Arial; font-size:12px;\">";
echo '<div style="width:850px; overflow:auto">';
ListOutput($get_schools_cf,$column,'Custom Field','Custom Fields');
echo '</div>';
//echo "</body></html>";
    
}

?>