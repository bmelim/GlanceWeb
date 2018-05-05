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


function postmotiononselect($dbvars, $postmotionontime, $title)
{
        echo $title . ":<br>";
        echo "<select name='" . $postmotionontime .  "' onchange='if(this.value != 0) { autoSubmit(); }'>";

	$y = 5;
        while ($y < 61)
        {
                echo "<option value='" . $y . "'";
                if($dbvars[$postmotionontime] == $y)
                        echo "SELECTED";
                echo ">" . $y . " secs";
                echo "</option>";
                if ($y < 31)
                        $y = $y + 5;
                else
                        $y = $y + 10;

        }



        $x = 1;
        while ($x < 65)
        {
                echo "<option value='" . $x * 60 . "'";
                if($dbvars[$postmotionontime] == $x * 60)
                        echo "SELECTED";
                echo ">" . $x . " min";
                if (intval($x) > 1)
                        echo "s";
                echo "</option>";
                if ($x < 10)
                        $x = $x + 1;
                else
                        $x = $x + 5;

        }

        echo "</select> <br><br>";

}



function setswitchdata($dbvars, $xs, $label) 
{
        echo $label;
	echo "&nbsp";
        echo "<input type='text' name='button" . $xs . "alias' value = '";
                                echo $dbvars["button" . $xs . "alias"];
                                echo "' />";
                                echo "<br>";
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

        echo "<h2>Events</h2>";


        echo "<input type='hidden' value='off' name='eventson'>";
        echo "<input type='checkbox' name='eventson' id='eventson'"; 
        if($dbvars['eventson'] == "on") 
	        echo "checked='checked'";
	echo " onclick='autoSubmit();'>Events on<br>";

       echo "<br>";




	$x = 0; 

	while ($x < 8)
	{
		$y = $x + 1;

		echo $y . ":&nbsp";

	        echo "<input type='text' name='event" . $x . "description' placeholder='Event " . $y . " text' size = '15' value = '";
                                echo $dbvars["event" . $x . "description"];
                                echo "' />";
		echo "<br>";

		echo "&nbsp";
		echo "&nbsp";
		echo "&nbsp";
		echo "&nbsp";

	        echo "<input type='text' name='event" . $x . "days' placeholder='Event " . $y . " days' size = '15' value = '";
                                echo $dbvars["event" . $x . "days"];
                                echo "' />";

		echo "&nbsp";

		echo "<input type='time' name='event" . $x . "time' value='";
		echo $dbvars['event' . $x . 'time'];
		echo"'>";

		echo "&nbsp";
                echo "<br>";

                echo "<br>";

		$x++; 
	}




echo "<br>";


		echo "</div>";
      echo "<div class='col-md-4'" . $colourstyle .  ">";


	        echo "<h2>Actions</h2>";


		//Select the action
		echo "<select name='actions'  onchange='if(this.value != 0) { autoSubmit(); }'>";
		
			echo "<option value='screensingletouch'";	
			if ($dbvars["actions"] ==  "screensingletouch") 
				echo "SELECTED";
			echo ">Screen Single Touch</option>";

			echo "<option value='' disabled='disabled'>───────────────</option>";

			echo "<option value='motion'";	
			if ($dbvars["actions"] ==  "motion") 
				echo "SELECTED";
			echo ">Motion</option>";

			echo "<option value='temp'";	
			if ($dbvars["actions"] ==  "temp") 
				echo "SELECTED";
			echo ">Temperature</option>";

			echo "<option value='humidity'";	
			if ($dbvars["actions"] ==  "humidity") 
				echo "SELECTED";
			echo ">Humidity</option>";

			echo "<option value='light'";	
			if ($dbvars["actions"] ==  "light") 
				echo "SELECTED";
			echo ">Light</option>";

			echo "<option value='' disabled='disabled'>───────────────</option>";

			for ($x = 0; $x < 8; $x++) 
			{
				if ($dbvars['button' . $x . 'alias'] != "") //only show the button as an option if it is configured
				{
					$y = $x + 1;
					echo "<option value='button" . $x . "'";	
					if ($dbvars["actions"] ==  "button" . $x) 
						echo "SELECTED";
					echo ">Button " . $y . " [" . $dbvars['button' . $x . 'alias'] . "] </option>";
				}
			}

			echo "<option value='' disabled='disabled'>───────────────</option>";

			for ($x = 0; $x < 8; $x++) 
			{
				if ($dbvars['event' . $x . 'description'] != "") //only show the event as an option if it is configured
				{
					$y = $x + 1;
					echo "<option value='event" . $x . "'";	
					if ($dbvars["actions"] ==  "event" . $x) 
						echo "SELECTED";
					echo ">Event " . $y . " [" . $dbvars['event' . $x . 'description'] . "] </option>";
				}
			}


		echo "</select> <br>";


//                echo "<B>Screen Single Touch:</B>";
                echo "<br>";
                echo "<b>On Steps</b>";
                echo "<br>";

		$x = 0;
		while ($x < 10)
		{
			if ($x < 9)
		                setdisplaydata($clpdb, $dbvars, $dbvars["actions"] . "on-" . strval($x), strval($x + 1) . ":&nbsp&nbsp&nbsp", "action");
			else
		                setdisplaydata($clpdb, $dbvars, $dbvars["actions"] . "on-" . strval($x), strval($x + 1) . ":&nbsp", "action");

			$x++;
		}


		$actiontype = "";
		if(isset($dbvars[$dbvars["actions"] . "type"]))
			$actiontype = $dbvars[$dbvars["actions"] . "type"];

                echo "<br>";
                echo "<br>";
	
		if ($actiontype == "togglebutton" or $dbvars["actions"] == "screensingletouch")
		{
        	        echo "<b>Off Steps</b>";
                	echo "<br>";

			$x = 0;
			while ($x < 10)
			{
				if ($x < 9)
			                setdisplaydata($clpdb, $dbvars, $dbvars["actions"] . "off-" . strval($x), strval($x + 1) . ":&nbsp&nbsp&nbsp", "action");
				else
			                setdisplaydata($clpdb, $dbvars, $dbvars["actions"] . "off-" . strval($x), strval($x + 1) . ":&nbsp", "action");
				$x++;
			}
		}

		$clpdb->close();

	        echo "<br>";

		echo	"<input type='submit' value='Save'>";



		echo "</div>";

 ?>


      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>


</form>
</body>
</html>





