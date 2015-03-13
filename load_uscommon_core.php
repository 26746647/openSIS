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

include('Redirect_root.php');
include 'Warehouse.php';
include 'data.php';

set_time_limit(200);
$common_core_maths= 'US_Common_Core_Files/Common Core - Maths.csv';
$common_core_english = 'US_Common_Core_Files/Common Core - English.csv';


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


?>
