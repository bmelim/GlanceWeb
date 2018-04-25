<?php
//
// Module responsible for the configuration of GlaceWeb
//
// by Peter Juett
// References:
//
// Copyright 2018
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    	<title></title>

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

function validateForm() {

    var re = /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;

    var x = document.forms['settingsform']['alarm1'].value;
    if (x.length!=0)
    {
	if (re.test(x)==false || x.length != 5) {
        	alert("Invalid weekday alarm");
        	return false;
    
	}
    }


    var y = document.forms['settingsform']['alarm2'].value;
    if (y.length!=0)
    {
	if (re.test(y)==false || y.length != 5) {
        	alert("Invalid weekend alarm");
        	return false;
    
	}
    }

    return true; 
}

</script>
 

</head>

<body>
	<form action = "" method = "post" onsubmit="return validateForm()" name="settingsform" id="settingsform">


<?php    

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
if(isset($_POST['temperaturescale'])){ //check if form was submitted
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
			
			

	$db->close();
//	$dddb->close();


?>


                       <b>Aliases: </b><br>


                        Name: <br>
                        <input type="text" name="name" value = "<?php echo $dbvars['name'] ?>" />
<br><br>


			<B>Temperature Scale:</B> <br>
    			<input type="radio" name="temperaturescale" value="c" <?php if($dbvars['temperaturescale'] == "c") echo "checked='checked'";?> onChange="autoSubmit();">Degrees C</option> <br>
    			<input type="radio" name="temperaturescale" value="f" <?php if($dbvars['temperaturescale'] == "f") echo "checked='checked'";?> onChange="autoSubmit();">Degrees F</option> <br>
<br>
			<B>Time Style:</B> <br>
    			<input type="radio" name="timestyle" value="24" <?php if($dbvars['timestyle'] == "24") echo "checked='checked'";?> onChange="autoSubmit();">24 hour</option> <br>
    			<input type="radio" name="timestyle" value="12" <?php if($dbvars['timestyle'] == "12") echo "checked='checked'";?> onChange="autoSubmit();">12 hour</option> <br>
<br>

                       <b>Miscellaneous: </b><br>
                       Web Colour:<br>
                       <select name="webcolour" onchange="if(this.value != 0) { autoSubmit(); }">
                            <option value="green" <?php if($dbvars['webcolour'] == 'green') echo "SELECTED";?>>green</option>
                            <option value="red" <?php if($dbvars['webcolour'] == 'red') echo "SELECTED";?>>red</option>
                            <option value="orange" <?php if($dbvars['webcolour'] == 'orange') echo "SELECTED";?>>orange</option>
                        </select>

			<br>
			Location (woeid): 
			<a href="http://woeid.rosselliot.co.nz/" target="_blank">lookup</a>
			<br>
			<input type="text" name="location"  value = "<?php echo $dbvars['location'] ?>" />
			<br>



<br>



    		
	</div>
        


<?php 

		echo "<div class='col-md-4'" . $colourstyle .  ">"; 
                echo "<h2>Events</h2>";
		echo "<b>Environment Events</b>";
		echo "<br>";

                setdisplaydata($clpdb, $dbvars, "temperaturesensor", "Temperature Message:<br>", "display");

		echo "Temperature Event Trigger Limits:<br>";
		echo "Cold:";
                echo "<input type='text' name='cold' size = '2' style='width:40px;font-size:11pt;' value = ";
		echo $dbvars['cold'];
		echo " />";

		echo "&nbsp";
		echo "Hot:";
                echo "<input type='text' name='hot' size = '2' style='width:40px;font-size:11pt;' value = ";
		echo $dbvars['hot'];
		echo " />";
		echo "<br>";


                echo "<br>"; 
                setdisplaydata($clpdb, $dbvars, "humiditysensor", "Humidity Message:<br>", "display");

		echo "Humidity Event Trigger Limits:<br>";
		echo "Dry:";
                echo "<input type='text' name='dry' size = '2' style='width:40px;font-size:11pt;' value = ";
		echo $dbvars['dry'];
		echo " />";

		echo "&nbsp";
		echo "Humid:";
                echo "<input type='text' name='humid' size = '2' style='width:40px;font-size:11pt;' value = ";
		echo $dbvars['humid'];
		echo " />";
		echo "<br>";

                echo "<br>"; 
                setdisplaydata($clpdb, $dbvars, "lightsensor", "Light Message:<br>", "display");

		echo "Light Event Trigger Limits:<br>";
		echo "Normal:";
                echo "<input type='text' name='normallight' size = '2' style='width:40px;font-size:11pt;' value = ";
		echo $dbvars['dry'];
		echo " />";

		echo "&nbsp";
		echo "Bright:";
                echo "<input type='text' name='brightlight' size = '2' style='width:40px;font-size:11pt;' value = ";
		echo $dbvars['humid'];
		echo " />";
		echo "<br><br>";


                setdisplaydata($clpdb, $dbvars, "motionsensor", "Motion:<br>", "display");

		echo "<br>";
		echo "<b>Speech Synthesis</b>";
		echo "<br>";
		echo "Custom sentence 1 :";
		echo "<br>";
		echo "<input type='text' name='customsentence1'  size = '30' value = '";
		echo $dbvars['customsentence1'];
		echo "'/>";
		echo "<br>";
                echo "Custom Sentence 2: <br>";
                echo "<input type='text' name='customsentence2' size = '30' value = '";
		echo $dbvars['customsentence2'];
		echo "'/>";
		echo "<br><br>";

        echo "</div>";

      echo "<div class='col-md-4'" . $colourstyle .  ">"; ?>
                <h2>Communications</h2>





<?php
function setdaydatahours($dbvars, $name)
{
        echo "<select name='" . $name . "'  onchange='autoSubmit();'>";

        for ($rawvalue=0; $rawvalue<=23; $rawvalue++) {
		if ($rawvalue<10)
			$y = "0" . $rawvalue;
		else
			$y = $rawvalue;  

                echo "<option value='" . $rawvalue . "'";
		if (strcmp($rawvalue, $dbvars[$name])==0)
                        echo "SELECTED";
                echo ">" . $y . "</option>";
        }
        echo "</select>";
}

function setdaydatamins($dbvars, $name)
{
        echo "<select name='" . $name . "'  onchange='autoSubmit();'>";

        for ($rawvalue=0; $rawvalue<=59; $rawvalue++) {
		if ($rawvalue<10)
			$y = "0" . $rawvalue;
		else
			$y = $rawvalue;  

                echo "<option value='" . $rawvalue . "'";
		if (strcmp($rawvalue, $dbvars[$name])==0)
                        echo "SELECTED";
                echo ">" . $y . "</option>";
        }
        echo "</select>";
}


?>

<b>                        Email Addresses</b>
<br>                    From email address: <br>
			<input type="text" name="gmailaddress"  value = "<?php echo $dbvars['gmailaddress'] ?>" />

<br>                    From email password: <br>
			<input type="password" name="gmailpassword"  value = "<?php echo $dbvars['gmailpassword'] ?>" />
<br>
                        Motion notification: <br>
                        <input type="text" name="motionaddress" value = "<?php echo $dbvars['motionaddress'] ?>" />
<br>
                       Alarm not dismissed: <br>
			<input type="text" name="nondismissaddress" value = "<?php echo $dbvars['nondismissaddress'] ?>" />
<br><br>


<b>                        Mosquito Communications</b>
<br>                        
                        
                        Broker address: <br>
			<input type="text" name="mosbrokeraddress"  value = "<?php echo $dbvars['mosbrokeraddress'] ?>" />
<br>
                        Broker port: <br>
			<input type="text" name="mosbrokerport"  value = "<?php echo $dbvars['mosbrokerport'] ?>" />
<br>
                        Username: <br>
			<input type="text" name="mosusername"  value = "<?php echo $dbvars['mosusername'] ?>" />
<br>
                        Password: <br>
			<input type="password" name="mospassword"  value = "<?php echo $dbvars['mospassword'] ?>" />
<br>
                        Topic: <br>
			<input type="text" name="mostopic"  value = "<?php echo $dbvars['mostopic'] ?>" />
<br><br>

<br>

                <input  type="submit" name="submitbutton" value="Save" /><br /> <br>

        </div>
      </div>

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>


</form>
</body>
</html>





