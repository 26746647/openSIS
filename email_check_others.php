<?php

include('Redirect_root.php'); 

	include 'Warehouse.php';
	include 'data.php';
        if(isset($_REQUEST['email']) && $_REQUEST['email']!='')
        {
            if($_REQUEST['type']=='3')
            {
                if($_REQUEST['id']==0)
                $result_stu=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX FROM students WHERE EMAIL=\''.$_REQUEST['email'].'\''));
                else
                $result_stu=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX FROM students WHERE EMAIL=\''.$_REQUEST['email'].'\' AND STUDENT_ID!='.$_REQUEST['id']));    

                $result_pe=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX FROM people WHERE EMAIL=\''.$_REQUEST['email'].'\''));
                $result_stf=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX FROM staff WHERE EMAIL=\''.$_REQUEST['email'].'\''));
               
                
//                echo 'SELECT COUNT(1) as EMAIL_EX FROM students WHERE EMAIL=\''.$_REQUEST['email'].'\' AND STUDENT_ID!='.$_REQUEST['id'];
//                echo 'SELECT COUNT(1) as EMAIL_EX FROM people WHERE EMAIL=\''.$_REQUEST['email'].'\'';
//                echo 'SELECT COUNT(1) as EMAIL_EX FROM staff WHERE EMAIL=\''.$_REQUEST['email'].'\'';
//                print_r($result_stu);
//                print_r($result_pe);
//                print_r($result_stf);
                
            }
            if($_REQUEST['type']=='2')
            {
                if($_REQUEST['id']==0)
                $result_stf=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX  FROM staff WHERE EMAIL=\''.$_REQUEST['email'].'\''));
                else
                $result_stf=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX  FROM staff WHERE EMAIL=\''.$_REQUEST['email'].'\' AND STAFF_ID!='.$_REQUEST['id']));    
                
                $result_pe=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX FROM people WHERE EMAIL=\''.$_REQUEST['email'].'\''));
                $result_stf=DBGet(DBQuery('SELECT COUNT(1) as EMAIL_EX FROM staff WHERE EMAIL=\''.$_REQUEST['email'].'\''));
            }
            
            if($result_stf[1]['EMAIL_EX']==0 && $result_pe[1]['EMAIL_EX']==0 && $result_stu[1]['EMAIL_EX']==0 )
            {
                echo '0';
            }
            else
            {
                echo '1';
            }
            exit;
        }
?>
