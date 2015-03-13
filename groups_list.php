<?php
include("data.php");
$con=mysql_connect($DatabaseServer,$DatabaseUsername,$DatabasePassword);
$s=mysql_select_db($DatabaseName,$con);
$keyword = $_REQUEST['str'];
if($keyword=="")
    echo "";
else
{
$grpnames=mysql_query("select * from mail_groupmembers where group_id=$keyword") or die(mysql_error());
if(mysql_num_rows($grpnames))
{
    while($row = mysql_fetch_array($grpnames))
    {
        $names[]=$row['user_name'];
        
    }
    echo $values=implode(',',$names);
}
else
    echo "";
}
?>
