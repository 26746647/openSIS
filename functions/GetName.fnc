<?php

function GetNameFromUserName($userName)
{	
    //echo "<br>hello ".$userName;
    $q="Select * from login_authentication where username='$userName'";
//    echo "<br> hello ".$q;
       $userProfile=  DBGet(DBQuery($q));
//       print_r($userProfile);
       $userProfileId=$userProfile[1]['PROFILE_ID'];
       $UserId=$userProfile[1]['USER_ID'];
       if($userProfileId!=3 ||$userProfileId!=4)
       {
           $nameQuery="Select CONCAT(first_name,' ', last_name) name from staff where profile_id=$userProfileId and staff_id=$UserId  ";
       }
       if($userProfileId==3)
       {
           $nameQuery="Select CONCAT(first_name,' ', last_name) name from students where student_id=$UserId  ";
       }
       if($userProfileId==4)
       {
           $nameQuery="Select CONCAT(first_name,' ', last_name) name from people where profile_id=$userProfileId and staff_id=$UserId  ";
       }
       $name=  DBGet(DBQuery($nameQuery));
       $name=$name[1]['NAME'];	
    return $name;
}
?>

