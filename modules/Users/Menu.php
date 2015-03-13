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
$menu['Users']['admin'] = array(
//						'Users/User.php'=>'User Info',
//						'Users/User.php&staff_id=new'=>'Add a User',
//						'Users/AddStudents.php'=>'Associate Students with Parents',
						'Users/Preferences.php'=>'Preferences',
                                                1=>'Report',
                                                'Users/UserAdvancedReport.php'=>'Advanced Report',
                                                2=>'Users',
						'Users/User.php'=>'User Info',
//						'Users/User.php&staff_id=new'=>'Add a User',
                                                3=>'Staff',
                                                'Users/Staff.php'=>'Staff Info',
                                                'Users/Staff.php&staff_id=new'=>'Add a Staff',
						4=>'Setup',
						'Users/Profiles.php'=>'Profiles',
						'Users/Exceptions.php'=>'User Permissions',
						'Users/UserFields.php'=>'User Fields',
                                                'Users/Exceptions_staff.php'=>'Staff Permissions',
                                                'Users/StaffFields.php'=>'Staff Fields',
//                                                'Users/UploadUserPhoto.php'=>'Upload Staff\'s Photo',
//						'Users/UploadUserPhoto.php?modfunc=edit'=>'Update Staff\'s Photo',
						5=>'Teacher Programs',
                                                
					);

$menu['Users']['teacher'] = array(
						'Users/Staff.php'=>'General Info',
						'Users/Preferences.php'=>'Preferences'
					);

$menu['Users']['parent'] = array(
						'Users/User.php'=>'General Info',
						'Users/Preferences.php'=>'Preferences'
					);

$exceptions['Users'] = array(
						'Users/User.php?staff_id=new'=>true
					);
?>