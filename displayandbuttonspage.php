<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    	<title>Glance Settings</title>

     <!-- Bootstrap -->
      <link href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel = "stylesheet">

   <!-- Custom styles for this template -->
    <link href="css/grid.css" rel="stylesheet">

<script>
function autoSubmit()
{
    var formObject = document.forms['settingsform'];
    formObject.submit();
}

</script>
 

</head>



<?php

//Functions 

//Set to display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function setswitchdata($dbvars, $xs, $label) 
{
        echo "Button " . $label;
	echo "<br>";
        echo "<input type='text' name='button" . $xs . "alias' placeholder='Button " . (((int)$xs)+1) . " text' style='width:100px;font-size:11pt;' value = '";
                                echo $dbvars["button" . $xs . "alias"];
                                echo "' />";

	echo "&nbsp";

        echo "<select name='button" . $xs . "type'  onchange='if(this.value != 0) { autoSubmit(); }'>";

                echo "<option value='button'";
                if ($dbvars['button' . $xs . 'type'] ==  "button")
                        echo "SELECTED";
                echo ">Button</option>";

                echo "<option value='togglebutton'";
                if ($dbvars['button' . $xs . 'type'] ==  "togglebutton")
                        echo "SELECTED";
                echo ">Toggle Button</option>";

	echo "</select> <br>";
}


function setdisplaydata($clpdb, $dbvars, $name, $label, $display_or_action) 
{
	echo $label;
	echo "<select name='" . $name . "'  onchange='if(this.value != 0) { autoSubmit(); }'>";

	echo "<option value='-'";	
	if ($dbvars[$name] ==  "-") 
		echo "SELECTED";
	echo ">-</option>";

	if ($display_or_action == "display")
	{
		echo "<option value='@@time'";	
		if ($dbvars[$name] ==  "@@time") 
			echo "SELECTED";
		echo ">@@Time</option>";

		echo "<option value='@@secs'";	
			if ($dbvars[$name] ==  "@@secs") 
				echo "SELECTED";
		echo ">@@Secs</option>";

		echo "<option value='@@date'";	
		if ($dbvars[$name] ==  "@@date") 
			echo "SELECTED";
		echo ">@@Date</option>";
	
		echo "<option value='@@nextevent'";	
		if ($dbvars[$name] ==  "@@nextevent") 
			echo "SELECTED";
		echo ">@@NextEvent</option>";
	
		echo "<option value='@@minsuntilnextevent'";	
		if ($dbvars[$name] ==  "@@minsuntilnextevent") 
			echo "SELECTED";
		echo ">@@MinsUntilNextEvent</option>";
	
		echo "<option value='@@hoursminsuntilnextevent'";	
		if ($dbvars[$name] ==  "@@hoursminsuntilnextevent") 
			echo "SELECTED";
		echo ">@@HoursMinsUntilNextEvent</option>";
	
		echo "<option value='@@remainingscreenontime'";	
		if ($dbvars[$name] ==  "@@remainingscreenontime") 
			echo "SELECTED";
		echo ">@@remainingscreenontime</option>";
	
		echo "<option value='@@away'";	
		if ($dbvars[$name] ==  "@@away") 
			echo "SELECTED";
		echo ">@@away</option>";
	
		echo "<option value='@@off'";	
		if ($dbvars[$name] ==  "@@off") 
			echo "SELECTED";
		echo ">@@Off</option>";
	}

	$results = $clpdb->query("select id, host, name, description, value from messages where " . $display_or_action  . " = '1' order by description");
	while ($ddrow = $results->fetchArray()) 
	{
		$separator = "/";

		echo "<option value='";
		echo $ddrow['id'] . "'";	
		if ($dbvars[$name] ==  $ddrow['id']) 
			echo "SELECTED";
		echo ">" . $ddrow['description'] .  "</option>";
	}

	echo "</select> <br>";
}


function settimeselect($dbvars, $name, $max)
{
//        echo "<select name='" . $name . "'  onchange='if(this.value != 0) { autoSubmit(); }'>";
        echo "<select name='" . $name . "'  onchange='autoSubmit();'>";
        $y = "";

        for ($x=0; $x<=$max; $x++) {
                if ($x < 10)
                        $y = "0" . strval($x);
                else
                        $y = strval($x);

                echo "<option value='$y'";
                if ($dbvars[$name] == $y)
                        echo "SELECTED";
                echo ">" . $y . "</option>";
        }

        echo "</select>";
}

echo "<body>";
echo "<form action = '' method = 'post' name='settingsform' id='settingsform'>";


$dbvars=array();

//Read the values from the database
$db = new SQLite3('clp.db') or die('Unable to open database');
$results = $db->query("SELECT setting,value FROM settings");
while ($row = $results->fetchArray()) 
{
	$dbvars[$row['setting']]=$row['value'];
}

$clpdb = new SQLite3('clp.db') or die('Unable to open database');

//Save the values on posting
if (!empty($_POST)) //check if form was submitted
{
	$query = $db->exec("PRAGMA synchronous=OFF");
	foreach ($dbvars as $key => $value) {
		if(isset($_POST[$key]))
		{ 
			if (strcmp($dbvars[$key], $_POST[$key])!==0) //If it has changed, update the database
			{	
				$query = $db->exec("UPDATE settings SET value = '" . $_POST[$key] . "' WHERE setting = '" .$key . "'");
				$dbvars[$key]=$_POST[$key]; //update the PHP field
			}
		}
	}
}



       if (strcmp($dbvars['webcolour'], "green")==0)
                $colourstyle = "style = 'background-color:rgba(0,255,0,.2)'";
        elseif (strcmp($dbvars['webcolour'], "red")==0)
                $colourstyle = "style = 'background-color:rgba(255,0,0,.5)'";
        elseif (strcmp($dbvars['webcolour'], "orange")==0)
                $colourstyle = "style = 'background-color:rgba(255,125,0,.2)'";






      echo "<div class='container'>";

      echo "<div class='col-md-4'" . $colourstyle .  ">";


      		echo "<h2>Display</h2>";
			
                setdisplaydata($clpdb, $dbvars, "display12", "Motion Icon:", "display");
                setdisplaydata($clpdb, $dbvars, "indicatorswitch", "Smiley Icon:", "display");

                echo("<br>");

		setdisplaydata($clpdb, $dbvars, "display1", "Label 1:", "display"); 
		setdisplaydata($clpdb, $dbvars, "display2", "Label 2:", "display"); 


		setdisplaydata($clpdb, $dbvars, "display3", "Label 3:", "display");

		echo "<input type='hidden' value='off' name='flashingtime'>";
		echo "<input type='checkbox' name='flashingtime' id='flashingtime'";
		if ($dbvars['flashingtime'] == 'on') 
			echo "checked='checked'";
		echo "onclick='autoSubmit();'>Flashing time colon<br>";

		setdisplaydata($clpdb, $dbvars, "display4", "Label 4:", "display");
		setdisplaydata($clpdb, $dbvars, "display5", "Label 5:", "display");
		setdisplaydata($clpdb, $dbvars, "display6", "Label 6:", "display");
		setdisplaydata($clpdb, $dbvars, "display7", "Label 7:", "display");
		setdisplaydata($clpdb, $dbvars, "display8", "Label 8:", "display");
		setdisplaydata($clpdb, $dbvars, "display9", "Label 9:", "display");
		setdisplaydata($clpdb, $dbvars, "display10", "Label 10:", "display");
		setdisplaydata($clpdb, $dbvars, "display11", "Label 11:", "display");

		echo "<br>";


		//	$dddb->close();


	echo "</div>";
        
      echo "<div class='col-md-4'" . $colourstyle .  ">";
        echo "<h2>Buttons</h2>";
		for ($x = 0; $x < 8; $x++) 
			setswitchdata($dbvars, $x, ((string)($x + 1)) . ":");
		
		echo "<br>";

                echo "<input  type='submit' name='submitbutton' value='Save' /><br /> <br>";


		$clpdb->close();


        echo "</div>";



 ?>


      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>


</form>
</body>
</html>





