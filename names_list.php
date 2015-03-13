<?php
include("data.php");
$keyword = $_REQUEST['str'];
$con=mysql_connect($DatabaseServer,$DatabaseUsername,$DatabasePassword);
$s=mysql_select_db($DatabaseName,$con);
if($keyword=="")
 echo "";
        else 
        {
//           $sql_staff="SELECT * FROM staff WHERE first_name LIKE '$keyword%' and username IS NOT NULL ORDER BY last_name";
            $sql_staff="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and first_name LIKE '$keyword%' and username IS NOT NULL and login_authentication.profile_id NOT IN(3,4) ORDER BY last_name";
//           $sql_student="SELECT * FROM students WHERE first_name LIKE '$keyword%' and username IS NOT NULL ORDER BY last_name";
            $sql_student="SELECT * FROM login_authentication,students WHERE login_authentication.user_id=students.student_id and first_name LIKE '$keyword%' and username IS NOT NULL and login_authentication.profile_id=3 ORDER BY last_name";
            $sql_people="SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and first_name LIKE '$keyword%' and username IS NOT NULL and login_authentication.profile_id=4 ORDER BY last_name";
           $result_staff = mysql_query($sql_staff) or die(mysql_error());
           $result_student = mysql_query($sql_student) or die(mysql_error());
           $result_people = mysql_query($sql_people) or die(mysql_error());
           // print_r($result_staff);
        
	if(mysql_num_rows($result_staff))
	{
		while($row = mysql_fetch_array($result_staff))
		{
			$str = strtolower($row['last_name'].' '.$row['first_name'].','.$row['username']);
                        if(trim($row['username']!=""))
                            echo '<a id="search'.$row['staff_id'].'" onclick="a(\''.$row['username'].'\')">'.$str.'</a><br>';
                       
		}
	}
	else
		echo "";
        
        
        if(mysql_num_rows($result_student))
	{
		while($row_student = mysql_fetch_array($result_student))
		{
			$str = strtolower($row_student['last_name'].' '.$row_student['first_name'].','.$row_student['username']);
                        if(trim($row_student['username']!=""))
                            echo '<a id="search'.$row_student['student_id'].'" onclick="a(\''.$row_student['username'].'\')">'.$str.'</a><br>';
                     
		}
	}
	else
		echo "";
        
        if(mysql_num_rows($result_people))
	{
		while($row_people = mysql_fetch_array($result_people))
		{
			$str = strtolower($row_people['last_name'].' '.$row_people['first_name'].','.$row_people['username']);
                        if(trim($row_people['username']!=""))
                            echo '<a id="search'.$row_people['staff_id'].'" onclick="a(\''.$row_people['username'].'\')">'.$str.'</a><br>';
                     
		}
	}
	else
		echo "";
        
        
        
        }

        
        
       $pos=strpos($keyword,',');
       $lastpos=strrpos($keyword,',');
       $str1=substr($keyword,$pos+1,strlen($keyword));
       $str2=substr($keyword,$lastpos+1,strlen($keyword));
      if($str2!="")
      {
        if($pos!=0 || $lastpos!=0)
        {
//        $sql_staff="SELECT * FROM staff WHERE (first_name LIKE '$str1%' or first_name LIKE '$str2%') and username IS NOT NULL ORDER BY last_name";
            $sql_staff="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and (first_name LIKE '$str1%' or first_name LIKE '$str2%') and username IS NOT NULL and login_authentication.profile_id NOT IN(3,4) ORDER BY last_name";
//        $sql_student="SELECT * FROM students WHERE (first_name LIKE '$str1%'  or first_name LIKE '$str2%') and username IS NOT NULL ORDER BY last_name";
            $sql_student="SELECT * FROM login_authentication,students WHERE login_authentication.user_id=students.student_id and (first_name LIKE '$str1%'  or first_name LIKE '$str2%') and username IS NOT NULL and login_authentication.profile_id=3 ORDER BY last_name";
            $sql_people="SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and (first_name LIKE '$str1%'  or first_name LIKE '$str2%') and username IS NOT NULL and login_authentication.profile_id=4 ORDER BY last_name";
	$result_staff = mysql_query($sql_staff) or die(mysql_error());
        $result_student = mysql_query($sql_student) or die(mysql_error());
        $result_people = mysql_query($sql_people) or die(mysql_error());
        
	if(mysql_num_rows($result_staff))
	{
		while($row = mysql_fetch_array($result_staff))
		{
			$str = strtolower($row['last_name'].' '.$row['first_name'].','.$row['username']);
                        $newpos=$lastpos+1;  
                        if(trim($row['username']!=""))
                            echo '<a id="search'.$row['staff_id'].'" onclick="b(\''.$newpos.'\',\''.$row['username'].'\');">'.$str.'</a><br>';
                }
	}
	else
		echo "";
        
        
        if(mysql_num_rows($result_student))
	{
		while($row_student = mysql_fetch_array($result_student))
		{
			$str = strtolower($row_student['last_name'].' '.$row_student['first_name'].','.$row_student['username']);
                        $newpos=$lastpos+1; 
                        if(trim($row_student['username']!=""))
                            echo '<a id="search'.$row_student['student_id'].'" onclick="b(\''.$newpos.'\',\''.$row_student['username'].'\')">'.$str.'</a><br>';
                }
	}
	else
		echo "";
        
        if(mysql_num_rows($result_people))
	{
		while($row_student = mysql_fetch_array($result_people))
		{
			$str = strtolower($row_people['last_name'].' '.$row_people['first_name'].','.$row_people['username']);
                        $newpos=$lastpos+1;
                        if(trim($row_people['username']!=""))
			echo '<a id="search'.$row_people['student_id'].'" onclick="b(\''.$newpos.'\',\''.$row_people['username'].'\')">'.$str.'</a><br>';
                }
	}
	else
		echo "";
        
        } 
      }
      else
          echo "";
       
$group_id=mysql_query("select distinct group_id,group_name from mail_group where group_name LIKE '$keyword%'");

if(mysql_num_rows($group_id))
        {
            while($row=mysql_fetch_array($group_id))
            {
                $str=strtolower($row['group_name']);
                $id=$row['group_id'];
                $group=mysql_query("select * from mail_groupmembers where group_id=$id");
                while($r=mysql_fetch_array($group))
                {
                    $name[]=$r['user_name'];
                }
                if(!empty($name) && count($name)>0)
                $username=implode(',',$name);
                echo '<a id="search'.$row['group_id'].'" onclick="a(\''.$str.'\')">'.$str.'</a><br>';
            }
        }
 else {
     echo "";
 }
 
?>
