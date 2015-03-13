<?php

include('Redirect_root.php'); 

	include 'Warehouse.php';
	include 'data.php';
        
        if(isset($_REQUEST['email']) && $_REQUEST['email']!='')
        {
            if($_REQUEST['p_id']==0)
            $result=DBGet(DBQuery('SELECT STAFF_ID FROM people WHERE EMAIL=\''.$_REQUEST['email'].'\''));
            else
            $result=DBGet(DBQuery('SELECT STAFF_ID FROM people WHERE EMAIL=\''.$_REQUEST['email'].'\' AND STAFF_ID!='.$_REQUEST['p_id']));    
            if(count($result)>0)
            {
                echo '0_'.$_REQUEST['opt'];
            }
            else
            {
                echo '1_'.$_REQUEST['opt'];
            }
            exit;
        }
?>
