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
$menu['Messaging']['admin'] = array(
						'Messaging/Inbox.php'=>'Inbox',
						'Messaging/Compose.php'=>'Compose',
						'Messaging/SentMail.php'=>'Sent Message',
						'Messaging/Trash.php'=>'Trash', 
//                                                 1=>'Group',
//						'Internal_Messaging/Group.php'=>'Create Group',
//                                               'Internal_Messaging/ManageGroup.php'=>'Manage Group',
//						'Internal_Messaging/AddMember.php'=>'Add Member',
                                                'Messaging/Group.php'=>'Groups'		
					);

$menu['Messaging']['teacher'] = array(
						'Messaging/Inbox.php'=>'Inbox',
						'Messaging/Compose.php'=>'Compose',
						'Messaging/SentMail.php'=>'Sent Message',
						'Messaging/Trash.php'=>'Trash', 
//                                                 1=>'Group',
//						'Internal_Messaging/Group.php'=>'Create Group',
//                                                'Internal_Messaging/ManageGroup.php'=>'Manage Group',
//						'Internal_Messaging/AddMember.php'=>'Add Member to Group',
                                                'Messaging/Group.php'=>'Groups'		
					);

$menu['Messaging']['parent'] = array(
						'Messaging/Inbox.php'=>'Inbox',
						'Messaging/Compose.php'=>'Compose',
						'Messaging/SentMail.php'=>'Sent Message',
						'Messaging/Trash.php'=>'Trash',
//                                                 1=>'Group',
//						'Internal_Messaging/Group.php'=>'Create Group',
//                                                'Internal_Messaging/ManageGroup.php'=>'Manage Group',
//						'Internal_Messaging/AddMember.php'=>'Add Member to Group',
                                                'Messaging/Group.php'=>'Groups'		
					);
$menu['Messaging']['student'] = array(
						'Messaging/Inbox.php'=>'Inbox',
						'Messaging/Compose.php'=>'Compose',
						'Messaging/SentMail.php'=>'Sent Message',
						'Messaging/Trash.php'=>'Trash',
//                                                 1=>'Group',
//						'Internal_Messaging/Group.php'=>'Create Group',
//                                                'Internal_Messaging/ManageGroup.php'=>'Manage Group',
//						'Internal_Messaging/AddMember.php'=>'Add Member to Group',
                                                'Messaging/Group.php'=>'Groups'		
					);

//$exceptions['Internal_Messaging'] = array(
//						'Students/Student.php?include=General_Info?student_id=new'=>true,
//						'Students/AssignOtherInfo.php'=>true
//					);
?>
