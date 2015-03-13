function nameslist(textvalue,id)
{
//    var textvalue=document.getElementById("txtToUser").value;
//    if(textvalue)
if(textvalue!="")
    ajax_call('names_list.php?str='+textvalue, namecheck_match, namecheck_unmatch); 
}
 function namecheck_match(data) {
 	var response = data;
if(response!=0)
{    
 document.getElementById("ajax_response").innerHTML=response;
 return true;
  }
else
  {
  return false;
  }
 }
 
 function namecheck_unmatch (err) {
 	alert ("Error: " + err);
 }

 function a(id)
 { 
    document.getElementById("txtToUser").value=id;

    document.getElementById("ajax_response").innerHTML="";
   
}
function b(index,val)
{
   
//   function setCharAt(str,index,chr) {
//    if(index > str.length-1) return str;
//    return str.substr(0,index) + chr + str.substr(index+1);
//   }
 var a=document.getElementById("txtToUser").value;
 var l=a.slice(0,index);
 
 document.getElementById("txtToUser").value=l+val;
// a = setCharAt(a,index+1,val);
//document.getElementById("txtToUser").value=a;
//var x=document.getElementById("txtToUser").value;
//
//document.getElementById("txtToUser").value=y;
    document.getElementById("ajax_response").innerHTML="";
}

function list_of_groups(groupid)
{
    document.getElementById("txtToUser").value=groupid;
    //ajax_call('groups_list.php?str='+groupid, groupcheck_match, groupcheck_unmatch); 
}


function groupcheck_match(data) {
 	var response = data;
if(response!=0)
{    
 document.getElementById("txtToUser").value=response;
 return true;
  }
else
  {
  document.getElementById("txtToUser").value="";
  return false;
  }
 }
 
function groupcheck_unmatch (err) {
 	alert ("Error: " + err);
 }


function groups(value)
{
    document.getElementById("groupname").value=value;
}
function desc(value)
{
    document.getElementById("groupdescription").value=value;
}