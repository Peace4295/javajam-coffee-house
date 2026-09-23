var jobForm = document.getElementById("jobForm");

jobForm.addEventListener("submit", validateForm);

function validateForm(event)
{
    event.preventDefault();

    //name
    var name = document.getElementById("CustName").value;
    
    var namePattern = /^[A-Za-z ]+$/;
    

    if (!namePattern.test(name)){
        //alert ("Name must only contain alphabet characters and spacing");
        //additional 15% for name Q4i
        document.getElementById("nameError").innerHTML = "Please enter letters and spaces only";

        return;
    } 
    //additional for name
    if (name.trim().length < 2){
        document.getElementById("nameError").innerHTML = "Name must contain at least 2 characters";
        return;
    }
   

    //email
    var email = document.getElementById("CustEmail").value;

    var emailPattern = /^[\w.-]+@(\w+\.){1,3}[A-Za-z]{2,3}$/;

    if (!emailPattern.test(email)){
        alert("Please enter a valid email address");
        return;
    }
     //alert("Email is valid");


     //start date
    var StartDateValue = document.getElementById("myDateid").value;

    if (StartDateValue != "")
    {
        var startDate = new Date(StartDateValue + "T00:00:00");
        var today = new Date();     //get todays date

        today.setHours(0, 0, 0, 0);      //only care about date

        if (startDate <= today) //<= means today or earlier
        {
            alert("Start date must be after today");
            return;
        }

       //alert("Start date is correct");
    }

    var exp = document.getElementById("comments").value;

    if(exp.trim( )== ""){        //trim will not except spaces as characters
        alert("Experience cannot be empty");
        return;
    }
    //alert("yup");

    //additional for experience
    if (exp.trim().length < 10){  //must be more than 10 characters
        alert("Please provide more details about your experience");
        return;     
    }

    if (exp.length > 200){      //cnnt exceed 200c
        alert("Experience must not exceed 200 characters");
        return;
    }
}



