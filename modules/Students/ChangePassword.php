<?php
include('../../Redirect_modules.php');
DrawBC("School Setup >> ".ProgramTitle());

if((clean_param($_REQUEST['action'],PARAM_ALPHAMOD) == 'update') && (clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Save') && (User('PROFILE')=='parent' || User('PROFILE')=='student'))
{
	$stu_PASS=DBGet(DBQuery('SELECT la.PASSWORD FROM login_authentication la, students s WHERE s.STUDENT_ID=\''.UserStudentId().'\' AND la.USER_ID=s.STUDENT_ID AND la.PROFILE_ID=3'));
	$pass_old=$_REQUEST['old'];
	if($pass_old=="")
	 {
	   $error[] = "Please Type The Password";
	   echo ErrorMessage($error,'Error');
	 }
	 else
	 {
            $column_name= PASSWORD;
            $pass_old= paramlib_validation($column_name,$_REQUEST['old']);
            $pass_new= paramlib_validation($column_name,$_REQUEST['new']);
            
            $pass_retype= paramlib_validation($column_name,$_REQUEST['retype']);
            $pass_old = str_replace("\'","''",md5($pass_old));
            $pass_new = str_replace("\'","''",md5($pass_new));
            $pass_retype = str_replace("\'","''",md5($pass_retype));
	  if($stu_PASS[1]['PASSWORD']==$pass_old)
	   {
	 	if($pass_new==$pass_retype)
		 {
	 	  $sql='UPDATE login_authentication SET PASSWORD=\''.$pass_new.'\' WHERE USER_ID=\''.UserStudentId().'\' AND PROFILE_ID=3 ';
		  DBQuery($sql);
		  $note[] = "Password Sucessfully Changed";
	 	    echo ErrorMessage($note,'note');
		 }
		else
		 {
		 	$error[] = "Please Retype Password";
	 	    echo ErrorMessage($error,'Error');
		 }
	 }
	  else
	   {
	 	$error[] = "Password Does'nt Exist";
	 	echo ErrorMessage($error,'Error');
	 }
	 }
	
}
/*
echo "<FORM name=change_password id=change_password action=Modules.php?modname=$_REQUEST[modname]&action=update method=POST>";
echo "<span id='error' name='error'></span>";
echo "<table width=70%><tr><td><fieldset style='border:1px solid #ccc'><legend><b>Change Password </b></legend><table border=0 cellpadding=4 align=center >";
echo "<tr><td align='right'><strong>Old Password :</strong> </td><td><INPUT type=password class=cell_floating name=old></td></tr>";
echo "<tr><td align='right'><strong>New Password :</strong> </td><td><INPUT type=password class=cell_floating name=new></td></tr>";
echo "<tr><td align='right'><strong>Retype Password :</strong> </td><td><INPUT type=password class=cell_floating name=retype></td></tr>";
echo "</table>";
DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Save' onclick='return change_pass();'></CENTER>");
echo "</FORM>";
*/

echo "<span id='error' name='error'></span>";

PopTable('header','Change Password');
echo "<FORM name=change_password id=change_password action=Modules.php?modname=$_REQUEST[modname]&action=update method=POST>";
echo '<table border=0 width=350px><tr><td><table border=0 cellpadding=4 align=center >';
echo '<tr><td align="right"><strong>Old Password :</strong> </td><td><INPUT type=password class=cell_floating name=old AUTOCOMPLETE = "off"></td></tr>';
echo '<tr><td align="right"><strong>New Password :</strong> </td><td><INPUT type=password id=new_pass class=cell_floating name=new AUTOCOMPLETE = "off" onkeyup=passwordStrength(this.value);passwordMatch();><span id=passwordStrength></span></td></tr>';
echo '<tr><td align="right"><strong>Retype Password :</strong> </td><td><INPUT type=password id=ver_pass class=cell_floating name=retype AUTOCOMPLETE = "off" onkeyup=passwordMatch();><span id=passwordMatch></span></td></tr>';
echo '</table></td></tr></table>';
DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Save' onclick='return change_pass();'></CENTER>");
echo "</FORM>";
PopTable('footer');

?>