
		function formcheck_school_setup_school()
		{
			var sel = document.getElementsByTagName('input');
			for(var i=1; i<sel.length; i++)
			{
				var inp_value = sel[i].value;
				if(inp_value == "")
				{
					var inp_name = sel[i].name;
					if(inp_name == 'values[TITLE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter school name")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[ADDRESS]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter address")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[CITY]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter city")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[STATE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter state")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[ZIPCODE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter zip/postal code")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[PHONE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter phone number")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[PRINCIPAL]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter principal")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[REPORTING_GP_SCALE]')
					{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter base grading scale")+"</font></b>";
						return false;
					}
					
				}
				else if(inp_value != "")
				{
					var val = inp_value;
					var inp_name1 = sel[i].name;
					if(inp_name1 == 'values[TITLE]')
					{
                                            if(val.length>50)
                                            {
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Max length for school name is 50")+"</font></b>";
						return false;
                                            }
					}
					if(inp_name1 == 'values[ZIPCODE]')
					{
					
						var charpos = val.search("[^a-zA-Z0-9-\(\)\, ]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter a valid zip/postal code.")+"</font></b>";
							return false;
						}
					}
					if(inp_name1 == 'values[PHONE]')
					{
					
						var charpos = val.search("[^0-9-\(\)\, ]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter a valid phone number.")+"</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[REPORTING_GP_SCALE]')
					{
					
						var charpos = val.search("[^0-9.]");
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter decimal value only.")+"</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[E_MAIL]')
					{
						var emailRegxp = /^(.+)@(.+)$/;
						if (emailRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter a valid email address.")+"</font></b>";
							return false;
						}
					}
					/*else if(inp_name1 == 'values[WWW_ADDRESS]')
					{
						var urlRegxp = /^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([\w]+)(.[\w]+){1,2}$/;
						if (urlRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter a Valid url.")+"</font></b>";
							return false;
						}
					}*/
                            //	frmvalidator.addValidation("values[address][SEC_FIRST_NAME]","req","Please enter secondary emergency contact frist name ");	
//	
//	frmvalidator.addValidation("values[address][SEC_LAST_NAME]","req","Please enter  secondary emergency contact last name");	
//	
//        frmvalidator.addValidation("values[address][SEC_EMAIL]","email","Please enter a valid email");
//        
//	frmvalidator.addValidation("values[students_join_people][STUDENT_RELATION]","req","Relation");
//	
//	
//	
//	frmvalidator.addValidation("values[people][FIRST_NAME]","req","Please enter first name");		
//	
//	frmvalidator.addValidation("values[people][LAST_NAME]","req","Please enter last name");		
//
//
//
// 	frmvalidator.addValidation("values[address][ADDRESS]","req","Please enter address");
//	frmvalidator.addValidation("values[address][PHONE]","ph","Please enter a valid phone number");
//	
//	frmvalidator.addValidation("values[people][FIRST_NAME]","alphabetic","first name allows only alphabetic value");
//	frmvalidator.addValidation("values[people][LAST_NAME]","alpha","last name allows only alphabetic value");
				}
			}
                        return true;
//			document.school.submit();
		}


	function formcheck_school_setup_portalnotes()
	{
	
		var frmvalidator  = new Validator("F2");
		var count=document.getElementById("count_note").value.trim();
                if(count!=0)
                {
                    for(var i=1;i<=count+1;i++)
                    {
                        frmvalidator.addValidation("values["+i+"][TITLE]","alphanumeric", "Title allows only alphanumeric value");
		frmvalidator.addValidation("values["+i+"][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("values["+i+"][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values["+i+"][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
                    }
                }
		frmvalidator.addValidation("values[new][TITLE]","alphanumeric", "Title allows only alphanumeric value");
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
		frmvalidator.setAddnlValidationFunction("ValidateDate_Portal_Notes");

	
	}
	
	
	function formcheck_student_advnc_srch()
	{
	
	var day_to=  $('day_to_birthdate');
    var month_to=  $('month_to_birthdate');
	var day_from=  $('day_from_birthdate');
    var month_from=  $('month_from_birthdate');
	if(!day_to.value && !month_to.value && !day_from.value && !month_from.value ){
		return true;
		}
    if(!day_to.value || !month_to.value || !day_from.value || !month_from.value )
		{ 
		strError="Please provide birthday to day, to month, from day, from month.";
	document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";return false;
		}	
				 				strError="To date must be equal to or greater than from date.";	

								if(month_from.value > month_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";                   
                                return false;
    							}else if(month_from.value == month_to.value && day_from.value > day_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";
                                return false;
    							}return true;
                                    
	
	}
	
		
	function ValidateDate_Portal_Notes()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F2"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))

		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the grade posting start date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}



	function formcheck_school_setup_marking(){

  	var frmvalidator  = new Validator("marking_period");
  	frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
	
	frmvalidator.addValidation("tables[new][SHORT_NAME]","req","Please enter the short name");
  	frmvalidator.addValidation("tables[new][SHORT_NAME]","maxlen=10", "Max length for short name is 10 characters");
	
	frmvalidator.addValidation("tables[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
  	frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "Enter only numeric value");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_Marking_Periods");
}

function ValidateDate_Marking_Periods()
{
var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey, grd ;
var frm = document.forms["marking_period"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}


if(elem[i].name=="month_tables[new][POST_START_DATE]")
{
psm=elem[i];
}
if(elem[i].name=="day_tables[new][POST_START_DATE]")
{
psd=elem[i];
}
if(elem[i].name=="year_tables[new][POST_START_DATE]")
{
psy=elem[i];
}


if(elem[i].name=="month_tables[new][POST_END_DATE]")
{
pem=elem[i];
}
if(elem[i].name=="day_tables[new][POST_END_DATE]")
{
ped=elem[i];
}
if(elem[i].name=="year_tables[new][POST_END_DATE]")
{
pey=elem[i];
}

if(elem[i].name=="tables[new][DOES_GRADES]")
{
grd=elem[i];
}

}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the start date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDate(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}

if (true==validate_chk(grd))
{

try
{  
   if (false==isDate(psm, psd, psy))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the grade posting start date."+"</font></b>";
   psm.focus();
   return false;
   }
}   
catch(err)
{

}

try
{  
   if (true==isDate(pem, ped, pey))
   {
   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
   {
   pem.focus();
   return false;
   }
   }

}   
catch(err)
{

}






try
{
   if (false==CheckDateMar(sm, sd, sy, psm, psd, psy))
   {
	   psm.focus();
	   return false;
   }
}
catch(err)
{

}



}




   return true;
}



function formcheck_school_setup_copyschool()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the new school's title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for title is 100 characters");
}

function formcheck_school_specific_standards()
{
	var frmvalidator  = new Validator("sss");   
        var count=document.getElementById("count_standard").value.trim();       
            for(var i=1;i<=count;i++)
            {
                frmvalidator.addValidation("values["+i+"][STANDARD_REF_NO]","req", "Please enter Ref Number");
                frmvalidator.addValidation("values["+i+"][STANDARD_REF_NO]","maxlen=100", "Max length for Ref Number is 100 characters");
                frmvalidator.addValidation("values["+i+"][DOMAIN]","req", "Please enter domain");
                frmvalidator.addValidation("values["+i+"][GRADE]","req","Please select the grade");  
                frmvalidator.addValidation("values["+i+"][DOMAIN]","maxlen=100", "Max length for Domain is 100 characters");
                frmvalidator.addValidation("values["+i+"][TOPIC]","maxlen=100", "Max length for Topic is 100 characters");      
            }  
            var topic=document.getElementById("values[new][TOPIC]").value.trim();
           // var ref_no=document.getElementById("values[new][STANDARD_REF_NO]").value.trim();
            var details=document.getElementById("values[new][STANDARD_DETAILS]").value.trim();
            if(topic!='' ||  details!='')
            {
                frmvalidator.addValidation("values[new][STANDARD_REF_NO]","req", "Please enter Ref Number");
                frmvalidator.addValidation("values[new][STANDARD_REF_NO]","maxlen=100", "Max length for Ref Number is 100 characters");
                frmvalidator.addValidation("values[new][GRADE]","req","Please select the grade");       
                frmvalidator.addValidation("values[new][DOMAIN]","req", "Please enter domain");
                frmvalidator.addValidation("values[new][DOMAIN]","maxlen=100", "Max length for Domain is 100 characters");
                frmvalidator.addValidation("values[new][TOPIC]","maxlen=100", "Max length for Topic is 100 characters");
            }
}

function formcheck_school_setup_calender()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for title is 100");
        frmvalidator.setAddnlValidationFunction("ValidateDate_SchoolSetup_calender");
}


function ValidateDate_SchoolSetup_calender()
{    
//var sd,sm,sy,ed,em,ey ;
var frm = document.forms["prompt_form"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{
if(elem[i].name=="month_min")
{
sm=elem[i];
}
if(elem[i].name=="day_min")
{
sd=elem[i];
}
if(elem[i].name=="year_min")
{
sy=elem[i];
}

if(elem[i].name=="month_max")
{
em=elem[i];
}
if(elem[i].name=="day_max")
{
ed=elem[i];
}
if(elem[i].name=="year_max")
{
ey=elem[i];
}
}
switch (sm.value) {
    case 'JAN':
        s_m = "1";
        break;
    case 'FEB':
        s_m = "2";
        break;
    case 'MAR':
        s_m = "3";
        break;
    case 'APR':
        s_m = "4";
        break;
   case 'MAY':
        s_m = "5";
        break;
    case 'JUN':
        s_m = "6";
        break;
    case 'JUL':
        s_m = "7";
        break;
    case 'AUG':
        s_m = "8";
        break;  
    case 'SEP':
        s_m = "9";
        break;
    case 'OCT':
        s_m = "10";
        break;
    case 'NOV':
        s_m = "11";
        break;
    case 'DEC':
        s_m = "12";
        break;
} 

try
{
    var s=s_m+"/"+sd.value+"/"+sy.value;
    
if (false==validatedate(s))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter correct start date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
switch (em.value) {
    case 'JAN':
        e_m = "1";
        break;
    case 'FEB':
        e_m = "2";
        break;
    case 'MAR':
        e_m = "3";
        break;
    case 'APR':
        e_m = "4";
        break;
   case 'MAY':
        e_m = "5";
        break;
    case 'JUN':
        e_m = "6";
        break;
    case 'JUL':
        e_m = "7";
        break;
    case 'AUG':
        e_m = "8";
        break;  
    case 'SEP':
        e_m = "9";
        break;
    case 'OCT':
        e_m = "10";
        break;
    case 'NOV':
        e_m = "11";
        break;
    case 'DEC':
        e_m = "12";
        break;
} 
try
{  
    var e=e_m+"/"+ed.value+"/"+ey.value;
   if (false==validatedate(e))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter correct end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
 var starDate = new Date(s);
 var endDate = new Date(e);
if (starDate > endDate && endDate!='')
{
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Start date cannot be greater than end date."+"</font></b>";
  return false;
}
//try
//{
//   if (false==CheckDateGoal(sm, sd, sy, em, ed, ey))
//   {
//   em.focus();
//   return false;
//   }
//}
//catch(err)
//{
//
//}
//
//try
//{
//   if (false==CheckValidDateGoal(sm, sd, sy, em, ed, ey))
//   {
//   sm.focus();
//   return false;
//   }
//}
//catch(err)
//{
//
//}
return true;  
}
function validatedate(inputText)  
  { 
  var dateformat = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4}$/;  
  // Match the date format through regular expression  
  if(inputText.match(dateformat))  
  {  
  //document.form1.text1.focus();  
  //Test which seperator is used '/' or '-'  
  var opera1 = inputText.split('/');  
  var opera2 = inputText.split('-');  
  lopera1 = opera1.length;  
  lopera2 = opera2.length;  
  // Extract the string into month, date and year  
  if (lopera1>1)  
  {  
  var pdate = inputText.split('/');  
  }  
  else if (lopera2>1)  
  {  
  var pdate = inputText.split('-');  
  }  
  var mm  = parseInt(pdate[0]);  
  var dd = parseInt(pdate[1]);  
  var yy = parseInt(pdate[2]);  
  // Create list of days of a month [assume there is no leap year by default]  
  var ListofDays = [31,28,31,30,31,30,31,31,30,31,30,31];  
  if (mm==1 || mm>2)  
  {  
  if (dd>ListofDays[mm-1])  
  {  
  //alert('Invalid date format!');  
  return false;  
  }  
  }  
  if (mm==2)  
  {  
  var lyear = false;  
  if ( (!(yy % 4) && yy % 100) || !(yy % 400))   
  {  
  lyear = true;  
  }  
  if ((lyear==false) && (dd>=29))  
  {  
  //alert('Invalid date format!');  
  return false;  
  }  
  if ((lyear==true) && (dd>29))  
  {  
  //alert('Invalid date format!');  
  return false;  
  }  
  }  
  }  
  else  
  {  
  //alert("Invalid date format!");  
  //document.form1.text1.focus();  
  return false;  
  }  
  }  


function formcheck_staff_staff(staff_school_chkbox_id)
{
  	var frmvalidator  = new Validator("staff");
        frmvalidator.addValidation("staff[TITLE]","req","Please enter the Salutation");
  	frmvalidator.addValidation("staff[FIRST_NAME]","req","Please enter the First Name");
	frmvalidator.addValidation("staff[LAST_NAME]","req","Please enter the Last Name");
	frmvalidator.addValidation("staff[GENDER]","req","Please select Gender");
        frmvalidator.setAddnlValidationFunction("ValidateDate_Staff");
        frmvalidator.addValidation("staff[ETHNICITY_ID]","req","Please select Ethnicity");
        frmvalidator.addValidation("staff[PRIMARY_LANGUAGE_ID]","req","Please select Primary language");
        frmvalidator.addValidation("staff[SECOND_LANGUAGE_ID]","req","Please select Secondary language");

        frmvalidator.addValidation("values[ADDRESS][STAFF_ADDRESS1_PRIMARY]","req","Please enter Street address 1");
        frmvalidator.addValidation("values[ADDRESS][STAFF_CITY_PRIMARY]","req","Please enter City");
        frmvalidator.addValidation("values[ADDRESS][STAFF_STATE_PRIMARY]","req","Please enter State");
        frmvalidator.addValidation("values[ADDRESS][STAFF_ZIP_PRIMARY]","req","Please enter Street Zip");
		
		frmvalidator.addValidation("values[ADDRESS][STAFF_ZIP_PRIMARY]","numeric", "Zip allows only numeric value");
		
        frmvalidator.addValidation("values[CONTACT][STAFF_HOME_PHONE]","req","Please enter Home Phone");
        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_PHONE]","req","Please enter Office Phone");
        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_EMAIL]","req","Please enter Work email");
        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_EMAIL]","email","Please enter Work email in proper format");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_FIRST_NAME]","req","Please enter Emergency First Name");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_LAST_NAME]","req","Please enter Emergency Last Name");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_RELATIONSHIP]","req","Please select Relationship to Staff");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_HOME_PHONE]","req","Please enter Emergency Home Phone");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_WORK_PHONE]","req","Please enter Emergency Work Phone");

        frmvalidator.addValidation("month_values[JOINING_DATE]","req","Please select Joining Date");
        frmvalidator.addValidation("day_values[JOINING_DATE]","req","Please select Joining Date");
        frmvalidator.addValidation("year_values[JOINING_DATE]","req","Please select Joining Date");
        
//        if(!document.getElementById('noaccs')||!document.getElementById('noaccs').checked){
//
//                 frmvalidator.addValidation("values[SCHOOL][OPENSIS_PROFILE]","req","Please select A Profile");
//		 frmvalidator.addValidation("values[SCHOOL][USER_ID]","req","Please Enter User Id");
//		 frmvalidator.addValidation("values[SCHOOL][PASSWORD]","req","Please Enter Password");
//		 return school_check_staff(staff_school_chkbox_id);
//                }
               return school_check(staff_school_chkbox_id);


}

function formcheck_school_setup_periods()
{
  	var frmvalidator  = new Validator("F1");
        var count=document.getElementById("count").value.trim();
        if(count!=0)
        {
            for(var i=0;i<=count;i++)
            {
                frmvalidator.addValidation("inputvalues["+i+"][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("inputvalues["+i+"][SHORT_NAME]","maxlen=50", "Max length for short name is 50 characters");
		
		frmvalidator.addValidation("inputvalues["+i+"][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("inputvalues["+i+"][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
		frmvalidator.addValidation("inputvalues["+i+"][START_HOUR]","req","Please select start time");
		frmvalidator.addValidation("inputvalues["+i+"][START_MINUTE]","req","Please select start time");
		frmvalidator.addValidation("inputvalues["+i+"][START_M]","req","Please select start time");
		
		frmvalidator.addValidation("inputvalues["+i+"][END_HOUR]","req","Please select end time");
		frmvalidator.addValidation("inputvalues["+i+"][END_MINUTE]","req","Please select end time");
		frmvalidator.addValidation("inputvalues["+i+"][END_M]","req","Please select end time");
	
            }
        }

	var p_name = document.getElementById('values[new][TITLE]');
	var p_name_val = p_name.value;
	
	if(p_name_val != "")
	{
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50 characters");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
		frmvalidator.addValidation("values[new][START_HOUR]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_MINUTE]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_M]","req","Please select start time");
		
		frmvalidator.addValidation("values[new][END_HOUR]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_MINUTE]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_M]","req","Please select end time");
	} 
	
}


function formcheck_school_setup_grade_levels()
{
		var frmvalidator  = new Validator("F1");
		
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50 characters");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
}


function formcheck_student_student()
{

  	var frmvalidator  = new Validator("student");
        frmvalidator.clearAllValidations();
  	frmvalidator.addValidation("students[FIRST_NAME]","req","Please enter the first name");
	frmvalidator.addValidation("students[FIRST_NAME]","maxlen=100", "Max length for school name is 100 characters");
	
	frmvalidator.addValidation("students[LAST_NAME]","req","Please enter the last name");
	frmvalidator.addValidation("students[LAST_NAME]","maxlen=100", "Max length for address is 100 characters");
//        frmvalidator.addValidation("students[GENDER]","req","Please select gender");
//        frmvalidator.addValidation("students[ETHNICITY]","req","Please select ethnicity");
	
	frmvalidator.addValidation("assign_student_id","num", "Student ID allows only numeric value");


        
  	frmvalidator.addValidation("values[student_enrollment][new][GRADE_ID]","req","Please select a grade");
	
	frmvalidator.addValidation("students[USERNAME]","maxlen=50", "Max length for Username is 50");
        frmvalidator.addValidation("students[PASSWORD]","password=8", "Password should be minimum 8 characters with atleast one special character and one number");
	frmvalidator.addValidation("students[PASSWORD]","maxlen=20", "Max length for password is 20 characters");
	frmvalidator.addValidation("students[EMAIL]","email","Please enter a valid email");
	frmvalidator.addValidation("students[PHONE]","phone","Invalid phone number");
	
	
  	if(document.getElementById('cal_stu_id'))
        {
            var cal_stu_id=document.getElementById('cal_stu_id').value;
            frmvalidator.addValidation("values[student_enrollment]["+cal_stu_id+"][CALENDAR_ID]","req","Please select calendar");
        }
	
	
	frmvalidator.addValidation("values[student_enrollment][new][NEXT_SCHOOL]","req","Please select rolling / retention options");
	
//	frmvalidator.addValidation("values[address][ADDRESS]","req","Please enter address");
//	
//	frmvalidator.addValidation("values[address][CITY]","req","Please enter city");
//	
//	frmvalidator.addValidation("values[address][STATE]","req","Please enter state");
//		
//	frmvalidator.addValidation("values[address][ZIPCODE]","req","Please enter zipcode");	
//	
//	frmvalidator.addValidation("values[address][PRIM_STUDENT_RELATION]","req","Relation");
//
//	frmvalidator.addValidation("values[address][PRI_FIRST_NAME]","req","Please enter first name");	
//	
//	frmvalidator.addValidation("values[address][PRI_LAST_NAME]","req","Please enter last name");
//
//        frmvalidator.addValidation("values[address][EMAIL]","email","Please enter a valid email");
//	
//	frmvalidator.addValidation("values[address][SEC_STUDENT_RELATION]","req","Please enter secondary relation");
//	
//	frmvalidator.addValidation("values[address][SEC_FIRST_NAME]","req","Please enter secondary emergency contact frist name ");	
//	
//	frmvalidator.addValidation("values[address][SEC_LAST_NAME]","req","Please enter  secondary emergency contact last name");	
//	
//        frmvalidator.addValidation("values[address][SEC_EMAIL]","email","Please enter a valid email");
//        
//	frmvalidator.addValidation("values[students_join_people][STUDENT_RELATION]","req","Relation");
//	
//	
//	
//	frmvalidator.addValidation("values[people][FIRST_NAME]","req","Please enter first name");		
//	
//	frmvalidator.addValidation("values[people][LAST_NAME]","req","Please enter last name");		
//
//
//
// 	frmvalidator.addValidation("values[address][ADDRESS]","req","Please enter address");
//	frmvalidator.addValidation("values[address][PHONE]","ph","Please enter a valid phone number");
//	
//	frmvalidator.addValidation("values[people][FIRST_NAME]","alphabetic","first name allows only alphabetic value");
//	frmvalidator.addValidation("values[people][LAST_NAME]","alpha","last name allows only alphabetic value");
	
//        if(document.getElementById('pri_person_id'))
//        {
            frmvalidator.addValidation("values[student_address][HOME][ADDRESS]","req","Please enter address");

            frmvalidator.addValidation("values[student_address][HOME][CITY]","req","Please enter city");

            frmvalidator.addValidation("values[student_address][HOME][STATE]","req","Please enter state");

            frmvalidator.addValidation("values[student_address][HOME][ZIPCODE]","req","Please enter zipcode");	

            frmvalidator.addValidation("values[people][PRIMARY][RELATIONSHIP]","req","Please select primary relation");

            frmvalidator.addValidation("values[people][PRIMARY][FIRST_NAME]","req","Please enter first name");	

            frmvalidator.addValidation("values[people][PRIMARY][LAST_NAME]","req","Please enter last name");
            frmvalidator.addValidation("values[people][PRIMARY][EMAIL]","req","Please enter a  email");

            frmvalidator.addValidation("values[people][PRIMARY][EMAIL]","email","Please enter a valid email");
            frmvalidator.addValidation("val_email_1","req","Please enter a  new email");

            if(document.getElementById('portal_1').checked==true )
            {
                    frmvalidator.addValidation("values[people][PRIMARY][USER_NAME]","req","Please enter username");

                    frmvalidator.addValidation("values[people][PRIMARY][PASSWORD]","req","Please enter password");
                    frmvalidator.addValidation("val_pass","req","This password is already taken.");

            }
            frmvalidator.addValidation("values[people][SECONDARY][RELATIONSHIP]","req","Please enter secondary relation");

            frmvalidator.addValidation("values[people][SECONDARY][FIRST_NAME]","req","Please enter secondary emergency contact frist name ");	

            frmvalidator.addValidation("values[people][SECONDARY][LAST_NAME]","req","Please enter  secondary emergency contact last name");	
            frmvalidator.addValidation("values[people][SECONDARY][EMAIL]","req","Please enter a email");
            frmvalidator.addValidation("values[people][SECONDARY][EMAIL]","email","Please enter a valid email");
            frmvalidator.addValidation("val_email_2","req","Please enter a  new email");
            if(document.getElementById('portal_2').checked==true)
            {
                    frmvalidator.addValidation("values[people][SECONDARY][USER_NAME]","req","Please enter username");

                    frmvalidator.addValidation("values[people][SECONDARY][PASSWORD]","req","Please enter password");
                    frmvalidator.addValidation("val_pass","req","This password is already taken.");
            }
        //}
        if(document.getElementById('oth_person_id'))
        {
            frmvalidator.addValidation("values[people][OTHER][RELATIONSHIP]","req","Please select relation");



            frmvalidator.addValidation("values[people][OTHER][FIRST_NAME]","req","Please enter first name");		

            frmvalidator.addValidation("values[people][OTHER][LAST_NAME]","req","Please enter last name");		
            frmvalidator.addValidation("values[people][OTHER][EMAIL]","req","Please enter a email");
            frmvalidator.addValidation("values[people][OTHER][EMAIL]","email","Please enter a valid email");
            frmvalidator.addValidation("val_email_2","req","Please enter a new email");

            if(document.getElementById("ron").value=='Y')
            frmvalidator.addValidation("values[student_address][OTHER][ADDRESS]","req","Please enter address");


//            frmvalidator.addValidation("values[student_address][OTHER][FIRST_NAME]","alphabetic","first name allows only alphabetic value");
//            frmvalidator.addValidation("values[student_address][OTHER][LAST_NAME]","alpha","last name allows only alphabetic value");
        
        }
	frmvalidator.addValidation("students[PHYSICIAN]","req","Please enter the physician name");
	
	frmvalidator.addValidation("students[PHYSICIAN_PHONE]","ph","Phone number cannot not be alphabetic.");
	
	
 	frmvalidator.addValidation("tables[goal][new][GOAL_TITLE]","req","Please enter goal title");
        frmvalidator.addValidation("tables[goal][new][GOAL_TITLE]","req","Please enter goal title");

	frmvalidator.addValidation("tables[goal][new][GOAL_DESCRIPTION]","req","Please enter goal description");
	
	
 	frmvalidator.addValidation("tables[progress][new][PROGRESS_NAME]","req","Please enter progress period name");
	frmvalidator.addValidation("tables[progress][new][PROFICIENCY]","req","Please select proficiency scale");
	frmvalidator.addValidation("tables[progress][new][PROGRESS_DESCRIPTION]","req","Please enter progress assessment");
	
	
            frmvalidator.setAddnlValidationFunction("ValidateDate_Student");
        

}

function change_pass()
 {	
 	
	var frmvalidator  = new Validator("change_password");
	frmvalidator.addValidation("old","req","Please enter old password");
	frmvalidator.addValidation("new","req","Please enter new password");
	frmvalidator.addValidation("retype","req","Please retype password");
        frmvalidator.addValidation("new","password=8","Password should be minimum 8 characters with atleast one special character and one number");
	
		
 }

function ValidateDate_Student()
{
    

var bm, bd, by ;
var frm = document.forms["student"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_students[BIRTHDATE]")
{
bm=elem[i];
}
if(elem[i].name=="day_students[BIRTHDATE]")
{
bd=elem[i];
}
if(elem[i].name=="year_students[BIRTHDATE]")
{
by=elem[i];
}


}

for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}



}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter start date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDateGoal(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}
//-----
try
{
   if (false==CheckValidDateGoal(sm, sd, sy, em, ed, ey))
   {
   sm.focus();
   return false;
   }
}
catch(err)
{

}


try
{
if (false==CheckBirthDate(bm, bd, by))
   {
   bm.focus();
   return false;
   }
}
catch(err)
{

}

for(var z = 0; z < elem.length; z++)
{
if(elem[z].name=="students[FIRST_NAME]")
{
var firstnameobj = elem[z];
var firstname =elem[z].value;
}
if(elem[z].name=="students[MIDDLE_NAME]")
{
var middlenameobj  = elem[z];   
var middlename =elem[z].value;
}
if(elem[z].name=="students[LAST_NAME]")
{
 var lastnameobj =    elem[z];
var lastname =elem[z].value;
}
if(elem[z].name=="values[student_enrollment][new][GRADE_ID]")
{
  var gradeobj=  elem[z];
var grade =elem[z].value;
}
var studentbirthday_year = by.value;
var studentbirthday_month = bm.value;
var studentbirthday_day = bd.value;
}
if(firstnameobj && middlenameobj && lastnameobj && gradeobj && by && bm && bd)
{    
ajax_call('check_duplicate_student.php?fn='+firstname+'&mn='+middlename+'&ln='+lastname+'&gd='+grade+'&byear='+studentbirthday_year+'&bmonth='+studentbirthday_month+'&bday='+studentbirthday_day, studentcheck_match, studentcheck_unmatch); 
   return false;
}
else
   return true;  
}

function studentcheck_match(data) {
 	var response = data;
if(response!=0)
{    
 var result = confirm("Duplicate student found. There is already a student with the same information. Do you want to proceed?");
if(result==true)
  {
  document.getElementById("student_isertion").submit();
  return true;
  }
else
  {
  return false;
  }
 }
 else
 {    
   document.getElementById("student_isertion").submit();
   return true;
 }
 }
 
 function studentcheck_unmatch (err) {
 	alert ("Error: " + err);
 }
   




	function formcheck_student_studentField_F2()
	{
		var frmvalidator  = new Validator("F2");
                var t_id=document.getElementById('t_id').value;
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","req","Please enter the title");
                frmvalidator.addValidation("tables["+t_id+"][TITLE]","maxlen=50","Max length for title is 50");
//		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	



	function formcheck_student_studentField_F1()
	{
		var frmvalidator  = new Validator("F1");
                 var f_id=document.getElementById('f_id').value;
		frmvalidator.addValidation("tables["+f_id+"][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables["+f_id+"][TYPE]","req","Please select the data type");
		
		frmvalidator.addValidation("tables["+f_id+"][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	
                    function formcheck_student_studentField_F1_defalut()
                    {
                           var type=document.getElementById('type');
                           if(type.value=='textarea')
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=true;
                           else
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=false;
                    }

///////////////////////////////////////// Student Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Address Field Start //////////////////////////////////////////////////////////



	function formcheck_student_addressField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	


	function formcheck_student_addressField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



///////////////////////////////////////// Address Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Contact Field Start //////////////////////////////////////////////////////////


	
	function formcheck_student_contactField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	


	function formcheck_student_contactField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



	function formcheck_user_user(staff_school_chkbox_id){
            
        
  	var frmvalidator  = new Validator("staff");
        //frmvalidator.addValidation("month_values[START_DATE]["+1+"]","req","Please Enter start date");
  	frmvalidator.addValidation("people[FIRST_NAME]","req","Please enter the first name");
//	frmvalidator.addValidation("staff[FIRST_NAME]","alphabetic", "First name allows only alphabetic value");
  	frmvalidator.addValidation("people[FIRST_NAME]","maxlen=100", "Max length for first name is 100 characters");
	
		
	frmvalidator.addValidation("people[LAST_NAME]","req","Please enter the Last Name");
//	frmvalidator.addValidation("staff[LAST_NAME]","alphabetic", "Last name allows only alphabetic value");
  	frmvalidator.addValidation("people[LAST_NAME]","maxlen=100", "Max length for Address is 100");
        frmvalidator.addValidation("people[PASSWORD]","password=8", "Password should be minimum 8 characters with one special character and one number");
//	frmvalidator.addValidation("staff[PROFILE]","req","Please select the user profile");
        frmvalidator.addValidation("people[EMAIL]","email", "Please enter a valid email");
//	frmvalidator.addValidation("staff[PHONE]","ph","Please enter a valid telephone number");
//        return school_check(staff_school_chkbox_id);	
        
}
function school_check(staff_school_chkbox_id)
		{
                    //alert(par);
                    //alert(document.getElementById('daySelect11').value);
			var chk='n';
                        var err='T';
			if(staff_school_chkbox_id)
			{
                                    for(i=1;i<=staff_school_chkbox_id;i++)
                                    {
                                        
                                            if(document.getElementById('staff_SCHOOLS'+i).checked==true)
                                            {
                                                    chk='y';
                                                    //alert(document.staff.day_values['START_DATE'][i].value);
                                                    //alert(document.staff)
                                                   sd=document.getElementById('daySelect1'+i).value;
                                                   sm=document.getElementById('monthSelect1'+i).value;
                                                   sy=document.getElementById('yearSelect1'+i).value;

                                                   ed=document.getElementById('daySelect2'+i).value;
                                                   em=document.getElementById('monthSelect2'+i).value;
                                                   ey=document.getElementById('yearSelect2'+i).value;
                                                    //ed=
                                                    //alert(sd+sm+sy);
                                                    //alert(ed+em+ey);
//                                                    if(sm=='' || sd=='' || sy=='')
//                                                        {
//                                                         err='F';
//                                                         break;
//                                                        }
//                                                        else
//                                                        {
                                                            var starDate = new Date(sd+"/"+sm+"/"+sy);
                                                            var endDate = new Date(ed+"/"+em+"/"+ey);
                                                             if (starDate > endDate && endDate!='')
                                                            {
                                                                err='S';

                                                            }
//                                                        }
                                            } 
                                         
                                    }
                              
                                
                        }
				if(chk!='y')
				{
					var d = $('divErr');
					err = "Please assign at least one school to this staff.";
					d.innerHTML="<b><font color=red>"+err+"</font></b>";
					return false;
				}
                                else if(chk=='y')
                                {
//                                    if(err=='F')
//                                    {
//                                      var d = $('divErr');
//                                       var err_date = "Please enter start date to selected school.";
//                                        d.innerHTML="<b><font color=red>"+err_date+"</font></b>";
//                                        return false;
//                                    }
                                    if(err=='S')
                                    {
                                      var d = $('divErr');
                                       var err_stardate = "Start date cannot be greater than end date.";
                                        d.innerHTML="<b><font color=red>"+err_stardate+"</font></b>";
                                        return false;
                                    }
                                    else
                                    {
                                        return true;
                                    }
                                            
                                }
				else
				{
					return true;
				}
			
//                        else
//                            {
//                            var d = $('divErr');
//			    var err = "Please assign at least one school to this new user asd.";
//                            d.innerHTML="<b><font color=red>"+err+"</font></b>";
//                            return false;
//                            }
	    }
/////////////////////////////////////////  Add User End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User Fields Start  //////////////////////////////////////////////////////////

	function formcheck_user_userfields_F2()
	{
		var frmvalidator  = new Validator("F2");
                var t_id=document.getElementById('t_id').value;
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","alphabetic", "Title allows only alphabetic value");
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","maxlen=50", "Max length for title is 100");
	}
	
	function formcheck_user_userfields_F1()
	{
		var frmvalidator1  = new Validator("F1");
                var f_id=document.getElementById('f_id').value;
                
		frmvalidator1.addValidation("tables["+f_id+"][TITLE]","req","Please enter the field Name");
		frmvalidator1.addValidation("tables["+f_id+"][TITLE]","req", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables["+f_id+"][TITLE]","maxlen=50", "Max length for Field Name is 100");
                //frmvalidator1.addValidation("tables[new][SORT_ORDER]","req","Please enter the sort order");
                frmvalidator1.addValidation("tables["+f_id+"][SORT_ORDER]","num", "sort order allows only numeric value");
                
	}
        
        function formcheck_schoolfields()
        {
            var frmvalidator1  = new Validator("SF1");
            var custom_id=document.getElementById('custom').value;
            if(custom_id=='')
            {
		frmvalidator1.addValidation("tables[new][TITLE]","req","Please enter the field name");
		frmvalidator1.addValidation("tables[new][TITLE]","alnum", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables[new][TITLE]","maxlen=50", "Max length for Field Name is 100");
              
                frmvalidator1.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
            }
            else
            {
                frmvalidator1.addValidation("tables["+custom_id+"][TITLE]","req","Please enter the field name");
		frmvalidator1.addValidation("tables["+custom_id+"][TITLE]","alnum", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables["+custom_id+"][TITLE]","maxlen=50", "Max length for Field Name is 100");
              
                frmvalidator1.addValidation("tables["+custom_id+"][SORT_ORDER]","num", "sort order allows only numeric value");
          
            }
        }

/////////////////////////////////////////  User Fields End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User End  ////////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling start ///////////////////////////////////////////////////////

//////////////////////////////////////// Course start ///////////////////////////////////////////////////////

function formcheck_scheduling_course_F4()
{
	var frmvalidator  = new Validator("F4");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","maxlen=100", "Max length for title is 100");
}

function formcheck_scheduling_course_F3()
{
//	var frmvalidator  = new Validator("F3");
//  	frmvalidator.addValidation("tables[courses][new][TITLE]","req","Please enter the title");
//  	frmvalidator.addValidation("tables[courses][new][TITLE]","maxlen=50", "Max length for title is 50");
//	
//  	frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","req","Please enter the short name");
//  	frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","maxlen=10", "Max length for short name is 10");
var frmvalidator  = new Validator("F3");
       // alert(frmvalidator);
       var course_id=document.getElementById('course_id_div').value;
       if(course_id=='new')
    {
  	frmvalidator.addValidation("tables[courses][new][TITLE]","req","Please enter the course name ");
  	frmvalidator.addValidation("tables[courses][new][TITLE]","maxlen=100", "Max length for course is 100 characters ");
        frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","maxlen=50", "Max length for course is 50 characters ");
        
    }
    else
    {
        frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","req","Please enter the course name ");
  	frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","maxlen=100", "Max length for course is 100 characters ");
        frmvalidator.addValidation("inputtables[courses]["+course_id+"][SHORT_NAME]","maxlen=100", "Max length for course is 100 characters ");
 
    }
}

function formcheck_scheduling_course_F2()
{
    var count;
    var check=0;
    if(document.getElementById("get_status").value=='false' && document.getElementById('cp_id').value!='new' && document.getElementById('cp_period'))
    {
        document.getElementById("divErr").innerHTML='<font color="red"><b>Cannot take attendance in this period</b></font>';
//        document.getElementById("cp_period").focus();
        return false;
    }
    for(count=1;count<=7;count++)
    {
       if(document.getElementById("DAYS"+count).checked==true)
         check++;  
    }
    if(check==0)
    {    
     document.getElementById("display_meeting_days_chk").innerHTML='<font color="red">Please select atleast one day</font>';
     document.getElementById("DAYS1").focus();
     return false;
    }
    else if((document.getElementById("cp_use_standards").checked==true) && (document.getElementById("cp_standard_scale").value==""))
    {
     document.getElementById("display_meeting_days_chk").innerHTML='<font color="red">Please select standard grade scale</font>';
     document.getElementById("cp_standard_scale").focus();
     return false;
    }    
    else
    {    
	var frmvalidator  = new Validator("F2");
  	frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","req","Please enter the short name");
  	frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","maxlen=20", "Max length for short name is 20");

  	frmvalidator.addValidation("tables[course_periods][new][TEACHER_ID]","req","Please select the teacher");

  	frmvalidator.addValidation("tables[course_periods][new][ROOM]","req","Please enter the Room");
  	frmvalidator.addValidation("tables[course_periods][new][ROOM]","maxlen=10", "Max length for room is 10");
	
  	frmvalidator.addValidation("tables[course_periods][new][PERIOD_ID]","req","Please select the period");
	frmvalidator.addValidation("tables[course_periods][new][MARKING_PERIOD_ID]","req","Please select marking period");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","req","Please input total seats");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","maxlen=10","Max length for seats is 10");
       
       // frmvalidator.addValidation("get_status","attendance=0","Cannot take attendance in this period");
     //   alert(document.forms["F2"]["tables[course_periods][new][DAYS][M]"].value);
    }  
}

function validate_course_period()
{
        var frmvalidator  = new Validator("F2");     
        var hidden_cp_id=document.getElementById("hidden_cp_id").value;
        if(hidden_cp_id!='new')
        frmvalidator.addValidation("tables[course_periods]["+hidden_cp_id+"][SHORT_NAME]","req","Please enter the short name");
        else
        frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","req","Please enter the short name");
  	if(hidden_cp_id!='new')
        frmvalidator.addValidation("tables[course_periods]["+hidden_cp_id+"][TOTAL_SEATS]","num","Total Seats allows only numeric value");
        else
        frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","num","Total Seats allows only numeric value");
  	
    
        frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","maxlen=20", "Max length for short name is 20");

  	frmvalidator.addValidation("tables[course_periods][new][TEACHER_ID]","req","Please select the teacher");
                  frmvalidator.setAddnlValidationFunction("validate_cp_other_fields");
  	frmvalidator.addValidation("tables[course_periods][new][ROOM_ID]","req","Please enter the Room");
  	frmvalidator.addValidation("tables[course_periods][new][ROOM_ID]","maxlen=10", "Max length for room is 10");
        if(hidden_cp_id!='new')
        frmvalidator.addValidation("tables[course_period_var]["+hidden_cp_id+"][ROOM_ID]","req","Please enter the Room");
        else
        frmvalidator.addValidation("tables[course_period_var][new][ROOM_ID]","req","Please enter the Room");       
	frmvalidator.addValidation("tables[course_periods][new][CALENDAR_ID]","req","Please select the calendar");       
        if(hidden_cp_id!='new')
        {
            frmvalidator.addValidation("tables[course_period_var]["+hidden_cp_id+"][PERIOD_ID]","req","Please enter the Period");
//            if(document.getElementById("variable").value=='FIXED')
//                frmvalidator.addValidation("tables[course_period_var]["+hidden_cp_id+"][DAYS][]","req","Please enter the Days");
        }
        else
        {
            frmvalidator.addValidation("tables[course_period_var][new][PERIOD_ID]","req","Please enter the Period");   
//            if(document.getElementById("variable").value=='FIXED')
//                frmvalidator.addValidation("tables[course_period_var][new][PERIOD_ID]","req","Please enter the Days");
        }       	        
   
//        if(document.getElementById("preset").checked)
//        {
//            frmvalidator.addValidation("tables[course_periods][new][MARKING_PERIOD_ID]","req","Please select marking period");
//        }
//        if(document.getElementById("custom").checked)
//        {
//            frmvalidator.addValidation("month_begin","req"," Begin Month cannot be blank");
//           frmvalidator.addValidation("day_begin","req","Begin Day cannot be blank");
//           frmvalidator.addValidation("year_begin","req","Begin Year cannot be blank");
//            frmvalidator.addValidation("month_end","req","End Month cannot be blank");
//           frmvalidator.addValidation("day_end","req","End Day cannot be blank");
//           frmvalidator.addValidation("year_end","req","End Year cannot be blank");
//        }
//	
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","req","Please input total seats");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","maxlen=10","Max length for seats is 10");
       if(document.getElementById("variable").value=='VARIABLE')
           {
               frmvalidator.addValidation("course_period_variable[new][DAYS]","req","Please select a day");
               frmvalidator.addValidation("course_period_variable[new][PERIOD_ID]","req","Please select a period");
               if(hidden_cp_id!='new')
               {
               var id_for_room=document.getElementById('for_editing_room').value;
               frmvalidator.addValidation("course_period_variable["+hidden_cp_id+"]["+id_for_room+"][ROOM_ID]","req","Please select a room");
               }
               else
               frmvalidator.addValidation("course_period_variable[new][ROOM_ID]","req","Please select a room");       
           }
          
       
             return true;   
//        frmvalidator.addValidation("get_status","attendance=0","Cannot take attendance in this period");
    }  
function validate_block_schedule(option)
{

    if(document.getElementById('hidden_period_block').value=='')
    {
        document.getElementById("block_error").innerHTML="<b><font color=red>"+"Please select a period"+"</font></b>";
        document.getElementById("_period").focus();
        return false;
    }
    if(document.getElementById('_room').value=='')
    {
        document.getElementById("block_error").innerHTML="<b><font color=red>"+"Please select a room"+"</font></b>";
        document.getElementById("_room").focus();
        return false;
    }
}
function validate_cp_other_fields()
{
    if(document.getElementById("fixed_schedule").checked==false && document.getElementById("variable_schedule").checked==false && document.getElementById("blocked_schedule").checked==false)
    {
        document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please select schedule type"+"</font></b>";
        document.getElementById("fixed_schedule").focus();
        return false;
    }
     if(document.getElementById("preset").checked==false && document.getElementById("custom").checked==false)
    {
        document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please select marking period or custom date range"+"</font></b>";
        document.getElementById("preset").focus();
        return false;
    }
    if(document.getElementById("custom").checked==true)
    {
        if(document.getElementById("monthSelect1").value=='' || document.getElementById("daySelect1").value=='' || document.getElementById("yearSelect1").value=='')
        {
           document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please input a valid starting date"+"</font></b>";
           document.getElementById("custom").focus();
           return false;
        }
        if(document.getElementById("monthSelect2").value=='' || document.getElementById("daySelect2").value=='' || document.getElementById("yearSelect2").value=='')
        {
           document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please input a valid ending date"+"</font></b>";
           document.getElementById("custom").focus();
           return false;
        }
    }
     if(document.getElementById("preset").checked==true && document.getElementById("marking_period").value=='')
    {
        document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please select marking period"+"</font></b>";
        document.getElementById("marking_period").focus();
        return false;
    }
   if(document.getElementById("fixed_schedule").checked==true)
   {
       var a=document.getElementById("course_period_day_checked");
        a.value="";
        var inputs = document.getElementsByTagName("input");
        for(var i = 0; i < inputs.length; i++) 
        {
            if(inputs[i].type == "checkbox") 
            {
                if(inputs[i].name=="tables[course_period_var][new][DAYS][M]" || inputs[i].name=="tables[course_period_var][new][DAYS][T]" ||inputs[i].name=="tables[course_period_var][new][DAYS][W]" ||inputs[i].name=="tables[course_period_var][new][DAYS][H]"|| inputs[i].name=="tables[course_period_var][new][DAYS][F]")
                {    
                    if(inputs[i].checked)
                    {
                        a.value="1";
                        break;
                    }
                }
            }
        }        
        if(a.value.trim()=="")
        {
        document.getElementById("divErr").innerHTML='<font color="red"><b>You must select at least 1 day</b></font>';       
        return false;
        }
   }
    else
    return true;
}
function show_stadard_div()
{

    if(document.getElementById('cp_use_standards').checked==true)
      document.getElementById('standards_option').style.display = 'block';
    else
      document.getElementById('standards_option').style.display = 'none';  
}

///////////////////////////////////////// Course End ////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling End ///////////////////////////////////////////////////////

//////////////////////////////////////// Grade Start ///////////////////////////////////////////////////////


function formcheck_grade_grade()
{
    var frmvalidator  = new Validator("F1");
    if(document.getElementById('gp_scale') && document.getElementById('gp_scale').value!='')
    frmvalidator.addValidation("values[new][TITLE]","req", "Gradescale cannot be blank");

    if(document.getElementById('break_off') && document.getElementById('break_off').value!='')
    frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");

    frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50");
    frmvalidator.addValidation("values[new][COMMENT]","maxlen=50", "Max length for comment is 50");
   
    if(document.getElementById('sc_title') && document.getElementById('sc_title').value!='')
    frmvalidator.addValidation("values[new][BREAK_OFF]","req", "Break off cannot be blank");
    
    frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50");
    frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
    frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5");
    
    if(document.getElementById('title') && document.getElementById('title').value!='')
    {
    frmvalidator.addValidation("values[new][GP_SCALE]","req", "Scale value cannot be blank");        
    
    frmvalidator.addValidation("values[new][GP_SCALE]","num", "Please enter numeric value");    
    }
    
}
function formcheck_honor_roll()
{
    var frmvalidator  = new Validator("F1");
    var count=document.getElementById("count").value.trim();
    if(count!=0)
    {
        for(var i=1;i<=count+1;i++)
        {
            frmvalidator.addValidation("inputvalues["+i+"][TITLE]","req", "Please enter Title");
            frmvalidator.addValidation("inputvalues["+i+"][TITLE]","maxlen=50", "Max length for title is 50");
            frmvalidator.addValidation("inputvalues["+i+"][VALUE]","num", "Breakoff allows only numeric value");
            frmvalidator.addValidation("inputvalues["+i+"][VALUE]","maxlen=10", "Max length for breakoff is 10");
        }
    }
    var breakoff=document.getElementById("values[new][VALUE]").value.trim();
    if(breakoff!='')
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Please enter Title");
    }
    //        var month=document.getElementById('monthSelect1').value;
//        var day=document.getElementById('daySelect1').value;
//        var year=document.getElementById('yearSelect1').value;
   // frmvalidator.addValidation("values[new][TITLE]","req", "Please enter Title");
    frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50");
    frmvalidator.addValidation("values[new][VALUE]","num", "Breakoff allows only numeric value");
    frmvalidator.addValidation("values[new][VALUE]","maxlen=10", "Max length for breakoff is 10");
}

//////////////////////////////////////// Report Card Comment Start ///////////////////////////////////////////////////////

function formcheck_grade_comment()
{

		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "ID allows only numeric value");
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Comment is 50");
	
}

////////////////////////////////////////  Report Card Comment End  ///////////////////////////////////////////////////////


//////////////////////////////////////// Grade End ///////////////////////////////////////////////////////

///////////////////////////////////////// Eligibility Start ////////////////////////////////////////////////////

///////// Activities Start/////////////////////////////

function formcheck_eligibility_activies()
{
	
	var frmvalidator  = new Validator("F1");
        var ar_id=document.getElementById('id_arr').value;
        ar_id=ar_id.trim();
        if(ar_id!=0)
        {
            var ar_id=ar_id.split(',');
            for(var i=0;i<ar_id.length;i++)
            {
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","req", "Title cannot be blank");
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","maxlen=20", "Max length for Title is 20");    
            }
        }
        var l=ar_id.length+1;
        var monthid="monthSelect"+l;
        var dayid="daySelect"+l;
        var yearid="yearSelect"+l;
        var month=document.getElementById(monthid).value;
        var day=document.getElementById(dayid).value;
        var year=document.getElementById(yearid).value;
//        var month=document.getElementById('monthSelect1').value;
//        var day=document.getElementById('daySelect1').value;
//        var year=document.getElementById('yearSelect1').value;

        
        if(month.trim()!='' || day.trim()!='' || year.trim()!='')
        {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
	frmvalidator.addValidation("values[new][TITLE]","maxlen=20", "Max length for Title is 20");    
        }
	frmvalidator.setAddnlValidationFunction("ValidateDate_eligibility_activies");

}


	
	function ValidateDate_eligibility_activies()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))
		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the grade posting start date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}




///////////////////////////////////////// Activies End ////////////////////////////////////////////////////



///////////////////////////////////////// Entry Times Start ////////////////////////////////////////////////

function formcheck_eligibility_entrytimes()
{
  	var frmvalidator  = new Validator("F1");
	frmvalidator.setAddnlValidationFunction("ValidateTime_eligibility_entrytimes");
}

	function ValidateTime_eligibility_entrytimes()
	{
		var sd, sh, sm, sp, ed, eh, em, ep, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="values[START_DAY]")
			{
				sd=elem[i];
			}
			if(elem[i].name=="values[START_HOUR]")
			{
				sh=elem[i];
			}
			if(elem[i].name=="values[START_MINUTE]")
			{
				sm=elem[i];
			}
			if(elem[i].name=="values[START_M]")
			{
				sp=elem[i];
			}
			if(elem[i].name=="values[END_DAY]")
			{
				ed=elem[i];
			}
			if(elem[i].name=="values[END_HOUR]")
			{
				eh=elem[i];
			}
			if(elem[i].name=="values[END_MINUTE]")
			{
				em=elem[i];
			}
			if(elem[i].name=="values[END_M]")
			{
				ep=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckTime(sd, sh, sm, sp, ed, eh, em, ep))
		   {
			   sh.focus();
			   return false;
		   }
		}
		catch(err)
		{
		}
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		}
		
		   return true;
	}




///////////////////////////////////////// Entry Times End //////////////////////////////////////////////////
       
function formcheck_mass_drop()
{
    if(document.getElementById("course_div").innerHTML=='')
    {    
        alert("Please choose a course period to drop");
        return false;
    }
    else
        return true;
}



function formcheck_attendance_category()
{
        var frmvalidator  = new Validator("F1");
        frmvalidator.addValidation("new_category_title","req","Please enter attendance category Name");
        frmvalidator.addValidation("new_category_title","maxlen=50", "Max length for category name is 50");
        frmvalidator.addValidation("new_category_title","alphanumeric", "Attendance category Name allows only alphanumeric value");	
}


function formcheck_attendance_codes()
{
        if(document.getElementById("values[new][TITLE]").value!='')
        {
            var frmvalidator  = new Validator("F1");
            frmvalidator.addValidation("values[new][TITLE]","maxlen=50","Max length for title is 50");
            frmvalidator.setAddnlValidationFunction(formcheck_attendance_codes_extra);
        }
}
function formcheck_attendance_codes_extra()
{
                        var sel = document.getElementsByTagName("select");
			for(var i=1; i<sel.length; i++)
			{
                            var inp_name = sel[i].name;
                            var inp_value = sel[i].value;
                            if(inp_name == 'values[new][TYPE]')
			    {
                                  
                                  if(inp_value == "")
                                  {
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter type")+"</font></b>";
						return false;
                                  }
			    }
			    else if(inp_name == 'values[new][STATE_CODE]')
			    {
                                if(inp_value == "")
                                  {
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter state code")+"</font></b>";
						return false;
                                  }
			    }
                        }
                        
                        return true;
}
function formcheck_failure_count()
{
       var frmvalidator  = new Validator("failure");
       frmvalidator.addValidation("failure[FAIL_COUNT]","req","Please enter count");
       frmvalidator.addValidation("failure[FAIL_COUNT]","num", "Count allows only numeric value");
       frmvalidator.addValidation("failure[FAIL_COUNT]","maxlen=5", "Max length for count order is 5 digits");
		
}
//-------------------------------------------------assignments Title Validation Starts---------------------------------------------
function formcheck_assignments()
{

           var frmvalidator  = new Validator("F3");
           var type_id=document.getElementById("type_id").value;
//           alert(type_id);
         if(type_id.trim()=='')
         {
//             alert('ekhane');
           frmvalidator.addValidation("tables[new][TITLE]","req","Title cannot be blank");
           frmvalidator.addValidation("tables[new][TITLE]","maxlen=50","Max length for title is 50");
           frmvalidator.addValidation("tables[new][POINTS]","req","Total points cannot be blank");
           frmvalidator.addValidation("month_tables[new][ASSIGNED_DATE]","req","Month cannot be blank");
           frmvalidator.addValidation("day_tables[new][ASSIGNED_DATE]","req","Day cannot be blank");
           frmvalidator.addValidation("year_tables[new][ASSIGNED_DATE]","req","Year cannot be blank");
            frmvalidator.addValidation("month_tables[new][DUE_DATE]","req","Month cannot be blank");
           frmvalidator.addValidation("day_tables[new][DUE_DATE]","req","Day cannot be blank");
           frmvalidator.addValidation("year_tables[new][DUE_DATE]","req","Year cannot be blank");  
        }
        else
        {
//            alert('okhane');
            frmvalidator.addValidation("tables["+type_id+"][TITLE]","req","Title cannot be blank");
           frmvalidator.addValidation("tables["+type_id+"][TITLE]","maxlen=50","Max length for title is 50");
           frmvalidator.addValidation("tables["+type_id+"][POINTS]","req","Total points cannot be blank");
           frmvalidator.addValidation("month_tables["+type_id+"][ASSIGNED_DATE]","req","Month cannot be blank");
           frmvalidator.addValidation("day_tables["+type_id+"][ASSIGNED_DATE]","req","Day cannot be blank");
           frmvalidator.addValidation("year_tables["+type_id+"][ASSIGNED_DATE]","req","Year cannot be blank");
            frmvalidator.addValidation("month_tables["+type_id+"][DUE_DATE]","req","Month cannot be blank");
           frmvalidator.addValidation("day_tables["+type_id+"][DUE_DATE]","req","Day cannot be blank");
           frmvalidator.addValidation("year_tables["+type_id+"][DUE_DATE]","req","Year cannot be blank");  
        }
           
}
//-------------------------------------------------assignments Title Validation Ends---------------------------------------------


function passwordStrength(password)

{
    document.getElementById("passwordStrength").style.display = "none";

        var desc = new Array();

        desc[0] = "Very Weak";

        desc[1] = "Weak";

        desc[2] = "Good";

        desc[3] = "Strong";

        desc[4] = "Strongest";


        //if password bigger than 7 give 1 point

        if (password.length > 0) 
        {   
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#cccccc" ;
            document.getElementById("passwordStrength").innerHTML = desc[0] ;
            
            
        }


        //if password has at least one number give 1 point

        if (password.match(/\d+/) && password.length > 5) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordStrength").innerHTML = desc[1] ;
        }



        //if password has at least one special caracther give 1 point

        if (password.match(/\d+/) && password.length > 7 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff5f5f" ;
            document.getElementById("passwordStrength").innerHTML = desc[2] ;
        }

        
        //if password has both lower and uppercase characters give 1 point      

        if (password.match(/\d+/) && password.length > 10 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[A-Z]/) ) ) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#56e500" ;
            document.getElementById("passwordStrength").innerHTML = desc[3] ;
        }


        //if password bigger than 12 give another 1 point

        if (password.match(/\d+/) &&  password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) && password.length > 12)
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordStrength").innerHTML = desc[4] ;
        }

}


function passwordStrengthMod(password,opt)

{
    document.getElementById("passwordStrength"+opt).style.display = "none";

        var desc = new Array();

        desc[0] = "Very Weak";

        desc[1] = "Weak";

        desc[2] = "Good";

        desc[3] = "Strong";

        desc[4] = "Strongest";


        //if password bigger than 7 give 1 point

        if (password.length > 0) 
        {   
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#cccccc" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[0] ;
            
            
        }


        //if password has at least one number give 1 point

        if (password.match(/\d+/) && password.length > 5) 
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[1] ;
        }



        //if password has at least one special caracther give 1 point

        if (password.match(/\d+/) && password.length > 7 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#ff5f5f" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[2] ;
        }

        
        //if password has both lower and uppercase characters give 1 point      

        if (password.match(/\d+/) && password.length > 10 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[A-Z]/) ) ) 
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#56e500" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[3] ;
        }


        //if password bigger than 12 give another 1 point

        if (password.match(/\d+/) &&  password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) && password.length > 12)
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[4] ;
        }

}
function passwordMatch()
{
    document.getElementById("passwordMatch").style.display = "none" ;
    var new_pass = document.getElementById("new_pass").value;
    var vpass = document.getElementById("ver_pass").value;
    if(new_pass || vpass)
    {
        if(new_pass==vpass)
        {
            document.getElementById("passwordMatch").style.display = "block" ;
            document.getElementById("passwordMatch").style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordMatch").innerHTML = "Password Match" ;
        }
        if(new_pass!=vpass)
        {
            document.getElementById("passwordMatch").style.display = "block" ;
            document.getElementById("passwordMatch").style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordMatch").innerHTML = "Password MisMatch" ;    
        }
    }
    
}
function pass_check()
{
    if(document.getElementById("new_pass").value==document.getElementById("ver_pass").value)
    {
        var new_pass = document.getElementById("new_pass").value;
//      var ver_pass = document.getElementById("ver_pass").value;
        if(new_pass.length < 7 || (new_pass.length > 7 && !new_pass.match((/\d+/))) || (new_pass.length > 7 && !new_pass.match((/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))))
        {
            document.getElementById('divErr').innerHTML="<b><font color=red>Password should be minimum 8 characters with atleast one number and one special character</font></b>";
            return false;
        }
        
        return true;
    }
    else
    {
        document.getElementById('divErr').innerHTML="<b><font color=red>New Password MisMatch</font></b>";
        return false;
    }
}

function reenroll()
{
    if(document.getElementById("monthSelect1").value=='' || document.getElementById("daySelect1").value=='' || document.getElementById("yearSelect1").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter a Proper Date</font></b>";
        return false;
    }
    if(document.getElementById("grade_id").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Select a Grade Level</font></b>";
        return false;
    }
    if(document.getElementById("en_code").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Select an Enrollment Code</font></b>";
        return false;
    }
    
    else
    {
        var x = document.getElementById("sav").elements.length;
        var counter=0;
        for(var i=0;i<=x;i++)
        {
           if(document.getElementById("sav").elements[i])
           {
           var type=document.getElementById("sav").elements[i].type;
            if(type=="checkbox")
            {
                if(document.getElementById("sav").elements[i])
                {
                if(document.getElementById("sav").elements[i].name && document.getElementById("sav").elements[i].name!='')    
                {
                if(document.getElementById("sav").elements[i].checked==true)
                counter++;
                }

                }
            }
           }
        }
        if(counter==0)
        {
        document.getElementById('divErr').innerHTML='<b><font style="color:red">Please select a student</font></b>';
        return false;
        }
        else
        {
         return true;
        }
    }
}

function sel_staff_val()
{
    var sel_stf_info = document.getElementsByName('staff');
    var ischecked_method = false;
    for ( var i = 0; i < sel_stf_info.length; i++) 
    {
    
        if(sel_stf_info[i].checked) 
        {
            ischecked_method = true;
            break;
        }
    }
    if(!ischecked_method)   
    { 
        document.getElementById('sel_err').innerHTML="<b><font color=red>Please select any one.</font></b>";
        return false;
    }
    else
    {
        return true;
    }
}
function formcheck_add_staff(staff_school_chkbox_id){

        //alert(par);
  	var frmvalidator  = new Validator("staff");
        //frmvalidator.addValidation("month_values[START_DATE]["+1+"]","req","Please Enter start date");
  	frmvalidator.addValidation("staff[TITLE]","req","Please select the title");
        frmvalidator.addValidation("staff[FIRST_NAME]","req","Please enter the first name");
//	frmvalidator.addValidation("staff[FIRST_NAME]","alphabetic", "First name allows only alphabetic value");
  	frmvalidator.addValidation("staff[FIRST_NAME]","maxlen=100", "Max length for first name is 100 characters");
	
		
	frmvalidator.addValidation("staff[LAST_NAME]","req","Please enter the Last Name");
//	frmvalidator.addValidation("staff[LAST_NAME]","alphabetic", "Last name allows only alphabetic value");
  	frmvalidator.addValidation("staff[LAST_NAME]","maxlen=100", "Max length for Address is 100");
//        frmvalidator.addValidation("staff[GENDER]","req", "Please select gender");
//        frmvalidator.addValidation("month_staff[BIRTHDATE]","req", "Please select month");
//        frmvalidator.addValidation("day_staff[BIRTHDATE]","req", "Please select date");
//        frmvalidator.addValidation("year_staff[BIRTHDATE]","req", "Please select year");
//	frmvalidator.addValidation("staff[ETHNICITY_ID]","req","Please select ethnicity");
//        frmvalidator.addValidation("staff[PRIMARY_LANGUAGE_ID]","req","Please select primary language");
//        frmvalidator.addValidation("staff[SECOND_LANGUAGE_ID]","req","Please select secondary language");
        frmvalidator.addValidation("staff[EMAIL]","req","Please select email");
//        frmvalidator.addValidation("values[ADDRESS][STAFF_ADDRESS1_PRIMARY]","req","Please enter street address");
//        frmvalidator.addValidation("values[ADDRESS][STAFF_CITY_PRIMARY]","req","Please enter city");
//        frmvalidator.addValidation("values[ADDRESS][STAFF_STATE_PRIMARY]","req","Please enter state");
//        frmvalidator.addValidation("values[ADDRESS][STAFF_ZIP_PRIMARY]","req","Please enter zip");
//	frmvalidator.addValidation("values[ADDRESS][STAFF_ZIP_PRIMARY]","num","Invalid zip code");
//        frmvalidator.addValidation("values[CONTACT][STAFF_HOME_PHONE]","req","Please enter home phone");
//	frmvalidator.addValidation("values[CONTACT][STAFF_HOME_PHONE]","num","Invalid home phone number");
//        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_PHONE]","req","Please enter work phone");
//	frmvalidator.addValidation("values[CONTACT][STAFF_WORK_PHONE]","phone","Invalid work phone number");
//        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_EMAIL]","req","Please enter work email");
//	frmvalidator.addValidation("values[CONTACT][STAFF_WORK_EMAIL]","email","Invalid email");
//        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_FIRST_NAME]","req","Please enter the emergency contacts first name");
//        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_LAST_NAME]","req","Please enter the emergency contacts last Name");
//        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_RELATIONSHIP]","req","Please enter the emergency contacts relationship");
//        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_HOME_PHONE]","req","Please enter the emergency contacts home phone");
//	frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_HOME_PHONE]","num","Invalid emergency contacts home phone number");
//        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_WORK_PHONE]","req","Please enter emergency contacts work phone");
//	frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_WORK_PHONE]","phone","Invalid emergency contacts work phone number");
        frmvalidator.addValidation("values[SCHOOL][CATEGORY]","req","Please select the category");
        frmvalidator.addValidation("month_values[JOINING_DATE]","req", "Please select the joing date's month");
        frmvalidator.addValidation("day_values[JOINING_DATE]","req", "Please select the joing date's date");
        frmvalidator.addValidation("year_values[JOINING_DATE]","req", "Please select the joing date's year");
        if(staff_school_chkbox_id!=0 && staff_school_chkbox_id!='')
        return school_check(staff_school_chkbox_id);
}
function formcheck_user_user_mod()
{
            
        
  	var frmvalidator  = new Validator("staff");
        
  	frmvalidator.addValidation("people[FIRST_NAME]","req","Please enter the first name");
  	frmvalidator.addValidation("people[FIRST_NAME]","maxlen=100", "Max length for first name is 100 characters");
	
		
	frmvalidator.addValidation("people[LAST_NAME]","req","Please enter the Last Name");

  	frmvalidator.addValidation("people[LAST_NAME]","maxlen=100", "Max length for Address is 100");
    
        frmvalidator.addValidation("people[EMAIL]","email", "Please enter a valid email");
        frmvalidator.addValidation("people[EMAIL]","req", "Please enter the email");
//	frmvalidator.addValidation("people[HOME_PHONE]","phone_num","Please enter a valid telephone number");
        
        frmvalidator.addValidation("student_addres[ADDRESS]","req","Please enter the address");
        frmvalidator.addValidation("student_addres[CITY]","req","Please enter the city");
        frmvalidator.addValidation("student_addres[STATE]","req","Please enter the state");
        frmvalidator.addValidation("student_addres[ZIPCODE]","req","Please enter the zipcode");
        
}
function validate_email()
{
    var frmvalidator  = new Validator("ComposeMail");
    frmvalidator.setAddnlValidationFunction("mail_body_chk");
    frmvalidator.addValidation("txtToUser","req","Enter message recipient");
    
}
function mail_body_chk()
{
 var oEditor = FCKeditorAPI.GetInstance('txtBody');
     var body1 = oEditor.GetHTML(true);
     if(body1 == '') 
     {
                document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please write body of message")+"</font></b>";		
		this.txtBody.focus();
                return false;
     }
     else
        return true;    
}
function validate_group_schedule()
{
        var x = document.getElementById("sav").elements.length;
        var counter=0;
        for(var i=0;i<=x;i++)
        {
           if(document.getElementById("sav").elements[i])
           {
           var type=document.getElementById("sav").elements[i].type;
            if(type=="checkbox")
            {
                if(document.getElementById("sav").elements[i])
                {
                if(document.getElementById("sav").elements[i].name && document.getElementById("sav").elements[i].name!='')    
                {
                if(document.getElementById("sav").elements[i].checked==true)
                counter++;
                }

                }
            }
           }
        }
        if(counter==0)
        {
        document.getElementById('divErr').innerHTML='<b><font style="color:red">Please select a student</font></b>';
        return false;
        }
        else
        {
         formload_ajax("sav");   
        }
}
function formcheck_rooms()
{
    var frmvalidator  = new Validator("F1");
   var count_room=document.getElementById("count_room").value.trim();
//   alert(count_room);
   //frmvalidator.addValidation("values[new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("values[new][DESCRIPTION]","maxlen=100", "Max length for DESCRIPTION is 100 characters");
        frmvalidator.addValidation("values[new][CAPACITY]","num", "Capacity allows only numeric value");
        frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
   if(count_room!=0)
   {
        for(var i=1;i<=count_room;i++)
        {
          //  frmvalidator.addValidation("inputvalues["+i+"][TITLE]","req","Please enter the title");
            frmvalidator.addValidation("inputvalues["+i+"][DESCRIPTION]","maxlen=100", "Max length for DESCRIPTION is 100 characters");
            frmvalidator.addValidation("inputvalues["+i+"][CAPACITY]","num", "Capacity allows only numeric value");
            frmvalidator.addValidation("inputvalues["+i+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
            frmvalidator.addValidation("values["+i+"][DESCRIPTION]","maxlen=100", "Max length for DESCRIPTION is 100 characters");
            frmvalidator.addValidation("values["+i+"][CAPACITY]","num", "Capacity allows only numeric value");
            frmvalidator.addValidation("values["+i+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
    
        }
    }
        
//  	var r=true;
//        var ids=document.getElementById("room_ids").value;
//        var room_iv=document.getElementById("room_iv").value;
//        if(room_iv!='')
//        room_iv=room_iv.split(",");
//        if(ids!='')
//        ids=ids.split(",");
//        if(document.getElementById("TITLE_new").value!='' && document.getElementById("CAPACITY_new").value=='')
//        {
//            document.getElementById('divErr').innerHTML="<b><font style='color:red'>Room capacity cannot be blank</font></b>";
//            r=false;
//        }
//        if(document.getElementById("TITLE_new").value=='' && document.getElementById("CAPACITY_new").value!='')
//        {
//            document.getElementById('divErr').innerHTML="<b><font style='color:red'>Room title cannot be blank</font></b>";
//            r=false;
//        }
//        if(document.getElementById("TITLE_new").value!='' && document.getElementById("CAPACITY_new").value!='' )
//        {
//            if(room_iv.length>0)
//            {
//                for(var ri=0;ri<room_iv.length;ri++)
//                {
//                    var room_d=room_iv[ri].split("_");
//                    if(document.getElementById("TITLE_new").value==room_d[1])
//                    {
//                     document.getElementById('divErr').innerHTML="<b><font style='color:red'>Room title already exists</font></b>";
//                     r=false;   
//                    }
//                }
//            }
//        }
//        if(ids.length>0)
//        {
//          for(var i=0;i<ids.length;i++)
//          {
//              if(document.getElementById("inputvalues["+ids[i]+"][TITLE]"))
//              {
//                if(document.getElementById("inputvalues["+ids[i]+"][TITLE]").value!='')
//                {
//                    if(room_iv.length>0)
//                    {
//                        for(var ri=0;ri<room_iv.length;ri++)
//                        {
//                            var room_d=room_iv[ri].split("_");
//                            if(document.getElementById("inputvalues["+ids[i]+"][TITLE]").value==room_d[1] && ids[i]!=room_d[0])
//                            {
//                             document.getElementById('divErr').innerHTML="<b><font style='color:red'>Room title already exists</font></b>";
//                             r=false;   
//                            }
//                        }
//                    }
//                }
//              }
//             if(document.getElementById("inputvalues["+ids[i]+"][DESCRIPTION]"))
//              {
//                if(document.getElementById("inputvalues["+ids[i]+"][DESCRIPTION]").value!='') 
//                {
//                    if(document.getElementById("inputvalues["+ids[i]+"][DESCRIPTION]").value.length>100)
//                    {
//                        document.getElementById('divErr').innerHTML="<b><font style='color:red'>Max length for DESCRIPTION is 100 characters</font></b>";
//                        r=false;
//                    }
//                }
//              }
//              var data=document.getElementById("inputvalues["+ids[i]+"][CAPACITY]");
//              if(data)
//              {                
//                if(data.value!='') 
//                {
//                    data=data.value;
//                    if (/^[0-9- ]*$/.test(data) == false)
//                    {
//                            document.getElementById('divErr').innerHTML="<b><font style='color:red'>Capacity allows only numeric value</font></b>";
//                            r=false;
//                    }
//                }
//              }
//              var data=document.getElementById("inputvalues["+ids[i]+"][SORT_ORDER]");
//              if(data)
//              {                
//                if(data.value!='') 
//                {
//                    data=data.value;
//                    if (/^[0-9- ]*$/.test(data) == false)
//                    {
//                            document.getElementById('divErr').innerHTML="<b><font style='color:red'>Sort Order allows only numeric value</font></b>";
//                            r=false;
//                    }
//                }
//              }
//        }
//    }
//    return r; 
}
function fill_rooms(option,id)
{
    var room_iv=document.getElementById("room_iv").value;
    if(room_iv!='')
    room_iv=room_iv.split(",");
    if(room_iv.length>0)
    {
        for(var i=0;i<room_iv.length;i++)
        {
            var rd=room_iv[i].split("_");
            if(rd[0]==id)
            {
                var old_string=room_iv[i];
                var new_string=id+'_'+option.value;
            }
        }
    }
    var new_res=document.getElementById("room_iv").value.replace(old_string,new_string);
    document.getElementById("room_iv").value=new_res;
    
}
function formcheck_Timetable_course_F4()
{
    var frmvalidator  = new Validator("F4");
        
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","req","Please enter the subject name");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","maxlen=100", "Max length for subject is 100 characters");
	
}
function formcheck_halfday_fullday()
{
    var frmvalidator  = new Validator("sys_pref");
    frmvalidator.addValidation("inputvalues[FULL_DAY_MINUTE]","maxlen=10", "Max length for full day minute is 10 digits");
    frmvalidator.addValidation("inputvalues[HALF_DAY_MINUTE]","maxlen=10", "Max length for half day minute is 10 digits");
    frmvalidator.addValidation("inputvalues[FULL_DAY_MINUTE]","num", "Full day minute allows only numeric value");
    frmvalidator.addValidation("inputvalues[HALF_DAY_MINUTE]","num", "Half day minute allows only numeric value");
}
function formcheck_Timetable_course_F3()
{
    var frmvalidator  = new Validator("F3");
    var course_id=document.getElementById('course_id_div').value;      
    if(course_id=='new')
    {
  	frmvalidator.addValidation("tables[courses][new][TITLE]","req","Please enter the course title ");
  	frmvalidator.addValidation("tables[courses][new][TITLE]","maxlen=50", "Max length for course title is 50 characters ");
//        frmvalidator.addValidation("tables[courses][new][TITLE]","alphanumeric", "Course title allows only alphanumeric value");
        frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","maxlen=25", "Max length for short name is 25 characters ");        
    }
    else
    {
        frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","req","Please enter the course title ");
  	frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","maxlen=50", "Max length for course title is 50 characters");
//        frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","alphanumeric", "Course title allows only alphanumeric value");
        frmvalidator.addValidation("inputtables[courses]["+course_id+"][SHORT_NAME]","maxlen=25", "Max length for short name is 25 characters"); 
    }
}

function mail_group_chk()
{
     var frmvalidator  = new Validator("Group");
     frmvalidator.addValidation("txtGrpName","req", "Please enter the group name");	

    frmvalidator.addValidation("txtGrpName","maxlen=100", "Max length for group name is 100 characters");	
}

function formcheck_enrollment_code()
{

        var frmvalidator  = new Validator("F1");
        var ar_id=document.getElementById('id_arr').value;
        ar_id=ar_id.trim();
        if(ar_id!=0)
        {
            var ar_id=ar_id.split(',');
            for(var i=0;i<ar_id.length;i++)
            {
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","req", "Title cannot be blank");
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","alphanumeric", "Title allows only alphanumeric value");
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","maxlen=50", "Max length for title is 50 characters");    
            }
        }
//        
        var sn=document.getElementById('stu_short_new').value;
        if(sn.trim()!='')
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
        frmvalidator.addValidation("values[new][TITLE]","alphanumeric", "Title allows only alphanumeric value");
        frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
}
function formcheck_calendar_event()
{
        var title=document.getElementById('title');        
        if(title!=null)
        {          
        if(title.value.trim()=='')
        {
            document.getElementById('err_message').innerHTML='<font style="color:red"><b>Title cannot be blank.</b></font>';
            return false;
        }
        else if(title.value.length>50)
        {
            document.getElementById('err_message').innerHTML='<font style="color:red"><b>Max length for title is 50 characters.</b></font>';
            return false;
        }
        else
            formload_ajax('popform');   
        }
        else
        {           
            var title=document.getElementById('values[TITLE]');
            if(title.value.trim()=='')
            {
                document.getElementById('err_message').innerHTML='<font style="color:red"><b>Title cannot be blank.</b></font>';
                return false;
            }
            else if(title.value.length>50)
            {
                document.getElementById('err_message').innerHTML='<font style="color:red"><b>Max length for title is 50 characters.</b></font>';
                return false;
            }
            else
                formload_ajax('popform');   
        }
        
        
}
function formcheck_common_standards()
{
    var frmvalidator  = new Validator("standard");
        if(document.getElementById("values[new][SUBJECT]").value!='')
        {           
            frmvalidator.addValidation("values[new][STANDARD_REF_NO]","req","Please enter standard ref number");
            frmvalidator.addValidation("values[new][SUBJECT]","maxlen=50","Max length for subject is 50");
            frmvalidator.addValidation("values[new][GRADE]","maxlen=50","Max length for grade is 50");
            frmvalidator.addValidation("values[new][COURSE]","maxlen=50","Max length for course is 50");
             frmvalidator.addValidation("values[new][DOMAIN]","maxlen=50","Max length for domain is 50");
             frmvalidator.addValidation("values[new][TOPIC]","maxlen=50","Max length for topic is 50");
             frmvalidator.addValidation("values[new][STANDARD_REF_NO]","maxlen=50","Max length for ref number is 50");
             frmvalidator.addValidation("values[new][STANDARD_DETAILS]","maxlen=50","Max length for ref details is 50");
             
        }
        var count=document.getElementById("count").value.trim();
//        alert(count);
        for(var i=1;i<count;i++)
        {
             frmvalidator.addValidation("inputvalues["+i+"][STANDARD_REF_NO]","req","Please enter standard ref number");
            frmvalidator.addValidation("inputvalues["+i+"][SUBJECT]","maxlen=50","Max length for subject is 50");
            frmvalidator.addValidation("inputvalues["+i+"][GRADE]","maxlen=50","Max length for grade is 50");
            frmvalidator.addValidation("inputvalues["+i+"][COURSE]","maxlen=50","Max length for course is 50");
             frmvalidator.addValidation("inputvalues["+i+"][DOMAIN]","maxlen=50","Max length for domain is 50");
             frmvalidator.addValidation("inputvalues["+i+"][TOPIC]","maxlen=50","Max length for topic is 50");
             frmvalidator.addValidation("inputvalues["+i+"][STANDARD_REF_NO]","maxlen=50","Max length for ref number is 50");
             frmvalidator.addValidation("inputvalues["+i+"][STANDARD_DETAILS]","maxlen=50","Max length for ref details is 50");
        }
}

function check_effort_cat()
{
            var frmvalidator  = new Validator("cat");
            frmvalidator.addValidation("TITLE","req","Please enter title");
            frmvalidator.addValidation("TITLE","maxlen=50","Max length for title is 50");
            frmvalidator.addValidation("SORT_ORDER","num", "Sort order allows only numeric value");
}
function check_effort_item()
{
            var frmvalidator  = new Validator("F1");
            var count=document.getElementById("count_item").value.trim();           
            if(count!='0')
            {alert("elo");
                for(var i=count;i>0;i--)
                {
                    frmvalidator.addValidation("inputvalues["+i+"][TITLE]","maxlen=50","Max length for title is 50");
                    frmvalidator.addValidation("inputvalues["+i+"][SORT_ORDER]","num", "Sort order allows only numeric value");
                }
            }
            if(document.getElementById("values[new][TITLE]").value!="")
            {
//              frmvalidator.addValidation("TITLE","req","Please enter title");
                frmvalidator.addValidation("values[new][TITLE]","maxlen=50","Max length for title is 50");
                frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
            }
}
   	