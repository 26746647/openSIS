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


////////////////////////will be in load_uscommon_core.php//////////////////////////////////
if($_REQUEST['load'])
{    
 set_time_limit(200);
    
$common_core_maths= 'modules/Tools/US_Common_Core_Files/Common Core - Maths.csv';
$common_core_english = 'modules/Tools/US_Common_Core_Files/Common Core - English.csv';


if (($handle = fopen($common_core_maths, "r")) !== FALSE) {
    
    while (($data = fgetcsv($handle,'', ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
             $data[$c];
        }
   $query = 'INSERT INTO us_common_core_standards (subject, grade, course, domain, topic, standard_ref_no, standard_details) VALUES ("'.$data[0].'","'.$data[1].'","'.$data[2].'","'.$data[3].'","'.$data[4].'","'.$data[5].'","'.$data[6].'")';     
    DBQuery($query);    
    }
    fclose($handle);
}


if (($eng_handle = fopen($common_core_english, "r")) !== FALSE) {
    while (($eng_data = fgetcsv($eng_handle,'', ",")) !== FALSE) {
        $eng_num = count($eng_data);
        for ($i=0; $i < $eng_num; $i++) {
             $eng_data[$c];
        }
    $eng_query = 'INSERT INTO us_common_core_standards (subject, grade, course, domain, topic, standard_ref_no, standard_details) VALUES ("'.$eng_data[0].'","'.$eng_data[1].'","'.$eng_data[2].'","'.$eng_data[3].'","'.$eng_data[4].'","'.$eng_data[5].'","'.$eng_data[6].'")';     
    DBQuery($eng_query);    
    }
    fclose($eng_handle);
}

echo '<br/><table><tr><td width="38"><img src="assets/icon_ok.png" /></td><td valign="middle"><span style="font-size:14px;">US Common Core Standards Data is successfully loaded in to database.</span></td></tr></table>';
}
/////////////////////////
else
{    
 
  
  
 //echo '<script type=text/javascript>load_us_common_core();</script>'; 


// $common_core_maths= getcwd().'\modules\Tools\US_Common_Core_Files\Common Core - English.csv';
//echo $common_core_maths=str_replace('\\', '\\\\', $common_core_maths);
//$sql ='LOAD DATA LOCAL INFILE \''.$common_core_maths.'\' INTO TABLE us_common_core_standards FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' ESCAPED BY \'\\\\\' LINES TERMINATED BY \'\r\n\' (subject, grade, course, domain, topic, standard_ref_no, standard_details)';

PopTable('header', 'Load US Common Core Standards'); 

$check_table_data=  DBGet(DBQuery('SELECT * FROM us_common_core_standards'));
if(count($check_table_data)!=0)
    echo '<p style="margin-top:20px; font-size:16px;">US Common Core Standards Data is already loaded into database.</p>';
else
{
   echo '<div id="load_form" >'; 
   echo '<form method=post action=Modules.php?modname='.$_REQUEST[modname].' ><table><tr>'; 
   echo '<td height="30">Click on submit to load US common core standards data into database<td></tr>'; 
   echo '<tr><td align="center"><input align="center" type="submit" name="load"  value="Submit" class=btn_medium onclick="load_us_common_core();" ></td></tr>';
   echo '</table></form>';
   echo '<div>';
   
   echo '<div id="load_common_core" style="display: none; padding-top:20px; padding-bottom:15px;"><img 
src="assets/missing_attn_loader.gif" /><br/><br/><br/><span style="color:#c90000;"><span style=" font-size:15px; font-
weight:bold;">Please wait.</span><br /><span style=" font-size:12px;">Loading US Common Core Standards. Do not click anywhere.</span></span></div>
<div id="load_common_core_result" style="font-size:14px"></div>'; 
   
   
  
}
 PopTable('footer');
}


?>
